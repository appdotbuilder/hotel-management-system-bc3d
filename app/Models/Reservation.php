<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Reservation
 *
 * @property int $id
 * @property string $reservation_number
 * @property int $guest_id
 * @property int|null $room_id
 * @property int $room_type_id
 * @property \Illuminate\Support\Carbon $check_in_date
 * @property \Illuminate\Support\Carbon $check_out_date
 * @property int $adults
 * @property int $children
 * @property string $status
 * @property string $total_amount
 * @property string|null $special_requests
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $checked_in_at
 * @property \Illuminate\Support\Carbon|null $checked_out_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Guest $guest
 * @property-read \App\Models\Room|null $room
 * @property-read \App\Models\RoomType $roomType
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereReservationNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereGuestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereRoomTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereCheckInDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereCheckOutDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereAdults($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereChildren($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereSpecialRequests($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereCheckedInAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereCheckedOutAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation active()
 * @method static \Database\Factories\ReservationFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class Reservation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'reservation_number',
        'guest_id',
        'room_id',
        'room_type_id',
        'check_in_date',
        'check_out_date',
        'adults',
        'children',
        'status',
        'total_amount',
        'special_requests',
        'notes',
        'checked_in_at',
        'checked_out_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'guest_id' => 'integer',
        'room_id' => 'integer',
        'room_type_id' => 'integer',
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'adults' => 'integer',
        'children' => 'integer',
        'total_amount' => 'decimal:2',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the guest that owns the reservation.
     */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    /**
     * Get the room that owns the reservation.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the room type that owns the reservation.
     */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Scope a query to only include active reservations.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['confirmed', 'checked_in']);
    }
}