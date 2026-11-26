<div wire:poll.10s class="space-y-5">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('visitor.institutions') }}" wire:navigate class="text-xs font-semibold text-[#0F6E56] hover:underline dark:text-[#90DEC3]">
                &larr; Ganti Lokasi Instansi
            </a>
            <div class="mt-1.5 flex flex-wrap items-center gap-2.5">
                <h1 class="text-2xl font-extrabold tracking-[-0.025em] sm:text-3xl">{{ $institution->name }}</h1>
                @if ($institution->registration_code)
                    <span class="rounded-md bg-[#90DEC3] px-2.5 py-0.5 font-mono text-[10px] font-bold text-[#0E241E]">
                        {{ $institution->registration_code }}
                    </span>
                @endif
            </div>
            <p class="mt-0.5 text-xs text-[#47625A] dark:text-[#98B4AC]">
                {{ $institution->building_type }} &middot; {{ $institution->city }} &mdash; {{ $institution->address }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <div class="surface-subtle inline-flex rounded-lg p-1">
                <button type="button" wire:click="$set('viewMode', 'map')"
                    class="rounded-md px-3.5 py-1.5 text-xs font-semibold transition {{ $viewMode === 'map' ? 'bg-[#0F6E56] text-[#F7FCFA] shadow-xs dark:bg-[#90DEC3] dark:text-[#0B1F19]' : 'text-[#47625A] dark:text-[#98B4AC]' }}">
                    Peta Denah 2D
                </button>
                <button type="button" wire:click="$set('viewMode', 'list')"
                    class="rounded-md px-3.5 py-1.5 text-xs font-semibold transition {{ $viewMode === 'list' ? 'bg-[#0F6E56] text-[#F7FCFA] shadow-xs dark:bg-[#90DEC3] dark:text-[#0B1F19]' : 'text-[#47625A] dark:text-[#98B4AC]' }}">
                    Daftar Zona
                </button>
            </div>

            <select wire:model.live="statusFilter"
                class="surface-card rounded-lg px-3.5 py-2 text-xs font-semibold outline-none">
                <option value="all">Semua Status</option>
                <option value="available">Available</option>
                <option value="in_used">In Used</option>
            </select>
        </div>
    </div>

    @if ($userActiveBooking)
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-rose-500/30 bg-rose-500/12 p-4 dark:bg-rose-950/35">
            <p class="text-xs font-semibold text-rose-900 dark:text-rose-200">
                Aturan Anti-Monopoli Aktif: Anda sedang memegang <span class="font-mono font-bold">{{ $userActiveBooking->facilityUnit->code }}</span>
                ({{ $userActiveBooking->status === 'active' ? 'Aktif' : 'Waiting List' }}). Selesaikan sesi tersebut sebelum memesan unit lain.
            </p>
            <a href="{{ route('visitor.session') }}" wire:navigate
                class="rounded-lg bg-rose-600 px-4 py-1.5 text-xs font-semibold text-white hover:bg-rose-700">
                Lihat Sesi Saya
            </a>
        </div>
    @endif

    <!-- Floor Switcher + Seat-Type Legend Bar (L = Leseh, S = Standar, H = Tinggi) -->
    <div class="surface-card flex flex-wrap items-center justify-between gap-3 rounded-2xl p-3.5">
        @if ($availableFloors->count() > 1)
            <div class="surface-subtle inline-flex items-center gap-1 rounded-lg p-1">
                @foreach ($availableFloors as $fl)
                    @php $stats = $floorStats[$fl] ?? ['available' => 0, 'total' => 0]; @endphp
                    <button type="button" wire:click="setFloor({{ $fl }})"
                        class="inline-flex items-center gap-2 rounded-md px-4 py-1.5 text-xs font-semibold transition {{ (int) $activeFloor === (int) $fl ? 'bg-[#0F6E56] text-[#F7FCFA] shadow-xs dark:bg-[#90DEC3] dark:text-[#0B1F19]' : 'text-[#47625A] hover:text-[#0E241E] dark:text-[#98B4AC]' }}">
                        <span>Lantai {{ $fl }}</span>
                        <span class="rounded px-1.5 py-0.5 font-mono text-[10px] {{ (int) $activeFloor === (int) $fl ? 'bg-white/20' : 'bg-[#CBE8DC]/70 dark:bg-[#213531]' }}">
                            {{ $stats['available'] }}/{{ $stats['total'] }}
                        </span>
                    </button>
                @endforeach
            </div>
        @else
            <div class="text-xs font-semibold text-[#47625A] dark:text-[#98B4AC]">
                Lantai {{ $activeFloor }} &middot; {{ $floorUnits->count() }} unit tersedia
            </div>
        @endif

        <div class="flex flex-wrap items-center gap-1.5">
            @php
                $seatFilters = [
                    ['key' => 'all',  'badge' => null, 'label' => 'Semua'],
                    ['key' => 'L',    'badge' => 'L',  'label' => 'Meja Leseh'],
                    ['key' => 'S',    'badge' => 'S',  'label' => 'Standar'],
                    ['key' => 'H',    'badge' => 'H',  'label' => 'Meja Tinggi'],
                    ['key' => 'F',    'badge' => 'F',  'label' => 'Ruang Fokus'],
                    ['key' => 'ROOM', 'badge' => 'R',  'label' => 'Ruangan'],
                ];
            @endphp
            @foreach ($seatFilters as $sf)
                @php $hasUnitsOfType = $sf['key'] === 'all' || $floorUnits->contains('seat_type', $sf['key']); @endphp
                @if ($hasUnitsOfType)
                    <button type="button" wire:click="setSeatTypeFilter('{{ $sf['key'] }}')"
                        class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-1 text-[11px] font-semibold transition {{ $seatTypeFilter === $sf['key'] ? 'border-[#0F6E56] bg-[#90DEC3] text-[#0E241E] dark:border-[#90DEC3]' : 'border-[#CBE8DC] bg-[#E2F3EC]/70 text-[#47625A] hover:border-[#0F6E56]/50 dark:border-[#213531] dark:bg-[#192825] dark:text-[#98B4AC]' }}">
                        @if ($sf['badge'])
                            <span class="rounded bg-[#0E241E]/10 px-1 font-mono text-[10px] font-extrabold dark:bg-white/10">{{ $sf['badge'] }}</span>
                        @endif
                        <span>{{ $sf['label'] }}</span>
                    </button>
                @endif
            @endforeach
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-12">
        <div class="space-y-5 {{ $selectedUnit ? 'lg:col-span-8' : 'lg:col-span-12' }}">
            @if ($viewMode === 'map')
                <section class="surface-card rounded-2xl p-5">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-2 border-b border-[#CBE8DC] pb-3.5 dark:border-[#213531]">
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span class="rounded-md bg-[#90DEC3] px-2.5 py-0.5 font-mono text-[10px] font-bold text-[#0E241E]">
                                BLUEPRINT SPASIAL 2D &middot; LT.{{ $activeFloor }}
                            </span>
                            <span class="text-[#47625A] dark:text-[#98B4AC]">
                                Klik langsung meja, kursi, atau ruangan pada denah untuk memesan slot.
                            </span>
                        </div>
                        <div class="flex items-center gap-3 text-[11px] font-semibold">
                            <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span> Tersedia</span>
                            <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span> In Used</span>
                            <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span> H-5 / Antrean</span>
                        </div>
                    </div>

                    @if ($institution->has_custom_floorplan)
                        <x-ui.pnj-floorplan
                            :floor="$activeFloor"
                            :units="$floorUnits"
                            :matching-unit-ids="$matchingUnitIds"
                            :selected-unit-id="$selectedUnitId"
                        />
                    @else
                        <div class="surface-subtle overflow-x-auto rounded-2xl p-4">
                            <div class="grid min-w-[600px] gap-3" style="grid-template-columns: repeat({{ $maxCol }}, minmax(0, 1fr));">
                                @for ($y = 1; $y <= $maxRow; $y++)
                                    @for ($x = 1; $x <= $maxCol; $x++)
                                        @php $unit = $gridMap->get("{$x},{$y}"); @endphp
                                        @if ($unit)
                                            <x-ui.spatial-unit-card
                                                wire:key="map-unit-{{ $unit->id }}"
                                                wire:click="selectUnit({{ $unit->id }})"
                                                :unit="$unit"
                                                :selected="$selectedUnitId === $unit->id"
                                                :waiting-count="$unit->waitingBookings->count()"
                                            />
                                        @else
                                            <div class="flex min-h-[92px] items-center justify-center rounded-2xl border border-dashed border-[#CBE8DC] bg-[#F7FCFA]/40 font-mono text-[10px] text-[#47625A]/50 dark:border-[#213531] dark:bg-[#131F1D]/40">
                                                Lorong
                                            </div>
                                        @endif
                                    @endfor
                                @endfor
                            </div>
                        </div>
                    @endif

                    <div class="mt-4 flex flex-wrap items-center justify-between gap-2 border-t border-[#CBE8DC] pt-3 text-[11px] text-[#47625A] dark:border-[#213531] dark:text-[#98B4AC]">
                        <div class="flex flex-wrap items-center gap-3">
                            <span><strong class="font-mono text-[#0E241E] dark:text-[#90DEC3]">L</strong> = Meja Leseh</span>
                            <span><strong class="font-mono text-[#0E241E] dark:text-[#90DEC3]">S</strong> = Meja Standar (Kursi &amp; Meja Umum)</span>
                            <span><strong class="font-mono text-[#0E241E] dark:text-[#90DEC3]">H</strong> = Meja &amp; Kursi Tinggi</span>
                        </div>
                        <span class="font-mono text-[10px]">Live Sync setiap 10 detik</span>
                    </div>
                </section>
            @else
                @forelse ($zones as $zoneKey => $zoneTitle)
                    @php $zoneUnits = $unitsByZone->get($zoneKey, collect()); @endphp
                    @if ($zoneUnits->isNotEmpty())
                        <section class="surface-card rounded-2xl p-5">
                            <div class="flex items-center justify-between border-b border-[#CBE8DC] pb-3 dark:border-[#213531]">
                                <h2 class="text-base font-bold tracking-tight">{{ $zoneTitle }}</h2>
                                <span class="font-mono text-xs text-[#47625A] dark:text-[#98B4AC]">{{ $zoneUnits->count() }} posisi</span>
                            </div>

                            <div class="mt-4 grid gap-3 sm:grid-cols-2 {{ $selectedUnit ? 'xl:grid-cols-3' : 'md:grid-cols-3 xl:grid-cols-4' }}">
                                @foreach ($zoneUnits as $unit)
                                    <x-ui.spatial-unit-card
                                        wire:key="unit-{{ $unit->id }}"
                                        wire:click="selectUnit({{ $unit->id }})"
                                        :unit="$unit"
                                        :selected="$selectedUnitId === $unit->id"
                                        :waiting-count="$unit->waitingBookings->count()"
                                    />
                                @endforeach
                            </div>
                        </section>
                    @endif
                @empty
                    <div class="surface-card rounded-2xl p-8 text-center text-xs text-[#47625A]">
                        Tidak ada unit yang sesuai dengan filter saat ini.
                    </div>
                @endforelse
            @endif
        </div>

        @if ($selectedUnit)
            @php $unitActive = $selectedUnit->activeBooking; @endphp
            <aside class="lg:col-span-4">
                <div class="surface-card sticky top-20 rounded-2xl border-t-4 border-t-[#0F6E56] p-5 dark:border-t-[#90DEC3]">
                    <div class="flex items-start justify-between gap-2 border-b border-[#CBE8DC] pb-3.5 dark:border-[#213531]">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-lg font-extrabold tracking-tight">{{ $selectedUnit->code }}</span>
                                <span class="rounded-md bg-[#90DEC3] px-2 py-0.5 font-mono text-[10px] font-bold text-[#0E241E]">
                                    Lt.{{ $selectedUnit->floor }}
                                </span>
                            </div>
                            <p class="mt-0.5 text-xs font-medium text-[#47625A] dark:text-[#98B4AC]">
                                {{ $selectedUnit->name }} &middot; {{ $selectedUnit->seatTypeLabel() }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-ui.status-badge :status="$selectedUnit->currentStatus()" />
                            <button type="button" wire:click="clearSelection"
                                class="surface-subtle flex h-6 w-6 items-center justify-center rounded-md text-xs font-bold text-[#47625A] hover:text-[#0E241E] dark:text-[#98B4AC]"
                                title="Tutup Panel">
                                &times;
                            </button>
                        </div>
                    </div>

                    @if ($unitActive)
                        <div class="mt-4 rounded-xl border border-amber-500/30 bg-amber-500/12 p-3.5 text-xs dark:bg-amber-950/35">
                            <p class="font-bold text-amber-900 dark:text-amber-300">Unit Sedang Digunakan (Waiting List Mode)</p>
                            <p class="mt-1 text-amber-800 dark:text-amber-200">
                                Pengguna aktif selesai pukul <span class="font-mono font-bold">{{ $unitActive->ends_at->format('H:i') }}</span>.
                                Anda dapat memesan slot antrean langsung setelahnya.
                            </p>
                        </div>
                    @endif

                    @if ($bookingError)
                        <div class="mt-4 rounded-xl border border-rose-500/30 bg-rose-500/12 p-3 text-xs font-semibold text-rose-800 dark:bg-rose-950/50 dark:text-rose-200">
                            {{ $bookingError }}
                        </div>
                    @endif

                    <form wire:submit="submitBooking" class="mt-4 space-y-3.5">
                        <div>
                            <label class="block text-xs font-semibold">Nama Pengguna / Kelompok</label>
                            <input type="text" wire:model="group_name" required
                                class="input-field mt-1 w-full rounded-xl px-3 py-2 text-sm">
                            @error('group_name') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold">Jumlah Orang (Maks {{ $selectedUnit->capacity }} org)</label>
                            <input type="number" min="1" max="{{ $selectedUnit->capacity }}" wire:model="people_count" required
                                class="input-field mt-1 w-full rounded-xl px-3 py-2 font-mono text-sm">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold">Jam Mulai</label>
                                <input type="time" wire:model="start_time" required
                                    class="input-field mt-1 w-full rounded-xl px-3 py-2 font-mono text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold">Jam Selesai</label>
                                <input type="time" wire:model="end_time" required
                                    class="input-field mt-1 w-full rounded-xl px-3 py-2 font-mono text-sm">
                            </div>
                        </div>

                        <p class="text-[11px] text-[#47625A] dark:text-[#98B4AC]">
                            Tipe: <strong class="text-[#0E241E] dark:text-[#E6F2EE]">{{ $selectedUnit->seatTypeLabel() }}</strong> &middot; Maks <span class="font-mono font-bold">{{ $selectedUnit->max_duration_minutes }} menit</span>.
                        </p>

                        <button type="submit"
                            class="btn-brand w-full rounded-lg px-4 py-2.5 text-sm font-semibold shadow-xs">
                            {{ $unitActive ? 'Masuk Waiting List' : 'Booking Sekarang' }}
                        </button>
                    </form>
                </div>
            </aside>
        @endif
    </div>
</div>
