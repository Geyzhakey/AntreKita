<?php

namespace App\Livewire\Visitor;

use App\Models\FacilityUnit;
use App\Models\Institution;
use App\Services\BookingRuleEngine;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Denah Spasial 2D Fasilitas — AntreKita'])]
class SpatialMapPage extends Component
{
    public Institution $institution;

    public string $viewMode = 'map'; // map | list

    public string $zoneFilter = 'all';

    public string $statusFilter = 'all';

    public ?int $selectedUnitId = null;

    public string $group_name = '';

    public int $people_count = 1;

    public string $start_time = '';

    public string $end_time = '';

    public string $bookingError = '';

    public function mount(Institution $institution): void
    {
        $this->institution = $institution;
        $this->group_name = Auth::user()?->name ?? '';
    }

    public function selectUnit(int $unitId): void
    {
        $this->bookingError = '';
        $this->resetValidation();

        $unit = FacilityUnit::with(['activeBooking', 'waitingBookings'])
            ->where('institution_id', $this->institution->id)
            ->findOrFail($unitId);

        $this->selectedUnitId = $unit->id;
        $this->people_count = $unit->zone === 'group_room' ? min(3, $unit->capacity) : 1;

        $active = $unit->activeBooking;
        $lastWaiting = $unit->waitingBookings->sortByDesc('ends_at')->first();

        $suggestedStart = $lastWaiting
            ? $lastWaiting->ends_at->copy()
            : ($active ? $active->ends_at->copy() : now()->addMinute());

        $suggestedEnd = $suggestedStart->copy()->addMinutes(min(90, $unit->max_duration_minutes));

        $this->start_time = $suggestedStart->format('H:i');
        $this->end_time = $suggestedEnd->format('H:i');
    }

    public function submitBooking(BookingRuleEngine $engine): void
    {
        $this->bookingError = '';
        $user = Auth::user();
        if (! $user) {
            $this->redirect(route('login'), navigate: true);

            return;
        }

        if (! $this->selectedUnitId) {
            return;
        }

        $this->validate([
            'group_name' => ['required', 'string', 'max:120'],
            'people_count' => ['required', 'integer', 'min:1'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
        ]);

        $unit = FacilityUnit::where('institution_id', $this->institution->id)->findOrFail($this->selectedUnitId);

        $today = now()->format('Y-m-d');
        $startsAt = Carbon::createFromFormat('Y-m-d H:i', "{$today} {$this->start_time}");
        $endsAt = Carbon::createFromFormat('Y-m-d H:i', "{$today} {$this->end_time}");

        if ($endsAt->lte($startsAt) && $endsAt->copy()->addDay()->diffInMinutes($startsAt) <= $unit->max_duration_minutes) {
            $endsAt->addDay();
        }

        try {
            $booking = $engine->createBooking(
                $user,
                $unit,
                $this->group_name,
                $this->people_count,
                $startsAt,
                $endsAt
            );

            $this->dispatch('wa-notify', [
                'title' => $booking->status === 'active' ? 'Booking Aktif Dikonfirmasi' : 'Masuk Waiting List',
                'recipient' => $user->whatsapp_number ?? $user->email,
                'message' => $booking->status === 'active'
                    ? "{$unit->code} aktif pukul {$startsAt->format('H:i')} - {$endsAt->format('H:i')}."
                    : "Anda masuk antrean {$unit->code} mulai pukul {$startsAt->format('H:i')}.",
            ]);

            $this->redirect(route('visitor.session'), navigate: true);
        } catch (ValidationException $e) {
            $firstMessage = collect($e->errors())->flatten()->first() ?? 'Gagal memproses booking.';
            $this->bookingError = $firstMessage;
        }
    }

    public function render()
    {
        $user = Auth::user();
        $userActiveBooking = $user?->activeBooking()->with('facilityUnit')->first();

        $allUnits = $this->institution->facilityUnits()
            ->with(['activeBooking.user', 'waitingBookings.user'])
            ->where('is_active', true)
            ->get();

        $filteredUnits = $allUnits->filter(function (FacilityUnit $unit) {
            if ($this->zoneFilter !== 'all' && $unit->zone !== $this->zoneFilter) {
                return false;
            }
            if ($this->statusFilter === 'available' && $unit->activeBooking !== null) {
                return false;
            }
            if ($this->statusFilter === 'in_used' && $unit->activeBooking === null) {
                return false;
            }

            return true;
        });

        $gridMap = $filteredUnits->keyBy(fn (FacilityUnit $u) => "{$u->pos_x},{$u->pos_y}");
        $maxRow = max($this->institution->map_rows ?? 4, (int) ($allUnits->max('pos_y') ?? 4));
        $maxCol = max($this->institution->map_cols ?? 6, (int) ($allUnits->max('pos_x') ?? 6));

        $selectedUnit = $this->selectedUnitId
            ? FacilityUnit::with(['activeBooking.user', 'waitingBookings.user'])->find($this->selectedUnitId)
            : null;

        return view('livewire.visitor.spatial-map-page', [
            'unitsByZone' => $filteredUnits->groupBy('zone'),
            'gridMap' => $gridMap,
            'maxCol' => $maxCol,
            'maxRow' => $maxRow,
            'zones' => FacilityUnit::ZONES,
            'selectedUnit' => $selectedUnit,
            'userActiveBooking' => $userActiveBooking,
        ]);
    }
}
