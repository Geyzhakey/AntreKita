<div wire:poll.10s class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded-md bg-[#90DEC3] px-2.5 py-0.5 font-mono text-[10px] font-bold text-[#0E241E]">LIVE MONITORING</span>
                @if ($institution?->registration_code)
                    <span class="surface-subtle rounded-md px-2.5 py-0.5 font-mono text-[10px] font-bold dark:text-[#90DEC3]">
                        {{ $institution->registration_code }}
                    </span>
                @endif
            </div>
            <h1 class="mt-2 text-3xl font-extrabold tracking-[-0.025em]">{{ $institution?->name }}</h1>
            <p class="mt-1 text-xs text-[#47625A] dark:text-[#98B4AC]">Pemantauan status Available vs In Used secara real-time di seluruh lantai &amp; zona fasilitas.</p>
        </div>

        <div class="flex items-center gap-2.5">
            @if ($floors->count() > 1)
                <select wire:model.live="floorFilter" class="surface-card rounded-lg px-4 py-2 text-xs font-semibold outline-none">
                    <option value="all">Semua Lantai</option>
                    @foreach ($floors as $fl)
                        <option value="{{ $fl }}">Lantai {{ $fl }}</option>
                    @endforeach
                </select>
            @endif
            <a href="{{ route('admin.setup') }}" wire:navigate
                class="btn-brand rounded-lg px-5 py-2.5 text-xs font-semibold shadow-xs">
                Kelola Peta &amp; Unit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="surface-card rounded-2xl p-4">
            <p class="text-xs font-semibold text-[#47625A] dark:text-[#98B4AC]">Total Unit Aktif</p>
            <p class="mt-1 font-mono text-2xl font-extrabold tabular-nums">{{ $totalUnits }}</p>
        </div>
        <div class="rounded-2xl border border-[#0F6E56]/20 bg-[#90DEC3]/55 p-4 text-[#0E241E]">
            <p class="text-xs font-bold">Available</p>
            <p class="mt-1 font-mono text-2xl font-extrabold tabular-nums">{{ $availableCount }}</p>
        </div>
        <div class="rounded-2xl border border-rose-500/25 bg-rose-500/12 p-4 text-rose-800 dark:text-rose-200">
            <p class="text-xs font-bold">In Used</p>
            <p class="mt-1 font-mono text-2xl font-extrabold tabular-nums">{{ $activeCount }}</p>
        </div>
        <div class="rounded-2xl border border-amber-500/25 bg-amber-500/12 p-4 text-amber-800 dark:text-amber-200">
            <p class="text-xs font-bold">Waiting List</p>
            <p class="mt-1 font-mono text-2xl font-extrabold tabular-nums">{{ $waitingTotal }}</p>
        </div>
    </div>

    <section class="surface-card rounded-2xl p-5">
        <h2 class="text-base font-bold tracking-tight">Sesi Berjalan &amp; Waiting List</h2>
        <div class="mt-3 overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-[#CBE8DC] text-[#47625A] dark:border-[#213531] dark:text-[#98B4AC]">
                        <th class="pb-2.5 font-semibold">Unit</th>
                        <th class="pb-2.5 font-semibold">Pengguna / Kelompok</th>
                        <th class="pb-2.5 font-semibold">Slot Waktu</th>
                        <th class="pb-2.5 font-semibold">Status</th>
                        <th class="pb-2.5 text-right font-semibold">Tindakan Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#CBE8DC]/70 dark:divide-[#213531]">
                    @forelse ($activeBookings as $b)
                        <tr>
                            <td class="py-3 font-mono font-bold">
                                {{ $b->facilityUnit->code }}
                                <span class="ml-1 rounded bg-[#E2F3EC] px-1.5 py-0.5 text-[10px] font-semibold text-[#47625A] dark:bg-[#192825] dark:text-[#98B4AC]">
                                    Lt.{{ $b->facilityUnit->floor }}
                                </span>
                            </td>
                            <td class="py-3">
                                <div class="font-semibold">{{ $b->group_name }} ({{ $b->people_count }} org)</div>
                                <div class="font-mono text-[11px] text-[#47625A] dark:text-[#98B4AC]">{{ $b->user->whatsapp_number ?? $b->user->email }}</div>
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
                                    class="surface-subtle rounded-lg px-3.5 py-1 text-[11px] font-semibold hover:border-[#0F6E56] dark:hover:border-[#90DEC3]">
                                    Selesaikan Sesi
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-[#47625A]">Belum ada booking aktif atau waiting list saat ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="space-y-5">
        @foreach ($zones as $zoneKey => $zoneTitle)
            @php $zoneUnits = $unitsByZone->get($zoneKey, collect()); @endphp
            @if ($zoneUnits->isNotEmpty())
                <section class="surface-card rounded-2xl p-5">
                    <div class="flex items-center justify-between border-b border-[#CBE8DC] pb-3 dark:border-[#213531]">
                        <h2 class="text-base font-bold tracking-tight">{{ $zoneTitle }}</h2>
                        <span class="font-mono text-xs text-[#47625A] dark:text-[#98B4AC]">{{ $zoneUnits->count() }} unit</span>
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
</div>
