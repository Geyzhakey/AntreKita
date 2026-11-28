<?php

namespace App\Livewire\Admin;

use App\Models\FacilityUnit;
use App\Models\Institution;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Desainer Denah & Unit — Admin AntreKita'])]
class FacilitySetupPage extends Component
{
    public ?Institution $institution = null;

    public string $inst_name = '';

    public string $inst_city = '';

    public string $inst_building_type = 'Perpustakaan Kampus';

    public string $inst_address = '';

    public string $inst_description = '';

    public int $map_cols = 8;

    public int $map_rows = 6;

    // Interactive Multi-Floor Floorplan Designer State
    public int $activeFloor = 1;

    public int $maxKnownFloor = 1;

    public string $paintTool = 'select'; // select | ROOM | S | L | H | F

    public bool $useGridDesigner = false;

    public int $bulk_floor_duration = 120;

    // Single Unit CRUD
    public ?int $editingUnitId = null;

    public string $code = '';

    public string $name = '';

    public string $zone = 'standard_desk';

    public int $floor = 1;

    public string $seat_type = 'S';

    public int $capacity = 2;

    public int $max_duration_minutes = 120;

    public int $pos_x = 1;

    public int $pos_y = 1;

    // Per-Floor Batch Generator
    public int $batch_floor = 1;

