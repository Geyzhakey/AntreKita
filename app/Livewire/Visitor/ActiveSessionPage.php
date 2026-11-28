<?php

namespace App\Livewire\Visitor;

use App\Models\Booking;
use App\Models\NotificationLog;
use App\Models\SystemSetting;
use App\Services\BookingRuleEngine;
use App\Services\NotificationDispatcher;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Sesi Aktif & Slide to Finish — AntreKita'])]
class ActiveSessionPage extends Component
{
    public bool $checkDeskClean = false;

    public bool $checkChairsArranged = false;

    public bool $checkPersonalItems = false;

    public bool $showGatewaySetup = false;

    public string $smtp_username = '';

    public string $smtp_password = '';

    public string $gatewayError = '';

    public function mount(BookingRuleEngine $engine): void
    {
        $this->smtp_username = SystemSetting::getValue('smtp_username') ?: (string) config('mail.mailers.smtp.username', 'antrekita@gmail.com');
        $this->checkRealtimeStatus($engine);
    }

    public function checkRealtimeStatus(BookingRuleEngine $engine): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $alerts = $engine->processPreExpiryAlerts();

        foreach ($alerts as $alert) {
            if ((int) ($alert['user_id'] ?? 0) === (int) $user->id) {
                $this->dispatch('wa-notify', [
                    'title' => $alert['type'] === 'h_minus_5_active' ? 'Pengingat 5 Menit Terakhir' : 'Giliran Dimulai Sebentar Lagi',
                    'recipient' => $user->email,
                    'message' => $alert['message'],
                ]);
            }
        }

        if (NotificationDispatcher::isEmailConfigured()) {
            app(NotificationDispatcher::class)->retryPendingForUser($user);
        }
    }

    public function toggleGatewaySetup(): void
    {
        $this->showGatewaySetup = ! $this->showGatewaySetup;
        $this->gatewayError = '';
    }

    public function saveSmtpInline(NotificationDispatcher $dispatcher): void
    {
        $this->gatewayError = '';

        $validated = $this->validate([
            'smtp_username' => ['required', 'email'],
            'smtp_password' => ['required', 'string', 'min:8'],
        ]);

        $cleanPass = trim($validated['smtp_password']);

        SystemSetting::setValue('smtp_host', 'smtp.gmail.com');
        SystemSetting::setValue('smtp_port', '587');
        SystemSetting::setValue('smtp_username', trim($validated['smtp_username']));
        SystemSetting::setValue('smtp_password', $cleanPass);
        SystemSetting::setValue('smtp_from_address', trim($validated['smtp_username']));

        $this->smtp_password = '';

        $user = Auth::user();
        $retried = $user ? $dispatcher->retryPendingForUser($user) : 0;

        $this->dispatch('wa-notify', [
            'title' => 'Konfigurasi Email Disimpan',
            'recipient' => trim($validated['smtp_username']),
            'message' => $retried > 0
                ? "SMTP Gmail diperbarui dan {$retried} email tertunda langsung diproses!"
                : 'Konfigurasi email pengirim AntreKita berhasil diperbarui.',
        ]);
    }

    public function retryNotification(int $logId, NotificationDispatcher $dispatcher): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $log = NotificationLog::where('user_id', $user->id)->find($logId);
        if (! $log) {
            return;
        }

        $updated = $dispatcher->retryLog($log);

        $this->dispatch('wa-notify', [
            'title' => $updated->status === 'sent' ? 'Email Terkirim Ulang' : 'Pengiriman Email Gagal',
            'recipient' => $updated->recipient,
            'message' => $updated->status === 'sent'
                ? 'Pesan email berhasil dikirimkan ke kotak masuk Anda.'
                : ($updated->error_message ?? 'Periksa konfigurasi SMTP pengirim terlebih dahulu.'),
        ]);
    }

    public function finishSession(BookingRuleEngine $engine): void
    {
        $booking = $this->currentBooking();
        $user = Auth::user();

        if (! $booking || ! $user) {
            return;
        }

        $unitCode = $booking->facilityUnit->code;
        $result = $engine->slideToFinish($booking, $user);

        $this->dispatch('wa-notify', [
            'title' => 'Sesi Selesai (Slide to Finish)',
            'recipient' => $user->email,
            'message' => $result['promoted']
                ? "Terima kasih! {$unitCode} langsung dialihkan ke {$result['promoted']->group_name} dan ringkasan dikirim ke email Anda."
                : "Terima kasih! {$unitCode} kini kembali berstatus [Available] dan ringkasan dikirim ke email Anda.",
        ]);

        $this->reset(['checkDeskClean', 'checkChairsArranged', 'checkPersonalItems']);
    }

    protected function currentBooking(): ?Booking
    {
        return Auth::user()
            ?->activeBooking()
            ->with(['facilityUnit', 'institution'])
            ->first();
    }

    public function render()
    {
        $user = Auth::user();
        $booking = $this->currentBooking();

        $activeHolder = $booking && $booking->status === 'waiting'
            ? $booking->facilityUnit->activeBooking()->with('user')->first()
            : null;

        $nextWaiting = $booking && $booking->status === 'active'
            ? $booking->facilityUnit->waitingBookings()->with('user')->first()
            : null;

        $logs = $user
            ? NotificationLog::where('user_id', $user->id)
                ->whereIn('channel', ['email', 'account'])
                ->latest('sent_at')
                ->limit(8)
                ->get()
            : collect();

        return view('livewire.visitor.active-session-page', [
            'booking' => $booking,
            'activeHolder' => $activeHolder,
            'nextWaiting' => $nextWaiting,
            'logs' => $logs,
            'checklistComplete' => $this->checkDeskClean && $this->checkChairsArranged && $this->checkPersonalItems,
            'emailConfigured' => NotificationDispatcher::isEmailConfigured(),
        ]);
    }
}
