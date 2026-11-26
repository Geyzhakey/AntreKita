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

    public string $floorFilter = 'all';

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
        $allUnits = $this->institution
            ? $this->institution->facilityUnits()
                ->with(['activeBooking.user', 'waitingBookings.user'])
                ->where('is_active', true)
                ->get()
            : collect();

        $floors = $allUnits->pluck('floor')->unique()->sort()->values();

        $units = $this->floorFilter === 'all'
            ? $allUnits
            : $allUnits->where('floor', (int) $this->floorFilter)->values();

        $activeCount = $allUnits->filter(fn (FacilityUnit $u) => $u->activeBooking !== null)->count();
        $availableCount = $allUnits->count() - $activeCount;
        $waitingTotal = $allUnits->sum(fn (FacilityUnit $u) => $u->waitingBookings->count());

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
            'floors' => $floors,
            'totalUnits' => $allUnits->count(),
            'availableCount' => $availableCount,
            'activeCount' => $activeCount,
            'waitingTotal' => $waitingTotal,
            'activeBookings' => $activeBookings,
        ]);
    }
}
