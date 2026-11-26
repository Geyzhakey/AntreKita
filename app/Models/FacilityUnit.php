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
        'leseh_desk' => 'Meja Leseh (L)',
        'standard_desk' => 'Meja Standar (S)',
        'high_desk' => 'Meja & Kursi Tinggi (H)',
        'focus_desk' => 'Ruang Fokus',
        'sofa_area' => 'Area Sofa',
        'indoor_desk' => 'Zona Meja Indoor',
        'outdoor_desk' => 'Zona Meja Outdoor',
        'reading_chair' => 'Kursi Baca',
    ];

    public const SEAT_TYPES = [
        'L' => 'Meja Leseh',
        'S' => 'Meja Standar',
        'H' => 'Meja & Kursi Tinggi',
        'F' => 'Meja Ruang Fokus',
        'SOFA' => 'Area Sofa',
        'ROOM' => 'Ruangan',
    ];

    protected $fillable = [
        'institution_id',
        'code',
        'name',
        'display_label',
        'zone',
        'floor',
        'seat_type',
        'capacity',
        'max_duration_minutes',
        'is_active',
        'sort_order',
        'pos_x',
        'pos_y',
        'map_x',
        'map_y',
        'map_w',
        'map_h',
    ];

    protected function casts(): array
    {
        return [
            'floor' => 'integer',
            'capacity' => 'integer',
            'max_duration_minutes' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'pos_x' => 'integer',
            'pos_y' => 'integer',
            'map_x' => 'float',
            'map_y' => 'float',
            'map_w' => 'float',
            'map_h' => 'float',
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

    public function seatTypeLabel(): string
    {
        if ($this->seat_type && isset(self::SEAT_TYPES[$this->seat_type])) {
            return self::SEAT_TYPES[$this->seat_type];
        }

        return $this->zoneLabel();
    }

    public function shortLabel(): string
    {
        return $this->display_label ?: $this->code;
    }
}
