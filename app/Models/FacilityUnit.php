<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FacilityUnit extends Model
{
    public const ZONES = [
        'group_room' => 'Ruang Kelompok',
        'indoor_desk' => 'Zona Meja Indoor',
        'outdoor_desk' => 'Zona Meja Outdoor',
        'reading_chair' => 'Kursi Baca',
    ];

    protected $fillable = [
        'institution_id',
        'code',
        'name',
        'zone',
        'capacity',
        'max_duration_minutes',
        'is_active',
        'sort_order',
        'pos_x',
        'pos_y',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'max_duration_minutes' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'pos_x' => 'integer',
            'pos_y' => 'integer',
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function activeBooking(): HasOne
    {
        return $this->hasOne(Booking::class)
            ->where('status', 'active')
            ->latestOfMany('starts_at');
    }

    public function waitingBookings(): HasMany
    {
        return $this->hasMany(Booking::class)
            ->where('status', 'waiting')
            ->orderBy('starts_at');
    }

    /**
     * Returns 'available' | 'in_used' | 'warning'
     */
    public function currentStatus(): string
    {
        $active = $this->relationLoaded('activeBooking')
            ? $this->activeBooking
            : $this->activeBooking()->first();

        if (! $active) {
            return 'available';
        }

        if ($active->isInPreExpiryWindow()) {
            return 'warning';
        }

        return 'in_used';
    }

    public function zoneLabel(): string
    {
        return self::ZONES[$this->zone] ?? $this->zone;
    }
}
