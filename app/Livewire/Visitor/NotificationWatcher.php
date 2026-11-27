<?php

namespace App\Livewire\Visitor;

use App\Models\NotificationLog;
use App\Services\BookingRuleEngine;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationWatcher extends Component
{
    public bool $open = false;

    public int $lastNotifiedId = 0;

    public function mount(BookingRuleEngine $engine): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $this->lastNotifiedId = (int) (NotificationLog::where('user_id', $user->id)
            ->where('channel', 'account')
            ->max('id') ?? 0);

        $this->pollNotifications($engine);
    }

    public function pollNotifications(BookingRuleEngine $engine): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $engine->processPreExpiryAlerts();

        $newLogs = NotificationLog::where('user_id', $user->id)
            ->where('channel', 'account')
            ->where('id', '>', $this->lastNotifiedId)
            ->orderBy('id')
            ->get();

        foreach ($newLogs as $log) {
            $title = match ($log->trigger_type) {
                'h_minus_5_active' => 'Pengingat H-5 Menit',
                'h_minus_5_waiting' => 'Giliran Dimulai Sebentar Lagi',
                'handover_ready' => 'Meja/Unit Siap Digunakan',
                default => 'Notifikasi AntreKita',
            };

            $this->dispatch('wa-notify', [
                'title' => $title,
                'recipient' => '@' . ($user->username ?: $user->name),
                'message' => $log->message,
            ]);

            $this->lastNotifiedId = max($this->lastNotifiedId, (int) $log->id);
        }
    }

    public function toggleOpen(): void
    {
        $this->open = ! $this->open;

        if ($this->open) {
            $this->markAllAsRead();
        }
    }

    public function markAllAsRead(): void
    {
        $userId = Auth::id();
        if (! $userId) {
            return;
        }

        NotificationLog::where('user_id', $userId)
            ->where('channel', 'account')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function render()
    {
        $userId = Auth::id();

        $accountLogs = $userId
            ? NotificationLog::where('user_id', $userId)
                ->where('channel', 'account')
                ->latest('sent_at')
                ->limit(8)
                ->get()
            : collect();

        $unreadCount = $userId
            ? NotificationLog::where('user_id', $userId)
                ->where('channel', 'account')
                ->whereNull('read_at')
                ->count()
            : 0;

        return view('livewire.visitor.notification-watcher', [
            'accountLogs' => $accountLogs,
            'unreadCount' => $unreadCount,
        ]);
    }
}