    public int $batch_max_duration = 120;

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
            $this->map_cols = $this->institution->map_cols ?? 8;
            $this->map_rows = $this->institution->map_rows ?? 6;
            $this->maxKnownFloor = max(1, (int) ($this->institution->facilityUnits()->max('floor') ?? 1));
            $this->activeFloor = 1;
            $this->floor = 1;
            $this->batch_floor = 1;
        }
    }

    public function selectFloor(int $floor): void
    {
        $floor = max(1, min(10, $floor));
        $this->activeFloor = $floor;
        $this->maxKnownFloor = max($this->maxKnownFloor, $floor);
        $this->floor = $floor;
        $this->batch_floor = $floor;
        $this->cancelEditKeepingFloor();
    }

    public function addFloor(): void
    {
        abort_unless(Auth::user()?->isAdmin() && $this->institution, 403);

        $dbMax = (int) ($this->institution->facilityUnits()->max('floor') ?? 1);
        $nextFloor = min(10, max($this->maxKnownFloor, $dbMax) + 1);
        $this->maxKnownFloor = $nextFloor;
        $this->selectFloor($nextFloor);

        $this->dispatch('wa-notify', [
            'title' => "Lantai {$nextFloor} Siap Didesain",
            'message' => "Silakan pilih alat kuas atau buat unit baru untuk menata Lantai {$nextFloor}.",
        ]);
    }

    public function setPaintTool(string $tool): void
    {
        $allowed = ['select', 'ROOM', 'S', 'L', 'H', 'F'];
        $this->paintTool = in_array($tool, $allowed, true) ? $tool : 'select';
    }

    public function toggleDesignerMode(): void
    {
        $this->useGridDesigner = ! $this->useGridDesigner;
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
            'title' => 'Profil & Kanvas Denah Disimpan',
            'message' => "{$this->institution->name} diperbarui ({$this->map_cols}x{$this->map_rows} grid).",
        ]);
    }

    public function pickCell(int $x, int $y): void
    {
        abort_unless(Auth::user()?->isAdmin() && $this->institution, 403);

        $existing = $this->institution->facilityUnits()
            ->where('floor', $this->activeFloor)
            ->where('pos_x', $x)
            ->where('pos_y', $y)
            ->first();

        if ($existing) {
            $this->editUnit($existing->id);

            return;
        }

        if ($this->paintTool !== 'select') {
            $meta = match ($this->paintTool) {
                'ROOM' => ['prefix' => "LT{$this->activeFloor}-R", 'label' => 'Ruang Diskusi', 'zone' => 'group_room', 'cap' => 6],
                'L' => ['prefix' => "LT{$this->activeFloor}-L", 'label' => 'Meja Leseh', 'zone' => 'leseh_desk', 'cap' => 2],
                'H' => ['prefix' => "LT{$this->activeFloor}-H", 'label' => 'Meja Tinggi', 'zone' => 'high_desk', 'cap' => 1],
                'F' => ['prefix' => "LT{$this->activeFloor}-F", 'label' => 'Ruang Fokus', 'zone' => 'focus_desk', 'cap' => 1],
                default => ['prefix' => "LT{$this->activeFloor}-S", 'label' => 'Meja Standar', 'zone' => 'standard_desk', 'cap' => 2],
            };

            $num = $this->institution->facilityUnits()
                ->where('floor', $this->activeFloor)
                ->where('seat_type', $this->paintTool)
                ->count() + 1;

            $code = $meta['prefix'] . $num;
            while ($this->institution->facilityUnits()->where('code', $code)->exists()) {
                $num++;
                $code = $meta['prefix'] . $num;
            }

            $created = FacilityUnit::create([
                'institution_id' => $this->institution->id,
                'code' => $code,
                'name' => "{$meta['label']} {$num} (Lt. {$this->activeFloor})",
                'display_label' => $code,
                'zone' => $meta['zone'],
                'floor' => $this->activeFloor,
                'seat_type' => $this->paintTool,
                'capacity' => $meta['cap'],
                'max_duration_minutes' => max(15, min(480, $this->batch_max_duration)),
                'is_active' => true,
                'sort_order' => ($this->institution->facilityUnits()->max('sort_order') ?? 0) + 1,
                'pos_x' => $x,
                'pos_y' => $y,
            ]);

            $this->editUnit($created->id);

            $this->dispatch('wa-notify', [
                'title' => 'Unit Ditempatkan di Kanvas',
                'message' => "{$created->code} ditambahkan ke Lantai {$this->activeFloor} posisi ({$x},{$y}).",
            ]);

            return;
        }

        $this->editingUnitId = null;
        $this->floor = $this->activeFloor;
        $this->pos_x = $x;
        $this->pos_y = $y;
    }

    public function applyDurationToFloor(): void
    {
        abort_unless(Auth::user()?->isAdmin() && $this->institution, 403);

        $this->validate([
            'bulk_floor_duration' => ['required', 'integer', 'min:15', 'max:480'],
        ]);

        $affected = $this->institution->facilityUnits()
            ->where('floor', $this->activeFloor)
            ->update(['max_duration_minutes' => $this->bulk_floor_duration]);

        $this->dispatch('wa-notify', [
            'title' => 'Batas Waktu Peminjaman Diperbarui',
            'message' => "{$affected} unit di Lantai {$this->activeFloor} kini memiliki batas pinjam {$this->bulk_floor_duration} menit.",
        ]);
    }

    public function generateBatchUnits(): void
    {
        abort_unless(Auth::user()?->isAdmin() && $this->institution, 403);

        $this->validate([
            'batch_floor' => ['required', 'integer', 'min:1', 'max:10'],
            'batch_max_duration' => ['required', 'integer', 'min:15', 'max:480'],
            'batch_group_rooms' => ['integer', 'min:0', 'max:20'],
            'batch_indoor_desks' => ['integer', 'min:0', 'max:40'],
            'batch_outdoor_desks' => ['integer', 'min:0', 'max:40'],
            'batch_reading_chairs' => ['integer', 'min:0', 'max:40'],
        ]);

        $targetFloor = $this->batch_floor;
        $this->maxKnownFloor = max($this->maxKnownFloor, $targetFloor);

        $specs = [
            ['count' => $this->batch_group_rooms, 'prefix' => "LT{$targetFloor}-R", 'label' => 'Ruang Kelompok', 'zone' => 'group_room', 'seat_type' => 'ROOM', 'cap' => 6],
            ['count' => $this->batch_indoor_desks, 'prefix' => "LT{$targetFloor}-S", 'label' => 'Meja Standar', 'zone' => 'standard_desk', 'seat_type' => 'S', 'cap' => 2],
            ['count' => $this->batch_outdoor_desks, 'prefix' => "LT{$targetFloor}-L", 'label' => 'Meja Leseh', 'zone' => 'leseh_desk', 'seat_type' => 'L', 'cap' => 2],
            ['count' => $this->batch_reading_chairs, 'prefix' => "LT{$targetFloor}-H", 'label' => 'Meja & Kursi Tinggi', 'zone' => 'high_desk', 'seat_type' => 'H', 'cap' => 1],
        ];

        $cols = max(3, $this->institution->map_cols);
        $occupied = $this->institution->facilityUnits()
            ->where('floor', $targetFloor)
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
            $existingCount = $this->institution->facilityUnits()
                ->where('floor', $targetFloor)
                ->where('zone', $spec['zone'])
                ->count();

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
                    'name' => "{$spec['label']} {$num} (Lt. {$targetFloor})",
                    'display_label' => $code,
                    'zone' => $spec['zone'],
                    'floor' => $targetFloor,
                    'seat_type' => $spec['seat_type'],
                    'capacity' => $spec['cap'],
                    'max_duration_minutes' => $this->batch_max_duration,
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
            $newRows = min(12, $y);
            $this->institution->update(['map_rows' => $newRows]);
            $this->map_rows = $newRows;
        }

        $this->activeFloor = $targetFloor;
        $this->reset(['batch_group_rooms', 'batch_indoor_desks', 'batch_outdoor_desks', 'batch_reading_chairs']);

        $this->dispatch('wa-notify', [
            'title' => "Pemetaan Lantai {$targetFloor} Selesai",
            'message' => "{$created} unit baru dipetakan dengan batas pinjam {$this->batch_max_duration} menit.",
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
                'display_label' => trim($this->code),
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

        $this->maxKnownFloor = max($this->maxKnownFloor, $this->floor);
        $this->activeFloor = $this->floor;
        $this->cancelEditKeepingFloor();

        $this->dispatch('wa-notify', [
            'title' => 'Unit Fasilitas Disimpan',
            'message' => "Konfigurasi unit pada Lantai {$this->activeFloor} berhasil disimpan.",
        ]);
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
        $this->activeFloor = $this->floor;
        $this->seat_type = $unit->seat_type ?? 'S';
        $this->capacity = $unit->capacity;
        $this->max_duration_minutes = $unit->max_duration_minutes;
        $this->pos_x = $unit->pos_x;
        $this->pos_y = $unit->pos_y;
    }

    public function cancelEdit(): void
    {
        $this->floor = 1;
        $this->cancelEditKeepingFloor();
    }

    protected function cancelEditKeepingFloor(): void
    {
        $this->editingUnitId = null;
        $this->reset(['code', 'name']);
        $this->zone = 'standard_desk';
        $this->floor = $this->activeFloor;
        $this->seat_type = 'S';
        $this->capacity = 2;
        $this->max_duration_minutes = $this->batch_max_duration;
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
        if ($this->editingUnitId === $unitId) {
            $this->cancelEditKeepingFloor();
        }
    }

    public function render()
    {
        $allUnits = $this->institution
            ? $this->institution->facilityUnits()->orderBy('floor')->orderBy('sort_order')->get()
            : collect();

        $dbFloors = $allUnits->pluck('floor')->map(fn ($f) => (int) ($f ?: 1))->unique();
        $floors = $dbFloors->merge(range(1, max(1, $this->maxKnownFloor)))->unique()->sort()->values();

        $floorUnits = $allUnits->where('floor', $this->activeFloor)->values();
        $gridMap = $floorUnits->keyBy(fn (FacilityUnit $u) => "{$u->pos_x},{$u->pos_y}");

        return view('livewire.admin.facility-setup-page', [
            'allUnits' => $allUnits,
            'floorUnits' => $floorUnits,
            'totalUnitsCount' => $allUnits->count(),
            'floors' => $floors,
            'gridMap' => $gridMap,
            'zones' => FacilityUnit::ZONES,
            'seatTypes' => FacilityUnit::SEAT_TYPES,
        ]);
    }
}
