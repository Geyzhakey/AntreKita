<?php

namespace App\Livewire\Admin;

use App\Models\FacilityUnit;
use App\Models\Institution;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Setup Peta Lokasi & Unit — Admin AntreKita'])]
class FacilitySetupPage extends Component
{
    public ?Institution $institution = null;

    public string $inst_name = '';

    public string $inst_city = '';

    public string $inst_building_type = 'Perpustakaan Kampus';

    public string $inst_address = '';

    public string $inst_description = '';

    public int $map_cols = 6;

    public int $map_rows = 4;

    public string $floorFilter = 'all';

    // Single Unit CRUD
    public ?int $editingUnitId = null;

    public string $code = '';

    public string $name = '';

    public string $zone = 'standard_desk';

    public int $floor = 1;

    public string $seat_type = 'S';

    public int $capacity = 1;

    public int $max_duration_minutes = 180;

    public int $pos_x = 1;

    public int $pos_y = 1;

    // Batch Generator
    public int $batch_group_rooms = 0;

    public int $batch_indoor_desks = 0;

    public int $batch_outdoor_desks = 0;

    public int $batch_reading_chairs = 0;

    public function mount(): void
    {
        $user = Auth::user();
        abort_unless($user && $user->isAdmin(), 403);

        $this->institution = $user->institution;
        if ($this->institution) {
            $this->inst_name = $this->institution->name;
            $this->inst_city = $this->institution->city;
            $this->inst_building_type = $this->institution->building_type ?? 'Perpustakaan Kampus';
            $this->inst_address = $this->institution->address;
            $this->inst_description = $this->institution->description ?? '';
            $this->map_cols = $this->institution->map_cols ?? 6;
            $this->map_rows = $this->institution->map_rows ?? 4;
        }
    }

    public function saveInstitution(): void
    {
        $user = Auth::user();
        abort_unless($user && $user->isAdmin(), 403);

        $this->validate([
            'inst_name' => ['required', 'string', 'max:255'],
            'inst_city' => ['required', 'string', 'max:120'],
            'inst_building_type' => ['required', 'string', 'max:80'],
            'inst_address' => ['required', 'string', 'max:255'],
            'inst_description' => ['nullable', 'string', 'max:1000'],
            'map_cols' => ['required', 'integer', 'min:3', 'max:12'],
            'map_rows' => ['required', 'integer', 'min:2', 'max:12'],
        ]);

        if ($this->institution) {
            $this->institution->update([
                'name' => trim($this->inst_name),
                'city' => trim($this->inst_city),
                'building_type' => trim($this->inst_building_type),
                'address' => trim($this->inst_address),
                'description' => trim($this->inst_description) ?: null,
                'map_cols' => $this->map_cols,
                'map_rows' => $this->map_rows,
            ]);
        } else {
            $baseSlug = Str::slug($this->inst_name);
            $slug = Institution::where('slug', $baseSlug)->exists()
                ? $baseSlug . '-' . $user->id
                : $baseSlug;

            $this->institution = Institution::create([
                'admin_id' => $user->id,
                'name' => trim($this->inst_name),
                'slug' => $slug,
                'registration_code' => $user->registration_code,
                'city' => trim($this->inst_city),
                'building_type' => trim($this->inst_building_type),
                'address' => trim($this->inst_address),
                'description' => trim($this->inst_description) ?: null,
                'is_active' => true,
                'map_cols' => $this->map_cols,
                'map_rows' => $this->map_rows,
            ]);
        }

        $this->dispatch('wa-notify', [
            'title' => 'Profil & Dimensi Peta Disimpan',
            'message' => "{$this->institution->name} diperbarui ({$this->map_cols}x{$this->map_rows} grid).",
        ]);
    }

    public function pickCell(int $x, int $y): void
    {
        $this->pos_x = $x;
        $this->pos_y = $y;
    }

