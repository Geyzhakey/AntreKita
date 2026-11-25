<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'affiliation',
        'email',
        'whatsapp_number',
        'role',
        'registration_code',
        'institution_label',
        'purpose',
        'is_verified_admin',
        'is_frozen',
        'frozen_reason',
        'frozen_at',
        'password',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_verified_admin' => 'boolean',
            'is_frozen' => 'boolean',
            'frozen_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isDeveloper(): bool
    {
        return $this->role === 'developer' && ! $this->is_frozen;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' && $this->is_verified_admin && ! $this->is_frozen;
    }

    public function isVisitor(): bool
    {
        return $this->role === 'visitor';
    }

    public function institution(): HasOne
    {
        return $this->hasOne(Institution::class, 'admin_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function activeBooking(): HasOne
    {
        return $this->hasOne(Booking::class)
            ->whereIn('status', ['active', 'waiting'])
            ->latestOfMany();
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    public static function normalizeWhatsapp(string $phone): string
    {
        $digits = preg_replace('/[^\d+]/', '', trim($phone)) ?? '';

        if (str_starts_with($digits, '0')) {
            $digits = '+62' . substr($digits, 1);
        } elseif (str_starts_with($digits, '62')) {
            $digits = '+' . $digits;
        } elseif (! str_starts_with($digits, '+') && $digits !== '') {
            $digits = '+62' . $digits;
        }

        return $digits;
    }
}
