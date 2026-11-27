<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\FacilityUnit;
use App\Models\NotificationLog;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingRuleEngine
{
    /**
     * Enforce Strict Lock: 1 Email/No. WA = 1 Active or Waiting Booking.
     *
     * @throws ValidationException
     */
    public function assertNoActiveBooking(User $user): void
    {
        $hasActive = Booking::query()
            ->whereIn('status', ['active', 'waiting'])
            ->whereHas('user', function ($query) use ($user) {
                $query->where('id', $user->id)
                    ->orWhere('email', $user->email);

                if ($user->whatsapp_number) {
                    $query->orWhere('whatsapp_number', $user->whatsapp_number);
                }
            })
            ->exists();

        if ($hasActive) {
            throw ValidationException::withMessages([
                'booking' => 'Ditolak: 1 Akun/No. WA hanya boleh memiliki 1 booking aktif atau waiting list.',
            ]);
        }
    }

    /**
     * Create an immediate active booking or queue into waiting list with row-level locking.
     *
     * @throws ValidationException
     */
    public function createBooking(
        User $user,
        FacilityUnit $unit,
        string $groupName,
        int $peopleCount,
        Carbon $startsAt,
        Carbon $endsAt
    ): Booking {
        return DB::transaction(function () use ($user, $unit, $groupName, $peopleCount, $startsAt, $endsAt) {
            if (SystemSetting::getValue('booking_maintenance_mode', '0') === '1') {
                throw ValidationException::withMessages([
                    'booking' => 'Sistem sedang dalam pemeliharaan terjadwal. Booking baru sementara dinonaktifkan.',
                ]);
            }

            $lockedUnit = FacilityUnit::with('institution.admin')->whereKey($unit->id)->lockForUpdate()->firstOrFail();

            if (! $lockedUnit->institution?->is_active || $lockedUnit->institution?->admin?->is_frozen) {
                throw ValidationException::withMessages([
                    'booking' => 'Fasilitas ini sedang dinonaktifkan sementara oleh pengelola sistem.',
                ]);
            }

            if (! $lockedUnit->is_active) {
                throw ValidationException::withMessages([
                    'booking' => 'Unit fasilitas sedang dinonaktifkan oleh administrator.',
                ]);
            }

            $this->assertNoActiveBooking($user);

            if ($peopleCount < 1 || $peopleCount > $lockedUnit->capacity) {
                throw ValidationException::withMessages([
                    'people_count' => "Jumlah pengguna harus antara 1 sampai {$lockedUnit->capacity} orang.",
                ]);
            }

            if ($endsAt->lte($startsAt)) {
                throw ValidationException::withMessages([
                    'ends_at' => 'Waktu selesai harus lebih akhir dari waktu mulai.',
                ]);
            }

            $durationMinutes = $startsAt->diffInMinutes($endsAt);
            if ($durationMinutes > $lockedUnit->max_duration_minutes) {
                throw ValidationException::withMessages([
                    'ends_at' => "Durasi maksimal untuk {$lockedUnit->code} adalah {$lockedUnit->max_duration_minutes} menit.",
                ]);
            }

            $activeBooking = $lockedUnit->activeBooking()->first();
            $lastWaiting = $lockedUnit->waitingBookings()->orderByDesc('ends_at')->first();

            $status = 'active';
            if ($activeBooking) {
                $earliestSlot = $lastWaiting ? $lastWaiting->ends_at : $activeBooking->ends_at;
                if ($startsAt->lt($earliestSlot)) {
                    throw ValidationException::withMessages([
                        'starts_at' => 'Waktu mulai antrean harus setelah pukul ' . $earliestSlot->format('H:i') . '.',
                    ]);
                }
                $status = 'waiting';
            }

            $booking = Booking::create([
                'institution_id' => $lockedUnit->institution_id,
                'facility_unit_id' => $lockedUnit->id,
                'user_id' => $user->id,
                'group_name' => trim($groupName),
                'people_count' => $peopleCount,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'status' => $status,
            ]);

            $timeRange = $startsAt->format('H:i') . ' - ' . $endsAt->format('H:i');
            $greeting = $this->timeOfDayGreeting();
            $username = $this->displayUsername($user);
            $msg = $status === 'active'
                ? "{$greeting}, {$username}! Reservasi {$lockedUnit->name} ({$lockedUnit->code}) dikonfirmasi untuk pukul {$timeRange} WIB. Harap jaga kebersihan dan geser tombol SELESAI saat usai."
                : "{$greeting}, {$username}! Kamu masuk Waiting List {$lockedUnit->name} ({$lockedUnit->code}) untuk slot {$timeRange} WIB. Pengingat otomatis akan dikirim 5 menit sebelum giliran dimulai.";

            $this->logDualChannel($booking, $user, 'booking_confirmed', $msg);

            return $booking;
        });
    }

    /**
     * Detect active bookings within 5 minutes of expiry and send dual WA/Email alerts.
     *
     * @return array<int, array<string, mixed>>
     */
    public function processPreExpiryAlerts(?Carbon $now = null): array
    {
        $now ??= now();
        $threshold = $now->copy()->addMinutes(5);

        $expiringBookings = Booking::with(['user', 'facilityUnit.waitingBookings.user'])
            ->where('status', 'active')
            ->whereNull('reminder_sent_at')
            ->where('ends_at', '<=', $threshold)
            ->get();

        $dispatched = [];

        foreach ($expiringBookings as $booking) {
            DB::transaction(function () use ($booking, $now, &$dispatched) {
                $locked = Booking::whereKey($booking->id)->lockForUpdate()->first();
                if (! $locked || $locked->reminder_sent_at !== null || $locked->status !== 'active') {
                    return;
                }

                $unit = $booking->facilityUnit;
                $activeUser = $booking->user;
                $minutesLeft = max(1, (int) ceil($now->diffInSeconds($locked->ends_at, false) / 60));
                $minutesLabel = min(5, $minutesLeft);
                $greeting = $this->timeOfDayGreeting($now);
                $activeUsername = $this->displayUsername($activeUser);

                $activeMsg = "{$greeting}, {$activeUsername}! Masa penggunaan {$unit->name} ({$unit->code}) yang kamu gunakan akan berakhir dalam {$minutesLabel} menit (pukul {$locked->ends_at->format('H:i')} WIB). Mohon untuk mempersiapkan diri, merapikan meja/kursi, memeriksa barang bawaan, dan menggeser tombol SELESAI saat waktu usai.";
                $this->logDualChannel($locked, $activeUser, 'h_minus_5_active', $activeMsg);

                $dispatched[] = [
                    'type' => 'h_minus_5_active',
                    'user_id' => $activeUser->id,
                    'recipient' => $activeUser->whatsapp_number ?? $activeUser->email,
                    'unit_code' => $unit->code,
                    'message' => $activeMsg,
                ];

                $nextWaiting = $unit->waitingBookings()->with('user')->first();
                if ($nextWaiting && $nextWaiting->user) {
                    $waitingUsername = $this->displayUsername($nextWaiting->user);
                    $waitingMsg = "{$greeting}, {$waitingUsername}! Bersiap ya, giliran kamu untuk menggunakan {$unit->name} ({$unit->code}) akan dimulai dalam {$minutesLabel} menit.";
                    $this->logDualChannel($nextWaiting, $nextWaiting->user, 'h_minus_5_waiting', $waitingMsg);

                    $dispatched[] = [
                        'type' => 'h_minus_5_waiting',
                        'user_id' => $nextWaiting->user->id,
                        'recipient' => $nextWaiting->user->whatsapp_number ?? $nextWaiting->user->email,
                        'unit_code' => $unit->code,
                        'message' => $waitingMsg,
                    ];
                }

                $locked->update(['reminder_sent_at' => $now]);
            });
        }

        return $dispatched;
    }

    /**
     * Complete active session via Slide-to-Finish and cascade waiting list User 02 -> Active.
     *
     * @return array{promoted: ?Booking, unit_status: string}
     */
    public function slideToFinish(Booking $booking, User $actor): array
    {
        return DB::transaction(function () use ($booking, $actor) {
            $locked = Booking::with('facilityUnit')->whereKey($booking->id)->lockForUpdate()->firstOrFail();

            if ($locked->user_id !== $actor->id && ! $actor->isAdmin()) {
                throw ValidationException::withMessages([
                    'booking' => 'Anda tidak memiliki izin untuk mengakhiri sesi ini.',
                ]);
            }

            if (! in_array($locked->status, ['active', 'waiting'], true)) {
                return [
                    'promoted' => null,
                    'unit_status' => $locked->facilityUnit->currentStatus(),
                ];
            }

            $wasActive = $locked->status === 'active';
            $now = now();

            $locked->update([
                'status' => 'completed',
                'finished_at' => $now,
            ]);

            $promoted = null;
            if ($wasActive) {
                $next = Booking::with('user')
                    ->where('facility_unit_id', $locked->facility_unit_id)
                    ->where('status', 'waiting')
                    ->orderBy('starts_at')
                    ->lockForUpdate()
                    ->first();

                if ($next) {
                    $duration = max(15, $next->starts_at->diffInMinutes($next->ends_at));
                    $newStart = $now;
                    $newEnd = $next->ends_at->gt($now->copy()->addMinutes(5))
                        ? $next->ends_at
                        : $now->copy()->addMinutes($duration);

                    $next->update([
                        'status' => 'active',
                        'starts_at' => $newStart,
                        'ends_at' => $newEnd,
                    ]);

                    $unitCode = $locked->facilityUnit->code;
                    $greeting = $this->timeOfDayGreeting($now);
                    $nextUsername = $this->displayUsername($next->user);
                    $handoverMsg = "{$greeting}, {$nextUsername}! {$locked->facilityUnit->name} ({$unitCode}) sudah siap dan bersih. Giliran kamu sekarang aktif hingga pukul {$newEnd->format('H:i')} WIB.";
                    $this->logDualChannel($next, $next->user, 'handover_ready', $handoverMsg);

                    $promoted = $next->fresh();
                }
            }

            return [
                'promoted' => $promoted,
                'unit_status' => $promoted ? 'in_used' : 'available',
            ];
        });
    }

    protected function timeOfDayGreeting(?Carbon $now = null): string
    {
        $hour = ($now ?? now())->hour;

        if ($hour >= 4 && $hour <= 10) {
            return 'Selamat pagi';
        }

        if ($hour >= 11 && $hour <= 14) {
            return 'Selamat siang';
        }

        if ($hour >= 15 && $hour <= 17) {
            return 'Selamat sore';
        }

        return 'Selamat malam';
    }

    protected function displayUsername(User $user): string
    {
        return trim((string) ($user->username ?: $user->name));
    }

    protected function logDualChannel(Booking $booking, User $user, string $triggerType, string $message): void
    {
        app(NotificationDispatcher::class)->dispatchDualChannel($booking, $user, $triggerType, $message);
    }
}
