<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomBooking extends Model
{
    protected $fillable = [
        'booking_number',
        'room_id',
        'user_id',
        'customer_name',
        'customer_phone',
        'status',
        'base_minutes',
        'base_price',
        'room_charge',
        'food_total',
        'discount_amount',
        'total',
        'payment_method',
        'amount_paid',
        'change_amount',
        'started_at',
        'expires_at',
        'checked_out_at',
        'notes',
    ];

    protected $casts = [
        'base_minutes' => 'integer',
        'base_price' => 'decimal:2',
        'room_charge' => 'decimal:2',
        'food_total' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The single "master order" that carries both the room-time line items
     * (base + extensions) and the QR food/drink items for this booking.
     */
    public function order()
    {
        return $this->hasOne(Order::class, 'room_booking_id');
    }

    /**
     * Seconds left on the countdown. Negative once the booking has run over time.
     */
    public function remainingSeconds(): int
    {
        if (!$this->expires_at) {
            return 0;
        }

        return (int) now()->diffInSeconds($this->expires_at, false);
    }
}
