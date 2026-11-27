<?php

namespace App\Livewire\Visitor;

use App\Models\Booking;
use App\Models\NotificationLog;
use App\Models\SystemSetting;
use App\Services\BookingRuleEngine;
use App\Services\NotificationDispatcher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
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

    public string $wa_pairing_phone = '';

    public ?string $wa_pairing_code = null;

    public string $gatewayError = '';

    public function mount(BookingRuleEngine $engine): void
    {
        $user = Auth::user();
        $this->smtp_username = SystemSetting::getValue('smtp_username') ?: ($user?->email ?? '');
        $this->wa_pairing_phone = $user?->whatsapp_number ?? '';

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
                    'title' => $alert['type'] === 'h_minus_5_active' ? 'Pengingat H-5 Menit' : 'Giliran Dimulai Sebentar Lagi',
                    'recipient' => $alert['recipient'],
                    'message' => $alert['message'],
                ]);
            }
        }

        if (NotificationDispatcher::isWhatsAppConfigured() || NotificationDispatcher::isEmailConfigured()) {
            app(NotificationDispatcher::class)->retryPendingForUser($user);
        }
    }

    public function toggleGatewaySetup(): void
    {
        $this->showGatewaySetup = ! $this->showGatewaySetup;
        $this->gatewayError = '';
    }

    public function requestWaPairingCode(): void
    {
        $this->gatewayError = '';
        $phone = trim($this->wa_pairing_phone ?: (Auth::user()?->whatsapp_number ?? ''));
        if ($phone === '') {
            $this->gatewayError = 'Masukkan nomor WhatsApp terlebih dahulu (contoh: 081222822531).';

            return;
        }

        try {
            $res = Http::timeout(8)->post(NotificationDispatcher::LOCAL_WA_URL . '/pair', [
                'phone' => $phone,
            ]);

            if ($res->successful() && $res->json('ok')) {
                $this->wa_pairing_code = (string) $res->json('pairingCode');
            } else {
                $this->gatewayError = (string) ($res->json('reason') ?? 'Gagal meminta kode tautan.');
            }
        } catch (\Throwable $e) {
            $this->gatewayError = 'Gateway WhatsApp lokal belum merespons: ' . $e->getMessage();
        }
    }

    public function resetWaGateway(): void
    {
        $this->gatewayError = '';
        $this->wa_pairing_code = null;

        try {
            Http::timeout(5)->post(NotificationDispatcher::LOCAL_WA_URL . '/reset');
        } catch (\Throwable $e) {
            $this->gatewayError = 'Gagal mereset sesi WA: ' . $e->getMessage();
        }
    }

    public function saveSmtpInline(NotificationDispatcher $dispatcher): void
    {
        $this->gatewayError = '';

        $validated = $this->validate([
            'smtp_username' => ['required', 'email'],
            'smtp_password' => ['required', 'string', 'min:8'],
        ]);

        $cleanPass = preg_replace('/\s+/', '', trim($validated['smtp_password']));

        SystemSetting::setValue('smtp_host', 'smtp.gmail.com');
        SystemSetting::setValue('smtp_port', '587');
        SystemSetting::setValue('smtp_username', trim($validated['smtp_username']));
        SystemSetting::setValue('smtp_password', $cleanPass);
        SystemSetting::setValue('smtp_from_address', trim($validated['smtp_username']));

        $this->smtp_password = '';

        $user = Auth::user();
        $retried = $user ? $dispatcher->retryPendingForUser($user) : 0;

        $this->dispatch('wa-notify', [
            'title' => 'SMTP Email Aktif',
            'recipient' => trim($validated['smtp_username']),
            'message' => $retried > 0
                ? "SMTP Gmail berhasil disimpan dan {$retried} notifikasi tertunda langsung diproses!"
                : 'SMTP Gmail berhasil disimpan. Email akan terkirim otomatis mulai reservasi berikutnya.',
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
            'title' => $updated->status === 'sent' ? 'Berhasil Dikirim Ulang' : 'Pengiriman Belum Berhasil',
            'recipient' => $updated->recipient,
            'message' => $updated->status === 'sent'
                ? 'Notifikasi berhasil dikirim ke tujuan.'
                : ($updated->error_message ?? 'Periksa koneksi gateway terlebih dahulu.'),
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
            'recipient' => $user->whatsapp_number ?? $user->email,
            'message' => $result['promoted']
                ? "Terima kasih! {$unitCode} langsung dialihkan ke {$result['promoted']->group_name} di Waiting List."
                : "Terima kasih! {$unitCode} kini kembali berstatus [Available].",
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
            ? NotificationLog::where('user_id', $user->id)->latest('sent_at')->limit(8)->get()
            : collect();

        return view('livewire.visitor.active-session-page', [
            'booking' => $booking,
            'activeHolder' => $activeHolder,
            'nextWaiting' => $nextWaiting,
            'logs' => $logs,
            'checklistComplete' => $this->checkDeskClean && $this->checkChairsArranged && $this->checkPersonalItems,
            'waConfigured' => NotificationDispatcher::isWhatsAppConfigured(),
            'emailConfigured' => NotificationDispatcher::isEmailConfigured(),
            'localWa' => NotificationDispatcher::getLocalWaStatus(),
        ]);
    }
}
