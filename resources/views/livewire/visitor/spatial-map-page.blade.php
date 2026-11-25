<div wire:poll.10s class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('visitor.institutions') }}" wire:navigate class="text-xs font-bold text-[#14452F] hover:underline dark:text-[#CAFFA1]">
                &larr; Ganti Lokasi Instansi
            </a>
            <div class="mt-1.5 flex flex-wrap items-center gap-2">
                <h1 class="font-serif text-3xl font-bold tracking-tight">{{ $institution->name }}</h1>
                @if ($institution->registration_code)
                    <span class="rounded-md bg-[#CAFFA1] px-2 py-0.5 font-mono text-[11px] font-bold text-[#1A241E]">
                        {{ $institution->registration_code }}
                    </span>
                @endif
            </div>
            <p class="mt-0.5 text-xs text-[#4E5C52] dark:text-[#B5C4B6]">{{ $institution->building_type }} &middot; {{ $institution->city }} &mdash; {{ $institution->address }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <div class="inline-flex rounded-xl border border-[#D9D1BA] bg-[#EFEAD8] p-1 dark:border-[#27382F] dark:bg-[#1C2922]">
                <button type="button" wire:click="$set('viewMode', 'map')"
                    class="rounded-lg px-3 py-1 text-xs font-bold transition {{ $viewMode === 'map' ? 'bg-[#CAFFA1] text-[#1A241E]' : 'text-[#4E5C52] dark:text-[#B5C4B6]' }}">
                    Peta Denah 2D
                </button>
                <button type="button" wire:click="$set('viewMode', 'list')"
                    class="rounded-lg px-3 py-1 text-xs font-bold transition {{ $viewMode === 'list' ? 'bg-[#CAFFA1] text-[#1A241E]' : 'text-[#4E5C52] dark:text-[#B5C4B6]' }}">
                    Tampilan Zona
                </button>
            </div>

            <select wire:model.live="zoneFilter"
                class="paper-card rounded-xl px-3 py-2 text-xs font-bold outline-none">
                <option value="all">Semua Zona</option>
                @foreach ($zones as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>

            <select wire:model.live="statusFilter"
                class="paper-card rounded-xl px-3 py-2 text-xs font-bold outline-none">
                <option value="all">Semua Status</option>
                <option value="available">Available</option>
                <option value="in_used">In Used</option>
            </select>
        </div>
    </div>

    @if ($userActiveBooking)
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-rose-300 bg-rose-100/80 p-4 dark:border-rose-800 dark:bg-rose-950/50">
            <p class="text-xs font-bold text-rose-900 dark:text-rose-200">
                Aturan Anti-Monopoli Aktif: Anda sedang memegang <span class="font-mono">{{ $userActiveBooking->facilityUnit->code }}</span>
                ({{ $userActiveBooking->status === 'active' ? 'Aktif' : 'Waiting List' }}). Selesaikan sesi tersebut sebelum memesan unit lain.
            </p>
            <a href="{{ route('visitor.session') }}" wire:navigate
                class="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-rose-700">
                Lihat Sesi Saya
            </a>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-12">
        <div class="space-y-5 {{ $selectedUnit ? 'lg:col-span-8' : 'lg:col-span-12' }}">
            @if ($viewMode === 'map')
                <section class="paper-card rounded-3xl p-5">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-[#D9D1BA] pb-3.5 dark:border-[#27382F]">
                        <div class="flex items-center gap-2 text-xs font-bold">
                            <span class="rounded bg-[#CAFFA1] px-2.5 py-0.5 font-mono text-[10px] text-[#1A241E]">DENAH ARSITEKTUR 2D</span>
                            <span class="text-[#4E5C52] dark:text-[#B5C4B6]">Klik kursi, meja, atau ruangan yang dituju</span>
                        </div>
                        <div class="flex items-center gap-3 text-[11px] font-bold">
                            <span class="inline-flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-[#10B981]"></span> Available</span>
                            <span class="inline-flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-[#F43F5E]"></span> In Used</span>
                            <span class="inline-flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-[#F59E0B]"></span> H-5 / Waiting</span>
                        </div>
                    </div>

                    <div class="mt-4 overflow-x-auto rounded-2xl border border-[#D9D1BA] bg-[#EFEAD8]/50 p-4 dark:border-[#27382F] dark:bg-[#1C2922]/50">
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
                                        <div class="flex min-h-[92px] items-center justify-center rounded-2xl border border-dashed border-[#D9D1BA]/70 bg-[#FAF7EC]/40 font-mono text-[10px] text-[#4E5C52]/50 dark:border-[#27382F] dark:bg-[#151F1A]/40">
                                            Lorong
                                        </div>
                                    @endif
                                @endfor
                            @endfor
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-between border-t border-[#D9D1BA] pt-3 font-mono text-[10px] font-bold text-[#4E5C52] dark:border-[#27382F] dark:text-[#9CB09E]">
                        <span>[ PINTU MASUK UTAMA &amp; RESEPSIONIS ]</span>
                        <span>ARAH UTARA &uarr;</span>
                    </div>
                </section>
            @else
                @forelse ($zones as $zoneKey => $zoneTitle)
                    @php $zoneUnits = $unitsByZone->get($zoneKey, collect()); @endphp
                    @if ($zoneUnits->isNotEmpty())
                        <section class="paper-card rounded-3xl p-5">
                            <div class="flex items-center justify-between border-b border-[#D9D1BA] pb-3 dark:border-[#27382F]">
                                <h2 class="font-serif text-lg font-bold">{{ $zoneTitle }}</h2>
                                <span class="font-mono text-xs text-[#4E5C52] dark:text-[#9CB09E]">{{ $zoneUnits->count() }} posisi</span>
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
                    <div class="paper-card rounded-2xl p-8 text-center text-xs text-[#4E5C52]">
                        Tidak ada unit yang sesuai dengan filter saat ini.
                    </div>
                @endforelse
            @endif
        </div>

        @if ($selectedUnit)
            @php $unitActive = $selectedUnit->activeBooking; @endphp
            <aside class="lg:col-span-4">
                <div class="paper-card sticky top-24 rounded-3xl p-5">
                    <div class="flex items-start justify-between gap-2 border-b border-[#D9D1BA] pb-3 dark:border-[#27382F]">
                        <div>
                            <span class="font-mono text-lg font-bold">{{ $selectedUnit->code }}</span>
                            <p class="text-xs text-[#4E5C52] dark:text-[#B5C4B6]">{{ $selectedUnit->name }} &middot; {{ $selectedUnit->zoneLabel() }}</p>
                        </div>
                        <x-ui.status-badge :status="$selectedUnit->currentStatus()" />
                    </div>

                    @if ($unitActive)
                        <div class="mt-4 rounded-xl border border-amber-300 bg-amber-100/70 p-3.5 text-xs dark:border-amber-700 dark:bg-amber-950/40">
                            <p class="font-bold text-amber-950 dark:text-amber-300">Unit Sedang Digunakan (Waiting List Mode)</p>
                            <p class="mt-1 text-amber-900 dark:text-amber-200">
                                Pengguna aktif selesai pukul <span class="font-mono font-bold">{{ $unitActive->ends_at->format('H:i') }}</span>.
                                Anda dapat memesan slot langsung setelahnya.
                            </p>
                        </div>
                    @endif

                    @if ($bookingError)
                        <div class="mt-4 rounded-xl border border-rose-300 bg-rose-100/80 p-3 text-xs font-bold text-rose-800 dark:border-rose-800 dark:bg-rose-950/60 dark:text-rose-200">
                            {{ $bookingError }}
                        </div>
                    @endif

                    <form wire:submit="submitBooking" class="mt-4 space-y-3.5">
                        <div>
                            <label class="block text-xs font-bold">Nama Pengguna / Kelompok</label>
                            <input type="text" wire:model="group_name" required
                                class="mt-1 w-full rounded-xl border border-[#D9D1BA] bg-[#EFEAD8]/60 px-3 py-2 text-sm outline-none focus:border-[#14452F] dark:border-[#27382F] dark:bg-[#1C2922]">
                            @error('group_name') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold">Jumlah Orang (Maks {{ $selectedUnit->capacity }} org)</label>
                            <input type="number" min="1" max="{{ $selectedUnit->capacity }}" wire:model="people_count" required
                                class="mt-1 w-full rounded-xl border border-[#D9D1BA] bg-[#EFEAD8]/60 px-3 py-2 font-mono text-sm outline-none focus:border-[#14452F] dark:border-[#27382F] dark:bg-[#1C2922]">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold">Jam Mulai</label>
                                <input type="time" wire:model="start_time" required
                                    class="mt-1 w-full rounded-xl border border-[#D9D1BA] bg-[#EFEAD8]/60 px-3 py-2 font-mono text-sm outline-none focus:border-[#14452F] dark:border-[#27382F] dark:bg-[#1C2922]">
                            </div>
                            <div>
                                <label class="block text-xs font-bold">Jam Selesai</label>
                                <input type="time" wire:model="end_time" required
                                    class="mt-1 w-full rounded-xl border border-[#D9D1BA] bg-[#EFEAD8]/60 px-3 py-2 font-mono text-sm outline-none focus:border-[#14452F] dark:border-[#27382F] dark:bg-[#1C2922]">
                            </div>
                        </div>

                        <p class="text-[11px] text-[#4E5C52] dark:text-[#9CB09E]">
                            Batas durasi maksimal: <span class="font-mono font-bold">{{ $selectedUnit->max_duration_minutes }} menit</span>.
                        </p>

                        <button type="submit"
                            class="w-full rounded-xl bg-[#14452F] px-4 py-2.5 text-sm font-bold text-[#CAFFA1] transition hover:bg-[#0F3524] dark:bg-[#CAFFA1] dark:text-[#1A241E]">
                            {{ $unitActive ? 'Masuk Waiting List' : 'Booking Sekarang' }}
                        </button>
                    </form>
                </div>
            </aside>
        @endif
    </div>
</div>
