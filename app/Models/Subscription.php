<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $table = 'tbl_gym_subscription';

    protected $fillable = [
        'gym_id',
        'name',
        'description',
        'price',
        'duration_days',
        'is_promo',
        'promo_start_date',
        'promo_end_date',
        'status',
    ];

    protected $casts = [
        'is_promo' => 'boolean',
        'promo_start_date' => 'date',
        'promo_end_date' => 'date',
    ];

    public function gym()
    {
        return $this->belongsTo(GymProfile::class, 'gym_id', 'gym_id');
    }
}
