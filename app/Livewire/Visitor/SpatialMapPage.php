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

    public int $activeFloor = 1;

    public string $seatTypeFilter = 'all'; // all | L | S | H | F | SOFA | ROOM

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

        $firstFloor = $institution->facilityUnits()
            ->where('is_active', true)
            ->min('floor');
        $this->activeFloor = $firstFloor ? (int) $firstFloor : 1;
    }

    public function setFloor(int $floor): void
    {
        $this->activeFloor = $floor;
    }

    public function setSeatTypeFilter(string $seatType): void
    {
        $this->seatTypeFilter = $seatType;
    }

    public function selectUnit(int $unitId): void
    {
        $this->bookingError = '';
        $this->resetValidation();

        $unit = FacilityUnit::with(['activeBooking', 'waitingBookings'])
            ->where('institution_id', $this->institution->id)
            ->findOrFail($unitId);

        $this->selectedUnitId = $unit->id;
        $this->activeFloor = $unit->floor ?? 1;
        $this->people_count = ($unit->zone === 'group_room' || $unit->seat_type === 'ROOM')
            ? min(3, $unit->capacity)
            : 1;

        $active = $unit->activeBooking;
        $lastWaiting = $unit->waitingBookings->sortByDesc('ends_at')->first();

        $suggestedStart = $lastWaiting
            ? $lastWaiting->ends_at->copy()
            : ($active ? $active->ends_at->copy() : now()->addMinute());

        $suggestedEnd = $suggestedStart->copy()->addMinutes(min(90, $unit->max_duration_minutes));

        $this->start_time = $suggestedStart->format('H:i');
        $this->end_time = $suggestedEnd->format('H:i');
    }

    public function clearSelection(): void
    {
        $this->selectedUnitId = null;
        $this->bookingError = '';
        $this->resetValidation();
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

        $availableFloors = $allUnits->pluck('floor')->unique()->sort()->values();
        if ($availableFloors->isNotEmpty() && ! $availableFloors->contains($this->activeFloor)) {
            $this->activeFloor = (int) $availableFloors->first();
        }

        $floorStats = [];
        foreach ($availableFloors as $floor) {
            $unitsOnFloor = $allUnits->where('floor', $floor);
            $occupied = $unitsOnFloor->filter(fn (FacilityUnit $u) => $u->activeBooking !== null)->count();
            $floorStats[$floor] = [
                'total' => $unitsOnFloor->count(),
                'occupied' => $occupied,
                'available' => max(0, $unitsOnFloor->count() - $occupied),
            ];
        }

        $floorUnits = $allUnits->where('floor', $this->activeFloor)->values();

        $matchingUnits = $floorUnits->filter(function (FacilityUnit $unit) {
            if ($this->seatTypeFilter !== 'all' && $unit->seat_type !== $this->seatTypeFilter) {
                return false;
            }
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

        $matchingUnitIds = $matchingUnits->pluck('id')->all();
        $gridMap = $matchingUnits->keyBy(fn (FacilityUnit $u) => "{$u->pos_x},{$u->pos_y}");
        $maxRow = max($this->institution->map_rows ?? 4, (int) ($floorUnits->max('pos_y') ?? 4));
        $maxCol = max($this->institution->map_cols ?? 6, (int) ($floorUnits->max('pos_x') ?? 6));

        $selectedUnit = $this->selectedUnitId
            ? FacilityUnit::with(['activeBooking.user', 'waitingBookings.user'])->find($this->selectedUnitId)
            : null;

        return view('livewire.visitor.spatial-map-page', [
            'unitsByZone' => $matchingUnits->groupBy('zone'),
            'floorUnits' => $floorUnits,
            'matchingUnitIds' => $matchingUnitIds,
            'availableFloors' => $availableFloors,
            'floorStats' => $floorStats,
            'gridMap' => $gridMap,
            'maxCol' => $maxCol,
            'maxRow' => $maxRow,
            'zones' => FacilityUnit::ZONES,
            'seatTypes' => FacilityUnit::SEAT_TYPES,
            'selectedUnit' => $selectedUnit,
            'userActiveBooking' => $userActiveBooking,
        ]);
    }
}
