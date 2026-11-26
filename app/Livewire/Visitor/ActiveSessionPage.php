<?php

namespace App\Livewire\Visitor;

use App\Models\Booking;
use App\Models\NotificationLog;
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

    public function mount(BookingRuleEngine $engine): void
    {
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
        ]);
    }
}
