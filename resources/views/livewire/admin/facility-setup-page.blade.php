<div class="ak-fade-up space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-md bg-[#90DEC3] px-2.5 py-0.5 font-mono text-[10px] font-bold text-[#0E241E]">STUDIO DESAIN DENAH</span>
                @if (auth()->user()->registration_code)
                    <span class="surface-subtle rounded-md px-2.5 py-0.5 font-mono text-[10px] font-bold dark:text-[#90DEC3]">
                        KODE: {{ auth()->user()->registration_code }}
                    </span>
                @endif
            </div>
            <h1 class="mt-2 text-3xl font-extrabold tracking-[-0.025em]">
                Desainer Denah, Lantai &amp; Batas Pinjam
            </h1>
            <p class="mt-1 text-xs text-[#47625A] dark:text-[#98B4AC]">
                Tata letak ruangan dan meja per lantai secara visual, atur penamaan, jumlah kursi, hingga batas durasi peminjaman masing-masing unit.
            </p>
        </div>

        <a href="{{ route('admin.monitor') }}" wire:navigate
            class="btn-brand rounded-xl px-5 py-2.5 text-xs font-semibold shadow-xs">
            Buka Live Monitor &rarr;
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-12">
        {{-- Left Inspector Sidebar --}}
        <div class="space-y-5 lg:col-span-4">
            <section class="surface-card card-lift rounded-2xl p-5">
                <h2 class="text-base font-bold tracking-tight">Profil Gedung &amp; Ukuran Kanvas</h2>
                <form wire:submit="saveInstitution" class="mt-3.5 space-y-3">
                    <div>
                        <label class="block text-xs font-semibold">Nama Instansi / Gedung</label>
                        <input type="text" wire:model="inst_name" required
                            class="input-field mt-1 w-full rounded-xl px-3 py-2 text-xs">
                    </div>
                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-xs font-semibold">Kota</label>
                            <input type="text" wire:model="inst_city" required
                                class="input-field mt-1 w-full rounded-xl px-3 py-2 text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold">Jenis Gedung</label>
                            <input type="text" wire:model="inst_building_type" required
                                class="input-field mt-1 w-full rounded-xl px-3 py-2 text-xs">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold">Alamat Lengkap</label>
                        <input type="text" wire:model="inst_address" required
                            class="input-field mt-1 w-full rounded-xl px-3 py-2 text-xs">
                    </div>
                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-xs font-semibold">Lebar Kolom (X)</label>
                            <input type="number" min="3" max="12" wire:model="map_cols"
                                class="input-field mt-1 w-full rounded-xl px-3 py-2 font-mono text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold">Tinggi Baris (Y)</label>
                            <input type="number" min="2" max="12" wire:model="map_rows"
                                class="input-field mt-1 w-full rounded-xl px-3 py-2 font-mono text-xs">
                        </div>
                    </div>
                    <button type="submit"
                        class="w-full rounded-xl bg-[#0E241E] px-4 py-2.5 text-xs font-semibold text-[#90DEC3] transition hover:opacity-90 dark:bg-[#90DEC3] dark:text-[#0B1F19]">
                        Simpan Profil &amp; Ukuran Kanvas
                    </button>
                </form>
            </section>

            @if ($institution)
                <section class="surface-card card-lift rounded-2xl p-5">
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="text-base font-bold tracking-tight">{{ $editingUnitId ? 'Ubah Detail Unit' : 'Tambah / Atur Unit' }}</h2>
                        <span class="rounded-md bg-[#90DEC3]/65 px-2 py-0.5 font-mono text-[10px] font-bold text-[#0E241E]">
                            Lt. {{ $floor }} &middot; ({{ $pos_x }},{{ $pos_y }})
                        </span>
                    </div>

                    <form wire:submit="saveUnit" class="mt-3.5 space-y-2.5 text-xs">
                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="block font-semibold">Kode Unit</label>
                                <input type="text" wire:model="code" required placeholder="LT1-R1"
                                    class="input-field mt-1 w-full rounded-xl px-3 py-2 font-mono">
                                @error('code') <p class="mt-1 text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block font-semibold">Tipe Tempat</label>
                                <select wire:model="seat_type" class="input-field mt-1 w-full rounded-xl px-2.5 py-2">
                                    @foreach ($seatTypes as $stKey => $stLabel)
                                        <option value="{{ $stKey }}">{{ $stKey }} &mdash; {{ $stLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold">Penamaan Ruangan / Meja</label>
                            <input type="text" wire:model="name" required placeholder="Contoh: Ruang Diskusi 1 / Meja Leseh 3"
                                class="input-field mt-1 w-full rounded-xl px-3 py-2">
                        </div>

                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="block font-semibold">Zona Area</label>
                                <select wire:model="zone" class="input-field mt-1 w-full rounded-xl px-2.5 py-2">
                                    @foreach ($zones as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold">Lantai</label>
                                <input type="number" min="1" max="10" wire:model="floor"
                                    class="input-field mt-1 w-full rounded-xl px-3 py-2 font-mono">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="block font-semibold">Jumlah Kursi / Kapasitas</label>
                                <input type="number" min="1" max="50" wire:model="capacity"
                                    class="input-field mt-1 w-full rounded-xl px-3 py-2 font-mono">
                            </div>
                            <div>
                                <label class="block font-semibold">Batas Pinjam (Menit)</label>
                                <input type="number" min="15" max="480" wire:model="max_duration_minutes"
                                    class="input-field mt-1 w-full rounded-xl px-3 py-2 font-mono">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="block font-semibold">Posisi Kolom (X)</label>
                                <input type="number" min="1" max="12" wire:model="pos_x"
                                    class="input-field mt-1 w-full rounded-xl px-3 py-2 font-mono">
                            </div>
                            <div>
                                <label class="block font-semibold">Posisi Baris (Y)</label>
                                <input type="number" min="1" max="12" wire:model="pos_y"
                                    class="input-field mt-1 w-full rounded-xl px-3 py-2 font-mono">
                            </div>
                        </div>

                        <div class="flex gap-2 pt-1.5">
                            <button type="submit"
                                class="btn-brand flex-1 rounded-xl px-4 py-2.5 text-xs font-semibold shadow-xs">
                                {{ $editingUnitId ? 'Simpan Perubahan' : 'Pasang di Denah' }}
                            </button>
                            @if ($editingUnitId)
                                <button type="button" wire:click="cancelEdit"
                                    class="surface-subtle rounded-xl px-3.5 py-2.5 font-semibold">Batal</button>
                            @endif
                        </div>
                    </form>
                </section>

                <section class="surface-card card-lift rounded-2xl p-5">
                    <h2 class="text-base font-bold tracking-tight">Buat Banyak Unit Sekaligus</h2>
                    <p class="mt-0.5 text-[11px] text-[#47625A] dark:text-[#98B4AC]">Tambahkan ruangan, meja, dan batas waktu pinjam otomatis pada lantai pilihan.</p>
                    <form wire:submit="generateBatchUnits" class="mt-3.5 space-y-3 text-xs">
                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="block font-semibold">Target Lantai</label>
                                <input type="number" min="1" max="10" wire:model="batch_floor"
                                    class="input-field mt-1 w-full rounded-xl px-3 py-1.5 font-mono">
                            </div>
                            <div>
                                <label class="block font-semibold">Batas Pinjam (Menit)</label>
                                <input type="number" min="15" max="480" wire:model="batch_max_duration"
                                    class="input-field mt-1 w-full rounded-xl px-3 py-1.5 font-mono">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="block font-semibold">Ruang Kelompok</label>
                                <input type="number" min="0" max="20" wire:model="batch_group_rooms"
                                    class="input-field mt-1 w-full rounded-xl px-3 py-1.5 font-mono">
                            </div>
                            <div>
                                <label class="block font-semibold">Meja Standar (S)</label>
                                <input type="number" min="0" max="40" wire:model="batch_indoor_desks"
                                    class="input-field mt-1 w-full rounded-xl px-3 py-1.5 font-mono">
                            </div>
                            <div>
                                <label class="block font-semibold">Meja Leseh (L)</label>
                                <input type="number" min="0" max="40" wire:model="batch_outdoor_desks"
                                    class="input-field mt-1 w-full rounded-xl px-3 py-1.5 font-mono">
                            </div>
                            <div>
                                <label class="block font-semibold">Meja Tinggi (H)</label>
                                <input type="number" min="0" max="40" wire:model="batch_reading_chairs"
                                    class="input-field mt-1 w-full rounded-xl px-3 py-1.5 font-mono">
                            </div>
                        </div>
                        <button type="submit"
                            class="btn-mint w-full rounded-xl px-4 py-2.5 text-xs font-semibold shadow-xs">
                            Generate ke Denah Lantai
                        </button>
                    </form>
                </section>
            @endif
        </div>

        {{-- Right Studio Canvas & Unit Directory --}}
        <div class="space-y-6 lg:col-span-8">
            @if ($institution)
                <section class="surface-card ak-fade-up ak-delay-1 rounded-2xl p-5">
                    {{-- Top Bar: Multi-Floor Selector & Designer Mode --}}
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#CBE8DC] pb-4 dark:border-[#213531]">
                        <div>
                            <h2 class="text-lg font-bold tracking-tight">
                                Kanvas Denah &mdash; Lantai {{ $activeFloor }}
                            </h2>
                            <p class="text-[11px] text-[#47625A] dark:text-[#98B4AC]">
                                Pilih alat kuas di bawah lalu klik sel kosong pada kanvas, atau klik unit yang sudah ada untuk mengedit.
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            @if ($institution->has_custom_floorplan)
                                <button type="button" wire:click="toggleDesignerMode"
                                    class="surface-subtle rounded-lg px-3 py-1.5 text-[11px] font-semibold transition hover:border-[#0F6E56]">
                                    {{ $useGridDesigner ? 'Lihat Blueprint Arsitektur' : 'Mode Studio Grid' }}
                                </button>
                            @endif
                            <span class="surface-subtle rounded-lg px-3 py-1 font-mono text-[11px] font-bold">
                                {{ $floorUnits->count() }} unit (Lt. {{ $activeFloor }}) &middot; {{ $totalUnitsCount }} total
                            </span>
                        </div>
                    </div>

                    {{-- Floor Tabs --}}
                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex flex-wrap items-center gap-1.5">
                            @foreach ($floors as $fl)
                                <button type="button" wire:click="selectFloor({{ $fl }})"
                                    class="rounded-xl px-3.5 py-1.5 text-xs font-semibold transition {{ $activeFloor === $fl ? 'bg-[#0F6E56] text-[#F7FCFA] shadow-xs dark:bg-[#90DEC3] dark:text-[#0B1F19]' : 'surface-subtle text-[#47625A] hover:text-[#0E241E] dark:text-[#98B4AC]' }}">
                                    Lantai {{ $fl }}
                                </button>
                            @endforeach
                            <button type="button" wire:click="addFloor"
                                class="rounded-xl border border-dashed border-[#0F6E56]/50 px-3 py-1.5 text-xs font-semibold text-[#0F6E56] transition hover:bg-[#90DEC3]/30 dark:border-[#90DEC3]/50 dark:text-[#90DEC3]">
                                + Tambah Lantai
                            </button>
                        </div>

                        {{-- Bulk Duration Setting for Active Floor --}}
                        <form wire:submit="applyDurationToFloor" class="flex items-center gap-1.5 text-xs">
                            <span class="text-[11px] font-medium text-[#47625A] dark:text-[#98B4AC]">Batas pinjam Lt.{{ $activeFloor }}:</span>
                            <input type="number" min="15" max="480" wire:model="bulk_floor_duration"
                                class="input-field w-16 rounded-lg px-2 py-1 text-center font-mono text-[11px]">
                            <span class="text-[11px] text-[#47625A] dark:text-[#98B4AC]">mnt</span>
                            <button type="submit"
                                class="rounded-lg bg-[#90DEC3]/75 px-2.5 py-1 text-[11px] font-bold text-[#0E241E] transition hover:bg-[#90DEC3]">
                                Terapkan
                            </button>
                        </form>
                    </div>

                    {{-- Quick Paint Toolbar --}}
                    @if (! $institution->has_custom_floorplan || $useGridDesigner)
                        @php
                            $tools = [
                                'select' => 'Pilih Sel',
                                'ROOM' => '+ Ruangan',
                                'S' => '+ Meja Standar (S)',
                                'L' => '+ Meja Leseh (L)',
                                'H' => '+ Meja Tinggi (H)',
                                'F' => '+ Ruang Fokus (F)',
                            ];
                        @endphp
                        <div class="surface-subtle mt-3.5 flex flex-wrap items-center gap-1.5 rounded-xl p-2">
                            <span class="px-2 text-[10px] font-bold uppercase tracking-wider text-[#47625A] dark:text-[#98B4AC]">Alat Kanvas:</span>
                            @foreach ($tools as $toolKey => $toolLabel)
                                <button type="button" wire:click="setPaintTool('{{ $toolKey }}')"
                                    class="rounded-lg px-3 py-1.5 text-[11px] font-semibold transition {{ $paintTool === $toolKey ? 'bg-[#0F6E56] text-[#F7FCFA] shadow-xs dark:bg-[#90DEC3] dark:text-[#0B1F19]' : 'bg-[#F7FCFA]/80 text-[#0E241E] hover:bg-[#90DEC3]/40 dark:bg-[#131F1D] dark:text-[#E6F2EE]' }}">
                                    {{ $toolLabel }}
                                </button>
                            @endforeach
                        </div>
                    @endif

                    {{-- Canvas Area --}}
                    @if ($institution->has_custom_floorplan && ! $useGridDesigner)
                        <div class="mt-4">
                            <x-ui.pnj-floorplan
                                :floor="$activeFloor"
                                :units="$floorUnits"
                                :matching-unit-ids="$floorUnits->pluck('id')->all()"
                                :selected-unit-id="$editingUnitId"
                                click-action="editUnit"
                            />
                        </div>
                    @else
                        <div class="mt-4 overflow-x-auto rounded-2xl border border-[#CBE8DC] bg-[#EEF8F4]/70 p-4 dark:border-[#213531] dark:bg-[#0D1514]/70">
                            <div class="grid min-w-[540px] gap-2.5" style="grid-template-columns: repeat({{ $institution->map_cols }}, minmax(0, 1fr));">
                                @for ($y = 1; $y <= $institution->map_rows; $y++)
                                    @for ($x = 1; $x <= $institution->map_cols; $x++)
                                        @php $u = $gridMap->get("{$x},{$y}"); @endphp
                                        @if ($u)
                                            @php
                                                $isSelected = $editingUnitId === $u->id;
                                                $accentBar = match ($u->seat_type) {
                                                    'ROOM' => 'border-l-4 border-l-amber-500',
                                                    'L' => 'border-l-4 border-l-teal-500',
                                                    'H' => 'border-l-4 border-l-sky-500',
                                                    'F' => 'border-l-4 border-l-indigo-500',
                                                    default => 'border-l-4 border-l-[#0F6E56]',
                                                };
                                            @endphp
                                            <button type="button" wire:click="editUnit({{ $u->id }})"
                                                class="{{ $accentBar }} flex min-h-[72px] flex-col justify-between rounded-xl border p-2.5 text-left transition {{ $isSelected ? 'border-[#0F6E56] bg-[#90DEC3]/65 ring-2 ring-[#0F6E56] dark:ring-[#90DEC3]' : 'border-[#CBE8DC] bg-[#F7FCFA] hover:-translate-y-0.5 hover:border-[#0F6E56] dark:border-[#213531] dark:bg-[#192825]' }} {{ ! $u->is_active ? 'opacity-50' : '' }}">
                                                <div class="flex items-center justify-between gap-1">
                                                    <span class="truncate font-mono text-xs font-bold">{{ $u->code }}</span>
                                                    <span class="shrink-0 rounded bg-[#E2F3EC] px-1.5 py-0.5 font-mono text-[9px] font-semibold text-[#0E241E] dark:bg-[#131F1D] dark:text-[#90DEC3]">{{ $u->capacity }}k</span>
                                                </div>
                                                <div class="mt-1 truncate text-[10px] font-medium text-[#0E241E] dark:text-[#E6F2EE]">{{ $u->name }}</div>
                                                <div class="mt-0.5 flex items-center justify-between font-mono text-[9px] text-[#47625A] dark:text-[#98B4AC]">
                                                    <span>{{ $u->seat_type }}</span>
                                                    <span>{{ $u->max_duration_minutes }}m</span>
                                                </div>
                                            </button>
                                        @else
                                            <button type="button" wire:click="pickCell({{ $x }}, {{ $y }})"
                                                class="flex min-h-[72px] flex-col items-center justify-center rounded-xl border border-dashed border-[#CBE8DC] font-mono text-[10px] text-[#47625A]/75 transition hover:border-[#0F6E56] hover:bg-[#90DEC3]/25 dark:border-[#213531] dark:text-[#98B4AC]">
                                                <span>{{ $paintTool === 'select' ? "+ ({$x},{$y})" : "+ {$paintTool}" }}</span>
                                            </button>
                                        @endif
                                    @endfor
                                @endfor
                            </div>
                        </div>
                    @endif
                </section>

                {{-- Unit Table on Active Floor --}}
                <section class="surface-card ak-fade-up ak-delay-2 rounded-2xl p-5">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <h2 class="text-base font-bold tracking-tight">Daftar Unit &mdash; Lantai {{ $activeFloor }}</h2>
                            <p class="text-[11px] text-[#47625A] dark:text-[#98B4AC]">Kelola penamaan, kapasitas kursi, dan batas waktu pinjam per unit.</p>
                        </div>
                        <span class="font-mono text-[11px] text-[#47625A] dark:text-[#98B4AC]">Menampilkan {{ $floorUnits->count() }} unit</span>
                    </div>
                    <div class="mt-3 max-h-[420px] overflow-x-auto overflow-y-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="sticky top-0 bg-[#F7FCFA] dark:bg-[#131F1D]">
                                <tr class="border-b border-[#CBE8DC] text-[#47625A] dark:border-[#213531] dark:text-[#98B4AC]">
                                    <th class="pb-2.5 font-semibold">Kode &amp; Posisi</th>
                                    <th class="pb-2.5 font-semibold">Nama &amp; Tipe</th>
                                    <th class="pb-2.5 font-semibold">Kursi &amp; Batas</th>
                                    <th class="pb-2.5 font-semibold">Status</th>
                                    <th class="pb-2.5 text-right font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#CBE8DC]/70 dark:divide-[#213531]">
                                @forelse ($floorUnits as $unit)
                                    <tr class="transition hover:bg-[#EEF8F4]/60 dark:hover:bg-[#192825]/60">
                                        <td class="py-2.5 font-mono font-bold">
                                            {{ $unit->code }}
                                            <span class="ml-1 rounded bg-[#E2F3EC] px-1.5 py-0.5 text-[10px] font-semibold text-[#47625A] dark:bg-[#192825] dark:text-[#98B4AC]">({{ $unit->pos_x }},{{ $unit->pos_y }})</span>
                                        </td>
                                        <td class="py-2.5">
                                            <div class="font-semibold">{{ $unit->name }}</div>
                                            <div class="text-[10px] text-[#47625A] dark:text-[#98B4AC]">{{ $unit->seatTypeLabel() }} &middot; {{ $unit->zoneLabel() }}</div>
                                        </td>
                                        <td class="py-2.5 font-mono">{{ $unit->capacity }} org &middot; {{ $unit->max_duration_minutes }} mnt</td>
                                        <td class="py-2.5">
                                            <button type="button" wire:click="toggleActive({{ $unit->id }})"
                                                class="rounded-md px-2.5 py-0.5 text-[10px] font-semibold transition {{ $unit->is_active ? 'bg-[#90DEC3] text-[#0E241E]' : 'surface-subtle text-[#47625A]' }}">
                                                {{ $unit->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </button>
                                        </td>
                                        <td class="py-2.5 text-right">
                                            <button type="button" wire:click="editUnit({{ $unit->id }})" class="font-semibold text-[#0F6E56] hover:underline dark:text-[#90DEC3]">Edit</button>
                                            <button type="button" wire:click="deleteUnit({{ $unit->id }})" class="ml-2 font-semibold text-rose-600 hover:underline">Hapus</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-xs text-[#47625A] dark:text-[#98B4AC]">
                                            Belum ada unit di Lantai {{ $activeFloor }}. Pilih alat kuas di atas atau gunakan form di kiri untuk mulai mendesain denah.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif
        </div>
    </div>
</div>
