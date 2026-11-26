<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded-md bg-[#90DEC3] px-2.5 py-0.5 font-mono text-[10px] font-bold text-[#0E241E]">PEMETAAN DENAH LOKASI</span>
                @if (auth()->user()->registration_code)
                    <span class="surface-subtle rounded-md px-2.5 py-0.5 font-mono text-[10px] font-bold dark:text-[#90DEC3]">
                        KODE: {{ auth()->user()->registration_code }}
                    </span>
                @endif
            </div>
            <h1 class="mt-2 text-3xl font-extrabold tracking-[-0.025em]">
                Konfigurasi Peta Ruangan, Meja &amp; Kursi
            </h1>
            <p class="mt-1 text-xs text-[#47625A] dark:text-[#98B4AC]">
                Kelola unit riil gedung Anda (L: Leseh, S: Standar, H: Tinggi, Ruangan) agar tampil akurat pada denah 2D pengunjung.
            </p>
        </div>

        <a href="{{ route('admin.monitor') }}" wire:navigate
            class="btn-brand rounded-lg px-5 py-2.5 text-xs font-semibold shadow-xs">
            Buka Live Monitor &rarr;
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-12">
        <div class="space-y-5 lg:col-span-4">
            <section class="surface-card rounded-2xl p-5">
                <h2 class="text-base font-bold tracking-tight">Profil Lokasi &amp; Ukuran Denah</h2>
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
                        <label class="block text-xs font-semibold">Alamat</label>
                        <input type="text" wire:model="inst_address" required
                            class="input-field mt-1 w-full rounded-xl px-3 py-2 text-xs">
                    </div>
                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-xs font-semibold">Kolom Peta (X)</label>
                            <input type="number" min="3" max="12" wire:model="map_cols"
                                class="input-field mt-1 w-full rounded-xl px-3 py-2 font-mono text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold">Baris Peta (Y)</label>
                            <input type="number" min="2" max="12" wire:model="map_rows"
                                class="input-field mt-1 w-full rounded-xl px-3 py-2 font-mono text-xs">
                        </div>
                    </div>
                    <button type="submit"
                        class="w-full rounded-lg bg-[#0E241E] px-4 py-2.5 text-xs font-semibold text-[#90DEC3] transition hover:opacity-90 dark:bg-[#90DEC3] dark:text-[#0B1F19]">
                        Simpan Profil &amp; Grid Peta
                    </button>
                </form>
            </section>

            @if ($institution)
                <section class="surface-card rounded-2xl p-5">
                    <h2 class="text-base font-bold tracking-tight">Generate Unit Massal ke Peta</h2>
                    <p class="mt-0.5 text-[11px] text-[#47625A] dark:text-[#98B4AC]">Masukkan jumlah riil ruangan/meja/kursi untuk dipetakan otomatis.</p>
                    <form wire:submit="generateBatchUnits" class="mt-3.5 space-y-3">
                        <div class="grid grid-cols-2 gap-2.5 text-xs">
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
                            class="btn-mint w-full rounded-lg px-4 py-2 text-xs font-semibold shadow-xs">
                            Petakan Otomatis ke Denah
                        </button>
                    </form>
                </section>

                <section class="surface-card rounded-2xl p-5">
                    <h2 class="text-base font-bold tracking-tight">{{ $editingUnitId ? 'Edit Unit Posisi' : 'Tambah / Atur Unit Manual' }}</h2>
                    <form wire:submit="saveUnit" class="mt-3.5 space-y-2.5 text-xs">
                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="block font-semibold">Kode Unit</label>
                                <input type="text" wire:model="code" required placeholder="LT1-L3"
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

                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="block font-semibold">Zona</label>
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

                        <div>
                            <label class="block font-semibold">Nama Deskriptif</label>
                            <input type="text" wire:model="name" required placeholder="Meja Leseh 3 (Lantai 1)"
                                class="input-field mt-1 w-full rounded-xl px-3 py-2">
                        </div>

                        <div class="grid grid-cols-4 gap-2">
                            <div>
                                <label class="block font-semibold">Kap.</label>
                                <input type="number" min="1" max="50" wire:model="capacity"
                                    class="input-field mt-1 w-full rounded-xl px-2 py-2 font-mono">
                            </div>
                            <div>
                                <label class="block font-semibold">Durasi</label>
                                <input type="number" min="15" max="480" wire:model="max_duration_minutes"
                                    class="input-field mt-1 w-full rounded-xl px-2 py-2 font-mono">
                            </div>
                            <div>
                                <label class="block font-semibold">Kol (X)</label>
                                <input type="number" min="1" max="12" wire:model="pos_x"
                                    class="input-field mt-1 w-full rounded-xl px-2 py-2 font-mono">
                            </div>
                            <div>
                                <label class="block font-semibold">Bar (Y)</label>
                                <input type="number" min="1" max="12" wire:model="pos_y"
                                    class="input-field mt-1 w-full rounded-xl px-2 py-2 font-mono">
                            </div>
                        </div>

                        <div class="flex gap-2 pt-1.5">
                            <button type="submit"
                                class="btn-brand flex-1 rounded-lg px-4 py-2 text-xs font-semibold shadow-xs">
                                {{ $editingUnitId ? 'Simpan Unit' : 'Simpan ke Peta' }}
                            </button>
                            @if ($editingUnitId)
                                <button type="button" wire:click="cancelEdit"
                                    class="surface-subtle rounded-lg px-3.5 py-2 font-semibold">Batal</button>
                            @endif
                        </div>
                    </form>
                </section>
            @endif
        </div>

        <div class="space-y-6 lg:col-span-8">
            @if ($institution)
                <section class="surface-card rounded-2xl p-5">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-[#CBE8DC] pb-3.5 dark:border-[#213531]">
                        <div>
                            <h2 class="text-lg font-bold tracking-tight">
                                {{ $institution->has_custom_floorplan ? 'Blueprint Arsitektur 2D' : "Blueprint Denah Lokasi 2D ({$institution->map_cols}x{$institution->map_rows})" }}
                            </h2>
                            <p class="text-[11px] text-[#47625A] dark:text-[#98B4AC]">
                                Klik unit pada peta untuk mengedit posisi, tipe tempat (L/S/H), atau kapasitasnya.
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            @if ($floors->count() > 1)
                                <select wire:model.live="floorFilter" class="surface-subtle rounded-lg px-3 py-1.5 text-xs font-semibold outline-none">
                                    <option value="all">Semua Lantai</option>
                                    @foreach ($floors as $fl)
                                        <option value="{{ $fl }}">Lantai {{ $fl }}</option>
                                    @endforeach
                                </select>
                            @endif
                            <span class="surface-subtle rounded-lg px-3 py-1 font-mono text-[11px] font-bold">
                                {{ $units->count() }}/{{ $totalUnitsCount }} unit
                            </span>
                        </div>
                    </div>

                    @if ($institution->has_custom_floorplan)
                        @php $previewFloor = $floorFilter === 'all' ? ($floors->first() ?? 1) : (int) $floorFilter; @endphp
                        <div class="mt-4">
                            <x-ui.pnj-floorplan
                                :floor="$previewFloor"
                                :units="$units->where('floor', $previewFloor)"
                                :matching-unit-ids="$units->where('floor', $previewFloor)->pluck('id')->all()"
                                :selected-unit-id="$editingUnitId"
                                click-action="editUnit"
                            />
                        </div>
                    @else
                        <div class="mt-4 overflow-x-auto">
                            <div class="grid min-w-[540px] gap-2.5" style="grid-template-columns: repeat({{ $institution->map_cols }}, minmax(0, 1fr));">
                                @for ($y = 1; $y <= $institution->map_rows; $y++)
                                    @for ($x = 1; $x <= $institution->map_cols; $x++)
                                        @php $u = $gridMap->get("{$x},{$y}"); @endphp
                                        @if ($u)
                                            <button type="button" wire:click="editUnit({{ $u->id }})"
                                                class="flex flex-col justify-between rounded-xl border p-2.5 text-left transition {{ $editingUnitId === $u->id ? 'border-[#0F6E56] bg-[#90DEC3]/65 ring-2 ring-[#0F6E56]' : 'border-[#CBE8DC] bg-[#E2F3EC]/70 hover:border-[#0F6E56] dark:border-[#213531] dark:bg-[#192825]' }}">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-mono text-xs font-bold">{{ $u->code }}</span>
                                                    <span class="font-mono text-[9px] text-[#47625A] dark:text-[#98B4AC]">({{ $x }},{{ $y }})</span>
                                                </div>
                                                <span class="mt-1 text-[10px] text-[#47625A] dark:text-[#98B4AC]">{{ $u->seatTypeLabel() }}</span>
                                            </button>
                                        @else
                                            <button type="button" wire:click="pickCell({{ $x }}, {{ $y }})"
                                                class="flex h-16 flex-col items-center justify-center rounded-xl border border-dashed border-[#CBE8DC] font-mono text-[10px] text-[#47625A]/70 transition hover:border-[#0F6E56] hover:bg-[#90DEC3]/25 dark:border-[#213531] dark:text-[#98B4AC]">
                                                + ({{ $x }},{{ $y }})
                                            </button>
                                        @endif
                                    @endfor
                                @endfor
                            </div>
                        </div>
                    @endif
                </section>

                <section class="surface-card rounded-2xl p-5">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-bold tracking-tight">Daftar Detail Unit Fasilitas</h2>
                        <span class="font-mono text-[11px] text-[#47625A] dark:text-[#98B4AC]">Menampilkan {{ $units->count() }} unit</span>
                    </div>
                    <div class="mt-3 max-h-[420px] overflow-x-auto overflow-y-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="sticky top-0 bg-[#F7FCFA] dark:bg-[#131F1D]">
                                <tr class="border-b border-[#CBE8DC] text-[#47625A] dark:border-[#213531] dark:text-[#98B4AC]">
                                    <th class="pb-2.5 font-semibold">Kode &amp; Lantai</th>
                                    <th class="pb-2.5 font-semibold">Nama &amp; Tipe</th>
                                    <th class="pb-2.5 font-semibold">Kapasitas</th>
                                    <th class="pb-2.5 font-semibold">Status</th>
                                    <th class="pb-2.5 text-right font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#CBE8DC]/70 dark:divide-[#213531]">
                                @foreach ($units as $unit)
                                    <tr>
                                        <td class="py-2.5 font-mono font-bold">
                                            {{ $unit->code }}
                                            <span class="ml-1 rounded bg-[#E2F3EC] px-1.5 py-0.5 text-[10px] font-semibold text-[#47625A] dark:bg-[#192825] dark:text-[#98B4AC]">Lt.{{ $unit->floor }}</span>
                                        </td>
                                        <td class="py-2.5">
                                            <div class="font-semibold">{{ $unit->name }}</div>
                                            <div class="text-[10px] text-[#47625A] dark:text-[#98B4AC]">{{ $unit->seatTypeLabel() }} &middot; {{ $unit->zoneLabel() }}</div>
                                        </td>
                                        <td class="py-2.5 font-mono">{{ $unit->capacity }} org &middot; {{ $unit->max_duration_minutes }}m</td>
                                        <td class="py-2.5">
                                            <button type="button" wire:click="toggleActive({{ $unit->id }})"
                                                class="rounded-md px-2.5 py-0.5 text-[10px] font-semibold {{ $unit->is_active ? 'bg-[#90DEC3] text-[#0E241E]' : 'surface-subtle text-[#47625A]' }}">
                                                {{ $unit->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </button>
                                        </td>
                                        <td class="py-2.5 text-right">
                                            <button type="button" wire:click="editUnit({{ $unit->id }})" class="font-semibold text-[#0F6E56] hover:underline dark:text-[#90DEC3]">Edit</button>
                                            <button type="button" wire:click="deleteUnit({{ $unit->id }})" class="ml-2 font-semibold text-rose-600 hover:underline">Hapus</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif
        </div>
    </div>
</div>
