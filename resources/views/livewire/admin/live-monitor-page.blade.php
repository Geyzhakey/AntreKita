<div wire:poll.10s class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded bg-[#CAFFA1] px-2 py-0.5 font-mono text-[10px] font-bold text-[#1C241C]">PILAR 02 &middot; LIVE MONITORING</span>
                @if ($institution?->registration_code)
                    <span class="rounded border border-[#D7CFB8] bg-[#EFEAD8] px-2 py-0.5 font-mono text-[10px] font-bold dark:border-[#2B3831] dark:bg-[#202B25] dark:text-[#CAFFA1]">
                        {{ $institution->registration_code }}
                    </span>
                @endif
            </div>
            <h1 class="mt-1.5 font-serif text-3xl font-bold tracking-tight text-[#1C241C] dark:text-[#F3EFE4]">{{ $institution?->name }}</h1>
            <p class="mt-1 text-xs text-[#4B5649] dark:text-[#B8C4B5]">Pemantauan status [Available] vs [In Used] secara real-time per zona fasilitas.</p>
        </div>
        <a href="{{ route('admin.setup') }}" wire:navigate
            class="rounded-xl bg-[#1B5E3A] px-4 py-2 text-xs font-bold text-[#CAFFA1] hover:bg-[#14472C] dark:bg-[#CAFFA1] dark:text-[#1C241C]">
            Kelola Peta &amp; Unit
        </a>
    </div>

    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="rounded-xl border border-[#D7CFB8] bg-[#FBF8EE] p-4 dark:border-[#2B3831] dark:bg-[#18211C]">
            <p class="text-xs font-semibold text-[#4B5649] dark:text-[#9AA897]">Total Unit Aktif</p>
            <p class="mt-1 font-mono text-2xl font-bold">{{ $totalUnits }}</p>
        </div>
        <div class="rounded-xl border border-[#1B5E3A]/30 bg-[#CAFFA1]/60 p-4 dark:border-emerald-700 dark:bg-emerald-950/50">
            <p class="text-xs font-bold text-[#1B5E3A] dark:text-emerald-300">Available</p>
            <p class="mt-1 font-mono text-2xl font-bold text-[#1C241C] dark:text-emerald-200">{{ $availableCount }}</p>
        </div>
        <div class="rounded-xl border border-rose-300 bg-rose-100/70 p-4 dark:border-rose-800 dark:bg-rose-950/40">
            <p class="text-xs font-bold text-rose-800 dark:text-rose-300">In Used</p>
            <p class="mt-1 font-mono text-2xl font-bold text-rose-950 dark:text-rose-200">{{ $activeCount }}</p>
        </div>
        <div class="rounded-xl border border-amber-300 bg-amber-100/70 p-4 dark:border-amber-700 dark:bg-amber-950/40">
            <p class="text-xs font-bold text-amber-900 dark:text-amber-300">Waiting List</p>
            <p class="mt-1 font-mono text-2xl font-bold text-amber-950 dark:text-amber-200">{{ $waitingTotal }}</p>
        </div>
    </div>

    <div class="space-y-5">
        @foreach ($zones as $zoneKey => $zoneTitle)
            @php $zoneUnits = $unitsByZone->get($zoneKey, collect()); @endphp
            @if ($zoneUnits->isNotEmpty())
                <section class="rounded-2xl border border-[#D7CFB8] bg-[#FBF8EE] p-5 dark:border-[#2B3831] dark:bg-[#18211C]">
                    <div class="flex items-center justify-between border-b border-[#D7CFB8] pb-3 dark:border-[#2B3831]">
                        <h2 class="font-serif text-base font-bold">{{ $zoneTitle }}</h2>
                        <span class="font-mono text-xs text-[#4B5649] dark:text-[#9AA897]">{{ $zoneUnits->count() }} unit</span>
                    </div>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
                        @foreach ($zoneUnits as $unit)
                            <x-ui.spatial-unit-card
                                :unit="$unit"
                                :waiting-count="$unit->waitingBookings->count()"
                            />
                        @endforeach
                    </div>
                </section>
            @endif
        @endforeach
    </div>

    <section class="rounded-2xl border border-[#D7CFB8] bg-[#FBF8EE] p-5 dark:border-[#2B3831] dark:bg-[#18211C]">
        <h2 class="font-serif text-base font-bold">Sesi Berjalan &amp; Waiting List</h2>
        <div class="mt-3 overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-[#D7CFB8] text-[#4B5649] dark:border-[#2B3831] dark:text-[#9AA897]">
                        <th class="pb-2.5 font-bold">Unit</th>
                        <th class="pb-2.5 font-bold">Pengguna / Kelompok</th>
                        <th class="pb-2.5 font-bold">Slot Waktu</th>
                        <th class="pb-2.5 font-bold">Status</th>
                        <th class="pb-2.5 text-right font-bold">Tindakan Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#D7CFB8]/60 dark:divide-[#2B3831]">
                    @forelse ($activeBookings as $b)
                        <tr>
                            <td class="py-3 font-mono font-bold">{{ $b->facilityUnit->code }}</td>
                            <td class="py-3">
                                <div class="font-bold">{{ $b->group_name }} ({{ $b->people_count }} org)</div>
                                <div class="font-mono text-[11px] text-[#4B5649] dark:text-[#9AA897]">{{ $b->user->whatsapp_number ?? $b->user->email }}</div>
                            </td>
                            <td class="py-3 font-mono">{{ $b->starts_at->format('H:i') }} - {{ $b->ends_at->format('H:i') }}</td>
                            <td class="py-3">
                                @if ($b->status === 'active')
                                    <x-ui.status-badge :status="$b->isInPreExpiryWindow() ? 'warning' : 'in_used'"
                                        :label="$b->isInPreExpiryWindow() ? 'H-5 Menit' : 'Aktif'" />
                                @else
                                    <x-ui.status-badge status="waiting" label="Waiting List" />
                                @endif
                            </td>
                            <td class="py-3 text-right">
                                <button type="button" wire:click="forceFinish({{ $b->id }})"
                                    class="rounded-lg border border-[#D7CFB8] bg-[#EFEAD8] px-2.5 py-1 text-[11px] font-bold hover:border-[#1B5E3A] dark:border-[#2B3831] dark:bg-[#202B25]">
                                    Selesaikan Sesi
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-[#4B5649]">Belum ada booking aktif atau waiting list saat ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