    public function generateBatchUnits(): void
    {
        abort_unless(Auth::user()?->isAdmin() && $this->institution, 403);

        $this->validate([
            'batch_group_rooms' => ['integer', 'min:0', 'max:20'],
            'batch_indoor_desks' => ['integer', 'min:0', 'max:40'],
            'batch_outdoor_desks' => ['integer', 'min:0', 'max:40'],
            'batch_reading_chairs' => ['integer', 'min:0', 'max:40'],
        ]);

        $specs = [
            ['count' => $this->batch_group_rooms, 'prefix' => 'Ruang', 'label' => 'Ruang Kelompok', 'zone' => 'group_room', 'seat_type' => 'ROOM', 'cap' => 6],
            ['count' => $this->batch_indoor_desks, 'prefix' => 'MejaIn', 'label' => 'Meja Standar Indoor', 'zone' => 'indoor_desk', 'seat_type' => 'S', 'cap' => 1],
            ['count' => $this->batch_outdoor_desks, 'prefix' => 'MejaOut', 'label' => 'Meja Leseh / Outdoor', 'zone' => 'outdoor_desk', 'seat_type' => 'L', 'cap' => 1],
            ['count' => $this->batch_reading_chairs, 'prefix' => 'KursiT', 'label' => 'Meja & Kursi Tinggi', 'zone' => 'reading_chair', 'seat_type' => 'H', 'cap' => 1],
        ];

        $cols = max(3, $this->institution->map_cols);
        $occupied = $this->institution->facilityUnits()
            ->get()
            ->map(fn (FacilityUnit $u) => "{$u->pos_x},{$u->pos_y}")
            ->flip()
            ->all();

        $created = 0;
        $x = 1;
        $y = 1;
        $order = ($this->institution->facilityUnits()->max('sort_order') ?? 0) + 1;

        foreach ($specs as $spec) {
            if ($spec['count'] <= 0) {
                continue;
            }
            $existingCount = $this->institution->facilityUnits()->where('zone', $spec['zone'])->count();

            for ($i = 1; $i <= $spec['count']; $i++) {
                while (isset($occupied["{$x},{$y}"])) {
                    $x++;
                    if ($x > $cols) {
                        $x = 1;
                        $y++;
                    }
                }

                $num = $existingCount + $i;
                $code = $spec['prefix'] . str_pad((string) $num, 2, '0', STR_PAD_LEFT);
                while ($this->institution->facilityUnits()->where('code', $code)->exists()) {
                    $num++;
                    $code = $spec['prefix'] . str_pad((string) $num, 2, '0', STR_PAD_LEFT);
                }

                FacilityUnit::create([
                    'institution_id' => $this->institution->id,
                    'code' => $code,
                    'name' => "{$spec['label']} {$num}",
                    'display_label' => $code,
                    'zone' => $spec['zone'],
                    'floor' => 1,
                    'seat_type' => $spec['seat_type'],
                    'capacity' => $spec['cap'],
                    'max_duration_minutes' => 180,
                    'is_active' => true,
                    'sort_order' => $order++,
                    'pos_x' => $x,
                    'pos_y' => $y,
                ]);

                $occupied["{$x},{$y}"] = true;
                $created++;
            }
        }

        if ($y > $this->institution->map_rows) {
            $this->institution->update(['map_rows' => min(12, $y)]);
            $this->map_rows = min(12, $y);
        }

        $this->reset(['batch_group_rooms', 'batch_indoor_desks', 'batch_outdoor_desks', 'batch_reading_chairs']);

        $this->dispatch('wa-notify', [
            'title' => 'Pemetaan Massal Selesai',
            'message' => "{$created} unit baru otomatis dipetakan ke denah 2D.",
        ]);
    }

