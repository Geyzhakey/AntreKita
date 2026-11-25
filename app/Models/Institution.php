<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institution extends Model
{
    protected $fillable = [
        'admin_id',
        'name',
        'slug',
        'registration_code',
        'city',
        'building_type',
        'address',
        'description',
        'purpose',
        'is_active',
        'map_cols',
        'map_rows',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'map_cols' => 'integer',
            'map_rows' => 'integer',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function facilityUnits(): HasMany
    {
        return $this->hasMany(FacilityUnit::class)->orderBy('pos_y')->orderBy('pos_x')->orderBy('sort_order');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
