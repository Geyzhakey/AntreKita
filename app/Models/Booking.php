<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $fillable = [
        'institution_id',
        'facility_unit_id',
        'user_id',
        'group_name',
        'people_count',
        'starts_at',
        'ends_at',
        'status',
        'reminder_sent_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'people_count' => 'integer',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'reminder_sent_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function facilityUnit(): BelongsTo
    {
        return $this->belongsTo(FacilityUnit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function remainingSeconds(): int
    {
        if (! $this->ends_at) {
            return 0;
        }

        return max(0, (int) now()->diffInSeconds($this->ends_at, false));
    }

    public function isInPreExpiryWindow(): bool
    {
        if ($this->status !== 'active' || ! $this->ends_at) {
            return false;
        }

        return $this->remainingSeconds() <= 300;
    }
}
