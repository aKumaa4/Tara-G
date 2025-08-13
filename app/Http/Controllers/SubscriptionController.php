<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    /**
     * Display the list of membership plans for the logged-in gym.
     */
    public function index(Request $request)
    {
        // Get the gym_id based on the logged-in user (owner_id)
        $gymId = DB::table('tbl_gym_information')
            ->where('owner_id', Auth::id())
            ->value('gym_id');

        if (!$gymId) {
            return back()->withErrors('You do not have a gym registered.');
        }

        $query = Subscription::where('gym_id', $gymId)
            ->whereIn('status', ['active', 'inactive']);

        // Apply search filter if search term is provided
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('price', 'LIKE', "%{$searchTerm}%");
            });
        }

        $offers = $query->orderBy('created_at', 'desc')->get();

        return view('subscription-offers', compact('offers'));
    }

    /**
     * Store a newly created membership plan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'price'             => 'required|numeric|min:0',
            'duration_days'     => 'required|integer|min:1',
            'is_promo'          => 'required|boolean',
            'promo_start_date'  => 'nullable|date',
            'promo_end_date'    => 'nullable|date|after_or_equal:promo_start_date',
            'status'            => 'required|in:active,inactive,archived',
        ]);

        // Get the gym_id based on the logged-in user (owner_id)
        $gymId = DB::table('tbl_gym_information')
            ->where('owner_id', Auth::id())
            ->value('gym_id');

        if (!$gymId) {
            return back()->withErrors('You do not have a gym registered.');
        }

        Subscription::create([
            'gym_id'            => $gymId,
            'name'              => $request->name,
            'description'       => $request->description,
            'price'             => $request->price,
            'duration_days'     => $request->duration_days,
            'is_promo'          => $request->is_promo,
            'promo_start_date'  => $request->is_promo ? $request->promo_start_date : null,
            'promo_end_date'    => $request->is_promo ? $request->promo_end_date : null,
            'status'            => $request->status,
        ]);

        return redirect()->route('offers.index')->with('success', 'Membership plan added successfully!');
    }

    /**
     * Show the edit form for a membership plan.
     */
    public function edit($id)
    {
        // Get the gym_id based on the logged-in user (owner_id)
        $gymId = DB::table('tbl_gym_information')
            ->where('owner_id', Auth::id())
            ->value('gym_id');

        if (!$gymId) {
            return back()->withErrors('You do not have a gym registered.');
        }

        $offer = Subscription::where('gym_id', $gymId)->findOrFail($id);

        return view('subscription.edit', compact('offer'));
    }

    /**
     * Update a membership plan.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'price'             => 'required|numeric|min:0',
            'duration_days'     => 'required|integer|min:1',
            'is_promo'          => 'required|boolean',
            'promo_start_date'  => 'nullable|date',
            'promo_end_date'    => 'nullable|date|after_or_equal:promo_start_date',
            'status'            => 'required|in:active,inactive,archived',
        ]);

        // Get the gym_id based on the logged-in user (owner_id)
        $gymId = DB::table('tbl_gym_information')
            ->where('owner_id', Auth::id())
            ->value('gym_id');

        if (!$gymId) {
            return back()->withErrors('You do not have a gym registered.');
        }

        $offer = Subscription::where('gym_id', $gymId)->findOrFail($id);

        $offer->update([
            'name'              => $request->name,
            'description'       => $request->description,
            'price'             => $request->price,
            'duration_days'     => $request->duration_days,
            'is_promo'          => $request->is_promo,
            'promo_start_date'  => $request->is_promo ? $request->promo_start_date : null,
            'promo_end_date'    => $request->is_promo ? $request->promo_end_date : null,
            'status'            => $request->status,
        ]);

        return redirect()->route('offers.index')->with('success', 'Membership plan updated successfully!');
    }

    /**
     * Archive a membership plan.
     */
    public function archive($id)
    {
        // Get the gym_id based on the logged-in user (owner_id)
        $gymId = DB::table('tbl_gym_information')
            ->where('owner_id', Auth::id())
            ->value('gym_id');

        if (!$gymId) {
            return back()->withErrors('You do not have a gym registered.');
        }

        $offer = Subscription::where('gym_id', $gymId)->findOrFail($id);

        $offer->update(['status' => 'archived']);

        return redirect()->route('offers.index')->with('success', 'Membership plan archived successfully!');
    }

    /**
     * Display the list of archived membership plans.
     */
    public function archiveIndex(Request $request)
    {
        // Get the gym_id based on the logged-in user (owner_id)
        $gymId = DB::table('tbl_gym_information')
            ->where('owner_id', Auth::id())
            ->value('gym_id');

        if (!$gymId) {
            return back()->withErrors('You do not have a gym registered.');
        }

        $query = Subscription::where('gym_id', $gymId)
            ->where('status', 'archived');

        // Apply search filter if search term is provided
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('price', 'LIKE', "%{$searchTerm}%");
            });
        }

        $archivedOffers = $query->orderBy('created_at', 'desc')->get();

        return view('subscription-archive', compact('archivedOffers'));
    }

    /**
     * Restore an archived membership plan.
     */
    public function restore($id)
    {
        // Get the gym_id based on the logged-in user (owner_id)
        $gymId = DB::table('tbl_gym_information')
            ->where('owner_id', Auth::id())
            ->value('gym_id');

        if (!$gymId) {
            return back()->withErrors('You do not have a gym registered.');
        }

        $offer = Subscription::where('gym_id', $gymId)->findOrFail($id);

        $offer->update(['status' => 'active']);

        return redirect()->route('offers.archive.index')->with('success', 'Membership plan restored successfully!');
    }
}