    public function saveUnit(): void
    {
        abort_unless(Auth::user()?->isAdmin() && $this->institution, 403);

        $this->validate([
            'code' => [
                'required',
                'string',
                'max:32',
                Rule::unique('facility_units', 'code')
                    ->where('institution_id', $this->institution->id)
                    ->ignore($this->editingUnitId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'zone' => ['required', Rule::in(array_keys(FacilityUnit::ZONES))],
            'floor' => ['required', 'integer', 'min:1', 'max:10'],
            'seat_type' => ['required', Rule::in(array_keys(FacilityUnit::SEAT_TYPES))],
            'capacity' => ['required', 'integer', 'min:1', 'max:50'],
            'max_duration_minutes' => ['required', 'integer', 'min:15', 'max:480'],
            'pos_x' => ['required', 'integer', 'min:1', 'max:12'],
            'pos_y' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        if ($this->editingUnitId) {
            $unit = FacilityUnit::where('institution_id', $this->institution->id)
                ->findOrFail($this->editingUnitId);
            $unit->update([
                'code' => trim($this->code),
                'name' => trim($this->name),
                'zone' => $this->zone,
                'floor' => $this->floor,
                'seat_type' => $this->seat_type,
                'capacity' => $this->capacity,
                'max_duration_minutes' => $this->max_duration_minutes,
                'pos_x' => $this->pos_x,
                'pos_y' => $this->pos_y,
            ]);
        } else {
            FacilityUnit::create([
                'institution_id' => $this->institution->id,
                'code' => trim($this->code),
                'name' => trim($this->name),
                'display_label' => trim($this->code),
                'zone' => $this->zone,
                'floor' => $this->floor,
                'seat_type' => $this->seat_type,
                'capacity' => $this->capacity,
                'max_duration_minutes' => $this->max_duration_minutes,
                'is_active' => true,
                'sort_order' => ($this->institution->facilityUnits()->max('sort_order') ?? 0) + 1,
                'pos_x' => $this->pos_x,
                'pos_y' => $this->pos_y,
            ]);
        }

        $this->cancelEdit();
    }

    public function editUnit(int $unitId): void
    {
        if (! $this->institution) {
            return;
        }

        $unit = FacilityUnit::where('institution_id', $this->institution->id)->findOrFail($unitId);
        $this->editingUnitId = $unit->id;
        $this->code = $unit->code;
        $this->name = $unit->name;
        $this->zone = $unit->zone;
        $this->floor = $unit->floor ?? 1;
        $this->seat_type = $unit->seat_type ?? 'S';
        $this->capacity = $unit->capacity;
        $this->max_duration_minutes = $unit->max_duration_minutes;
        $this->pos_x = $unit->pos_x;
        $this->pos_y = $unit->pos_y;
    }

    public function cancelEdit(): void
    {
        $this->editingUnitId = null;
        $this->reset(['code', 'name']);
        $this->zone = 'standard_desk';
        $this->floor = 1;
        $this->seat_type = 'S';
        $this->capacity = 1;
        $this->max_duration_minutes = 180;
        $this->pos_x = 1;
        $this->pos_y = 1;
    }

    public function toggleActive(int $unitId): void
    {
        if (! $this->institution) {
            return;
        }

        $unit = FacilityUnit::where('institution_id', $this->institution->id)->findOrFail($unitId);
        $unit->update(['is_active' => ! $unit->is_active]);
    }

    public function deleteUnit(int $unitId): void
    {
        if (! $this->institution) {
            return;
        }

        FacilityUnit::where('institution_id', $this->institution->id)->whereKey($unitId)->delete();
    }

    public function render()
    {
        $allUnits = $this->institution
            ? $this->institution->facilityUnits()->get()
            : collect();

        $floors = $allUnits->pluck('floor')->unique()->sort()->values();

        $units = $this->floorFilter === 'all'
            ? $allUnits
            : $allUnits->where('floor', (int) $this->floorFilter)->values();

        $gridMap = $units->keyBy(fn (FacilityUnit $u) => "{$u->pos_x},{$u->pos_y}");

        return view('livewire.admin.facility-setup-page', [
            'units' => $units,
            'totalUnitsCount' => $allUnits->count(),
            'floors' => $floors,
            'gridMap' => $gridMap,
            'zones' => FacilityUnit::ZONES,
            'seatTypes' => FacilityUnit::SEAT_TYPES,
        ]);
    }
}
