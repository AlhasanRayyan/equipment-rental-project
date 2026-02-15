<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'equipment_id',
        'renter_id',
        'owner_id',
        'start_date',
        'end_date',
        'rental_duration_days',
        'rental_rate_type',
        'total_cost',
        'deposit_amount_paid',
        'payment_status',
        'booking_status',
        'pickup_location',
        'return_location',
        'special_requirements'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'confirmed_at' => 'datetime',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
    public function renter()
    {
        return $this->belongsTo(User::class, 'renter_id');
    }
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
