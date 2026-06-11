<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'room_number',
        'name',
        'capacity',
        'base_price',
        'status',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function bookings()
    {
        return $this->hasMany(RoomBooking::class);
    }

    public function activeBooking()
    {
        return $this->hasOne(RoomBooking::class)
            ->where('status', 'active')
            ->latest();
    }
}
