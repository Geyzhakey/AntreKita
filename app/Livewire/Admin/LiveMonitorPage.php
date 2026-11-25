<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use App\Models\FacilityUnit;
use App\Models\Institution;
use App\Services\BookingRuleEngine;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Live Monitoring — Admin AntreKita'])]
class LiveMonitorPage extends Component
{
    public ?Institution $institution = null;

    public function mount(): void
    {
        $user = Auth::user();
        abort_unless($user && $user->isAdmin(), 403);

        $this->institution = $user->institution;
        if (! $this->institution) {
            $this->redirect(route('admin.setup'), navigate: true);
        }
    }

    public function forceFinish(int $bookingId, BookingRuleEngine $engine): void
    {
        abort_unless(Auth::user()?->isAdmin() && $this->institution, 403);

        $booking = Booking::where('institution_id', $this->institution->id)->findOrFail($bookingId);
        $result = $engine->slideToFinish($booking, Auth::user());

        $this->dispatch('wa-notify', [
            'title' => 'Sesi Diakhiri Admin',
            'message' => $result['promoted']
                ? "Unit {$booking->facilityUnit->code} langsung dialihkan ke Waiting List ({$result['promoted']->group_name})."
                : "Unit {$booking->facilityUnit->code} kini berstatus [Available].",
        ]);
    }

    public function render()
    {
        $units = $this->institution
            ? $this->institution->facilityUnits()
                ->with(['activeBooking.user', 'waitingBookings.user'])
                ->where('is_active', true)
                ->get()
            : collect();

        $activeCount = $units->filter(fn (FacilityUnit $u) => $u->activeBooking !== null)->count();
        $availableCount = $units->count() - $activeCount;
        $waitingTotal = $units->sum(fn (FacilityUnit $u) => $u->waitingBookings->count());

        $activeBookings = $this->institution
            ? Booking::with(['facilityUnit', 'user'])
                ->where('institution_id', $this->institution->id)
                ->whereIn('status', ['active', 'waiting'])
                ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
                ->orderBy('starts_at')
                ->get()
            : collect();

        return view('livewire.admin.live-monitor-page', [
            'unitsByZone' => $units->groupBy('zone'),
            'zones' => FacilityUnit::ZONES,
            'totalUnits' => $units->count(),
            'availableCount' => $availableCount,
            'activeCount' => $activeCount,
            'waitingTotal' => $waitingTotal,
            'activeBookings' => $activeBookings,
        ]);
    }
}
