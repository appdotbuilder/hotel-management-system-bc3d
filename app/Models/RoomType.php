<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\RoomType
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $base_price
 * @property int $max_occupancy
 * @property array|null $amenities
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Room> $rooms
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reservation> $reservations
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|RoomType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoomType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoomType query()
 * @method static \Illuminate\Database\Eloquent\Builder|RoomType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoomType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoomType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoomType whereBasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoomType whereMaxOccupancy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoomType whereAmenities($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoomType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoomType whereUpdatedAt($value)
 * @method static \Database\Factories\RoomTypeFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class RoomType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'base_price',
        'max_occupancy',
        'amenities',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'base_price' => 'decimal:2',
        'max_occupancy' => 'integer',
        'amenities' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the rooms for this room type.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Get the reservations for this room type.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}