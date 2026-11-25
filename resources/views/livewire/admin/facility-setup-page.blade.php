<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded bg-[#CAFFA1] px-2 py-0.5 font-mono text-[10px] font-bold text-[#1C241C]">PILAR 02 &middot; PEMETAAN DENAH LOKASI</span>
                @if (auth()->user()->registration_code)
                    <span class="rounded border border-[#D7CFB8] bg-[#EFEAD8] px-2 py-0.5 font-mono text-[10px] font-bold text-[#1C241C] dark:border-[#2B3831] dark:bg-[#202B25] dark:text-[#CAFFA1]">
                        KODE: {{ auth()->user()->registration_code }}
                    </span>
                @endif
            </div>
            <h1 class="mt-1.5 font-serif text-3xl font-bold tracking-tight text-[#1C241C] dark:text-[#F3EFE4]">
                Konfigurasi Peta Ruangan, Meja &amp; Kursi
            </h1>
            <p class="mt-1 text-xs text-[#4B5649] dark:text-[#B8C4B5]">
                Hitung dan petakan unit riil gedung Anda ke koordinat denah 2D agar mudah ditemukan pengguna.
            </p>
        </div>

        <a href="{{ route('admin.monitor') }}" wire:navigate
            class="rounded-xl bg-[#1B5E3A] px-4 py-2 text-xs font-bold text-[#CAFFA1] hover:bg-[#14472C] dark:bg-[#CAFFA1] dark:text-[#1C241C]">
            Buka Live Monitor &rarr;
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-12">
        <div class="space-y-5 lg:col-span-4">
            <section class="rounded-2xl border border-[#D7CFB8] bg-[#FBF8EE] p-5 dark:border-[#2B3831] dark:bg-[#18211C]">
                <h2 class="font-serif text-base font-bold">Profil Lokasi &amp; Ukuran Denah</h2>
                <form wire:submit="saveInstitution" class="mt-3 space-y-3">
                    <div>
                        <label class="block text-xs font-bold">Nama Instansi / Gedung</label>
                        <input type="text" wire:model="inst_name" required
                            class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-3 py-2 text-xs outline-none dark:border-[#2B3831] dark:bg-[#202B25]">
                    </div>
                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-xs font-bold">Kota</label>
                            <input type="text" wire:model="inst_city" required
                                class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-3 py-2 text-xs outline-none dark:border-[#2B3831] dark:bg-[#202B25]">
                        </div>
                        <div>
                            <label class="block text-xs font-bold">Jenis Gedung</label>
                            <input type="text" wire:model="inst_building_type" required
                                class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-3 py-2 text-xs outline-none dark:border-[#2B3831] dark:bg-[#202B25]">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold">Alamat</label>
                        <input type="text" wire:model="inst_address" required
                            class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-3 py-2 text-xs outline-none dark:border-[#2B3831] dark:bg-[#202B25]">
                    </div>
                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-xs font-bold">Kolom Peta (X)</label>
                            <input type="number" min="3" max="10" wire:model="map_cols"
                                class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-3 py-2 font-mono text-xs outline-none dark:border-[#2B3831] dark:bg-[#202B25]">
                        </div>
                        <div>
                            <label class="block text-xs font-bold">Baris Peta (Y)</label>
                            <input type="number" min="2" max="8" wire:model="map_rows"
                                class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-3 py-2 font-mono text-xs outline-none dark:border-[#2B3831] dark:bg-[#202B25]">
                        </div>
                    </div>
                    <button type="submit"
                        class="w-full rounded-xl bg-[#1C241C] px-4 py-2 text-xs font-bold text-[#CAFFA1] dark:bg-[#CAFFA1] dark:text-[#1C241C]">
                        Simpan Profil &amp; Grid Peta
                    </button>
                </form>
            </section>

            @if ($institution)
                <section class="rounded-2xl border border-[#D7CFB8] bg-[#FBF8EE] p-5 dark:border-[#2B3831] dark:bg-[#18211C]">
                    <h2 class="font-serif text-base font-bold">Generate Unit Massal ke Peta</h2>
                    <p class="mt-0.5 text-[11px] text-[#4B5649] dark:text-[#B8C4B5]">Masukkan jumlah riil ruangan/meja/kursi untuk dipetakan otomatis.</p>
                    <form wire:submit="generateBatchUnits" class="mt-3 space-y-2.5">
                        <div class="grid grid-cols-2 gap-2.5 text-xs">
                            <div>
                                <label class="block font-bold">Ruang Kelompok</label>
                                <input type="number" min="0" max="20" wire:model="batch_group_rooms"
                                    class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-3 py-1.5 font-mono dark:border-[#2B3831] dark:bg-[#202B25]">
                            </div>
                            <div>
                                <label class="block font-bold">Meja Indoor</label>
                                <input type="number" min="0" max="40" wire:model="batch_indoor_desks"
                                    class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-3 py-1.5 font-mono dark:border-[#2B3831] dark:bg-[#202B25]">
                            </div>
                            <div>
                                <label class="block font-bold">Meja Outdoor</label>
                                <input type="number" min="0" max="40" wire:model="batch_outdoor_desks"
                                    class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-3 py-1.5 font-mono dark:border-[#2B3831] dark:bg-[#202B25]">
                            </div>
                            <div>
                                <label class="block font-bold">Kursi Baca</label>
                                <input type="number" min="0" max="40" wire:model="batch_reading_chairs"
                                    class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-3 py-1.5 font-mono dark:border-[#2B3831] dark:bg-[#202B25]">
                            </div>
                        </div>
                        <button type="submit"
                            class="w-full rounded-xl border border-[#1B5E3A]/30 bg-[#CAFFA1]/70 px-4 py-2 text-xs font-bold text-[#1C241C] hover:bg-[#CAFFA1] dark:bg-[#202B25] dark:text-[#CAFFA1]">
                            Petakan Otomatis ke Denah
                        </button>
                    </form>
                </section>

                <section class="rounded-2xl border border-[#D7CFB8] bg-[#FBF8EE] p-5 dark:border-[#2B3831] dark:bg-[#18211C]">
                    <h2 class="font-serif text-base font-bold">{{ $editingUnitId ? 'Edit Unit Posisi' : 'Tambah / Atur Unit Manual' }}</h2>
                    <form wire:submit="saveUnit" class="mt-3 space-y-2.5 text-xs">
                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="block font-bold">Kode Unit</label>
                                <input type="text" wire:model="code" required placeholder="Meja11"
                                    class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-3 py-2 font-mono dark:border-[#2B3831] dark:bg-[#202B25]">
                                @error('code') <p class="mt-1 text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block font-bold">Zona</label>
                                <select wire:model="zone"
                                    class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-2.5 py-2 dark:border-[#2B3831] dark:bg-[#202B25]">
                                    @foreach ($zones as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold">Nama Deskriptif</label>
                            <input type="text" wire:model="name" required placeholder="Meja Baca Jendela Timur"
                                class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-3 py-2 dark:border-[#2B3831] dark:bg-[#202B25]">
                        </div>

                        <div class="grid grid-cols-4 gap-2">
                            <div>
                                <label class="block font-bold">Kap.</label>
                                <input type="number" min="1" max="50" wire:model="capacity"
                                    class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-2 py-2 font-mono dark:border-[#2B3831] dark:bg-[#202B25]">
                            </div>
                            <div>
                                <label class="block font-bold">Durasi</label>
                                <input type="number" min="15" max="480" wire:model="max_duration_minutes"
                                    class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-2 py-2 font-mono dark:border-[#2B3831] dark:bg-[#202B25]">
                            </div>
                            <div>
                                <label class="block font-bold">Kol (X)</label>
                                <input type="number" min="1" max="10" wire:model="pos_x"
                                    class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-2 py-2 font-mono dark:border-[#2B3831] dark:bg-[#202B25]">
                            </div>
                            <div>
                                <label class="block font-bold">Bar (Y)</label>
                                <input type="number" min="1" max="8" wire:model="pos_y"
                                    class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-2 py-2 font-mono dark:border-[#2B3831] dark:bg-[#202B25]">
                            </div>
                        </div>

                        <div class="flex gap-2 pt-1">
                            <button type="submit"
                                class="flex-1 rounded-xl bg-[#1B5E3A] px-4 py-2 font-bold text-[#CAFFA1] dark:bg-[#CAFFA1] dark:text-[#1C241C]">
                                {{ $editingUnitId ? 'Simpan Unit' : 'Simpan ke Peta' }}
                            </button>
                            @if ($editingUnitId)
                                <button type="button" wire:click="cancelEdit"
                                    class="rounded-xl border border-[#D7CFB8] px-3 py-2 font-bold">Batal</button>
                            @endif
                        </div>
                    </form>
                </section>
            @endif
        </div>

        <div class="space-y-6 lg:col-span-8">
            @if ($institution)
                <section class="rounded-2xl border border-[#D7CFB8] bg-[#FBF8EE] p-5 dark:border-[#2B3831] dark:bg-[#18211C]">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-[#D7CFB8] pb-3 dark:border-[#2B3831]">
                        <div>
                            <h2 class="font-serif text-lg font-bold">Blueprint Denah Lokasi 2D ({{ $institution->map_cols }}x{{ $institution->map_rows }})</h2>
                            <p class="text-[11px] text-[#4B5649] dark:text-[#B8C4B5]">Klik kotak kosong untuk memilih koordinat, atau klik unit untuk mengedit posisi/kapasitasnya.</p>
                        </div>
                        <span class="rounded-md bg-[#EFEAD8] px-2.5 py-1 font-mono text-[11px] font-bold dark:bg-[#202B25]">
                            Total: {{ $units->count() }} unit
                        </span>
                    </div>

                    <div class="mt-4 overflow-x-auto">
                        <div class="grid min-w-[540px] gap-2.5" style="grid-template-columns: repeat({{ $institution->map_cols }}, minmax(0, 1fr));">
                            @for ($y = 1; $y <= $institution->map_rows; $y++)
                                @for ($x = 1; $x <= $institution->map_cols; $x++)
                                    @php $u = $gridMap->get("{$x},{$y}"); @endphp
                                    @if ($u)
                                        <button type="button" wire:click="editUnit({{ $u->id }})"
                                            class="flex flex-col justify-between rounded-xl border p-2.5 text-left transition {{ $editingUnitId === $u->id ? 'border-[#1B5E3A] bg-[#CAFFA1]/70 ring-2 ring-[#1B5E3A]' : 'border-[#D7CFB8] bg-[#EFEAD8]/70 hover:border-[#1B5E3A] dark:border-[#2B3831] dark:bg-[#202B25]' }}">
                                            <div class="flex items-center justify-between">
                                                <span class="font-mono text-xs font-bold">{{ $u->code }}</span>
                                                <span class="font-mono text-[9px] text-[#4B5649] dark:text-[#9AA897]">({{ $x }},{{ $y }})</span>
                                            </div>
                                            <span class="mt-1 text-[10px] text-[#4B5649] dark:text-[#B8C4B5]">{{ $u->zoneLabel() }}</span>
                                        </button>
                                    @else
                                        <button type="button" wire:click="pickCell({{ $x }}, {{ $y }})"
                                            class="flex h-16 flex-col items-center justify-center rounded-xl border border-dashed border-[#D7CFB8] text-[10px] font-mono text-[#4B5649]/70 transition hover:border-[#1B5E3A] hover:bg-[#CAFFA1]/25 dark:border-[#2B3831] dark:text-[#7F8C7C]">
                                            + ({{ $x }},{{ $y }})
                                        </button>
                                    @endif
                                @endfor
                            @endfor
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-[#D7CFB8] bg-[#FBF8EE] p-5 dark:border-[#2B3831] dark:bg-[#18211C]">
                    <h2 class="font-serif text-base font-bold">Daftar Detail Unit Fasilitas</h2>
                    <div class="mt-3 overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-[#D7CFB8] text-[#4B5649] dark:border-[#2B3831] dark:text-[#9AA897]">
                                    <th class="pb-2 font-bold">Kode &amp; Posisi</th>
                                    <th class="pb-2 font-bold">Nama &amp; Zona</th>
                                    <th class="pb-2 font-bold">Kapasitas</th>
                                    <th class="pb-2 font-bold">Status</th>
                                    <th class="pb-2 text-right font-bold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#D7CFB8]/60 dark:divide-[#2B3831]">
                                @foreach ($units as $unit)
                                    <tr>
                                        <td class="py-2.5 font-mono font-bold">{{ $unit->code }} <span class="text-[10px] text-[#4B5649]">({{ $unit->pos_x }},{{ $unit->pos_y }})</span></td>
                                        <td class="py-2.5">
                                            <div class="font-semibold">{{ $unit->name }}</div>
                                            <div class="text-[10px] text-[#4B5649] dark:text-[#9AA897]">{{ $unit->zoneLabel() }}</div>
                                        </td>
                                        <td class="py-2.5 font-mono">{{ $unit->capacity }} org &middot; {{ $unit->max_duration_minutes }}m</td>
                                        <td class="py-2.5">
                                            <button type="button" wire:click="toggleActive({{ $unit->id }})"
                                                class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $unit->is_active ? 'bg-[#CAFFA1] text-[#1C241C]' : 'bg-[#EFEAD8] text-[#4B5649]' }}">
                                                {{ $unit->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </button>
                                        </td>
                                        <td class="py-2.5 text-right">
                                            <button type="button" wire:click="editUnit({{ $unit->id }})" class="font-bold text-[#1B5E3A] hover:underline dark:text-[#CAFFA1]">Edit</button>
                                            <button type="button" wire:click="deleteUnit({{ $unit->id }})" class="ml-2 font-bold text-rose-600 hover:underline">Hapus</button>
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
