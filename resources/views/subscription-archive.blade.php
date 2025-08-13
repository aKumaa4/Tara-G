@extends('layouts.app')

@section('title', '')

@section('contents')
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">Archived Membership Plans</h4>
            <a href="{{ route('offers.index') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-arrow-left"></i> Back to Active Plans
            </a>
        </div>

        <div class="row align-items-center mb-3">
            <div class="col-md-5">
                <form method="GET" action="{{ route('offers.archive.index') }}">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search archived plans" 
                               value="{{ request('search') }}">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i>
                        </button>
                        @if(request('search'))
                            <a href="{{ route('offers.archive.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 180px;">Membership Plan</th>
                        <th style="min-width: 250px;">Description</th>
                        <th>Price</th>
                        <th>Duration (Days)</th>
                        <th>Promo?</th>
                        <th>Promo Period</th>
                        <th>Archived Date</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($archivedOffers as $offer)
                        <tr>
                            <td>{{ $offer->name }}</td>
                            <td class="text-truncate" style="max-width: 200px;" title="{{ $offer->description }}">
                                {{ $offer->description }}
                            </td>
                            <td>₱{{ number_format($offer->price, 2) }}</td>
                            <td>{{ $offer->duration_days }}</td>
                            <td>{{ $offer->is_promo ? 'Yes' : 'No' }}</td>
                            <td>
                                @if($offer->is_promo)
                                    {{ $offer->promo_start_date }} - {{ $offer->promo_end_date }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $offer->updated_at->format('M d, Y H:i') }}</td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <form action="{{ route('offers.restore', $offer->id) }}"
                                          method="POST" style="display:inline;">
                                        @csrf
                                        <button class="btn btn-sm btn-success"
                                                onclick="return confirm('Are you sure you want to restore this membership plan?')">
                                            <i class="fas fa-undo"></i> Restore
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-archive fa-2x mb-3"></i>
                                    <p class="mb-0">No archived membership plans found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
