<div wire:poll.5s class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <span class="rounded-md bg-[#90DEC3] px-2.5 py-1 font-mono text-[10px] font-bold text-[#0E241E]">KARTU BACA DIGITAL &middot; HANDOVER H-5 MENIT</span>
            <h1 class="mt-2 text-3xl font-extrabold tracking-[-0.025em]">Sesi Aktif &amp; Antrean Anda</h1>
        </div>
        <a href="{{ route('visitor.institutions') }}" wire:navigate
            class="surface-card rounded-lg px-4 py-2 text-xs font-semibold transition hover:border-[#0F6E56] dark:hover:border-[#90DEC3]">
            Lihat Semua Lokasi
        </a>
    </div>

    @if (! $booking)
        <div class="surface-card rounded-2xl p-10 text-center">
            <h2 class="text-xl font-bold tracking-tight">Anda Tidak Memiliki Booking Aktif</h2>
            <p class="mt-1.5 text-xs text-[#47625A] dark:text-[#98B4AC]">
                Pilih gedung dan klik meja, kursi, atau ruangan pada denah spasial untuk memulai sesi baru.
            </p>
            <a href="{{ route('visitor.institutions') }}" wire:navigate
                class="btn-brand mt-5 inline-flex rounded-lg px-6 py-2.5 text-sm font-semibold shadow-xs">
                Cari Fasilitas Sekarang
            </a>
        </div>
    @else
        @php
            $targetTimestamp = $booking->status === 'active'
                ? $booking->ends_at->timestamp
                : ($activeHolder?->ends_at->timestamp ?? $booking->starts_at->timestamp);
            $isPreExpiry = $booking->isInPreExpiryWindow();
        @endphp

        <div class="grid gap-6 lg:grid-cols-12">
            <div class="space-y-5 lg:col-span-7">
                <div class="surface-card rounded-2xl border-t-4 border-t-[#0F6E56] p-6 dark:border-t-[#90DEC3] {{ $isPreExpiry ? 'ring-2 ring-amber-400' : '' }}">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold text-[#47625A] dark:text-[#98B4AC]">{{ $booking->institution->name }}</p>
                            <div class="mt-1 flex items-center gap-2">
                                <h2 class="font-mono text-2xl font-extrabold tracking-tight">{{ $booking->facilityUnit->code }}</h2>
                                @if ($booking->facilityUnit->floor)
                                    <span class="rounded-md bg-[#90DEC3] px-2 py-0.5 font-mono text-[10px] font-bold text-[#0E241E]">
                                        Lantai {{ $booking->facilityUnit->floor }}
                                    </span>
                                @endif
                            </div>
                            <p class="mt-0.5 text-xs text-[#47625A] dark:text-[#98B4AC]">
                                {{ $booking->facilityUnit->name }} ({{ $booking->facilityUnit->seatTypeLabel() }}) &middot; {{ $booking->group_name }} ({{ $booking->people_count }} org)
                            </p>
                        </div>

                        @if ($booking->status === 'active')
                            <x-ui.status-badge :status="$isPreExpiry ? 'warning' : 'in_used'"
                                :label="$isPreExpiry ? 'H-5 Menit Berakhir' : 'Sesi Sedang Aktif'" />
                        @else
                            <x-ui.status-badge status="waiting" label="Dalam Waiting List" />
                        @endif
                    </div>

                    <div
                        x-data="{
                            target: {{ $targetTimestamp }},
                            display: '00:00:00',
                            tick() {
                                const diff = Math.max(0, this.target - Math.floor(Date.now() / 1000));
                                const h = String(Math.floor(diff / 3600)).padStart(2, '0');
                                const m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
                                const s = String(diff % 60).padStart(2, '0');
                                this.display = `${h}:${m}:${s}`;
                            }
                        }"
                        x-init="tick(); setInterval(() => tick(), 1000)"
                        x-effect="target = {{ $targetTimestamp }}"
                        class="surface-subtle mt-5 rounded-2xl p-5"
                    >
                        <p class="text-xs font-semibold text-[#47625A] dark:text-[#98B4AC]">
                            {{ $booking->status === 'active' ? 'Sisa Waktu Penggunaan (Live Countdown)' : 'Estimasi Giliran Anda Dimulai Dalam' }}
                        </p>
                        <div class="mt-1.5 flex flex-wrap items-baseline justify-between gap-2">
                            <span class="font-mono text-3xl font-extrabold tracking-tight tabular-nums" x-text="display">00:00:00</span>
                            <span class="rounded-md bg-[#90DEC3] px-3 py-1 font-mono text-xs font-bold text-[#0E241E]">
                                Slot: {{ $booking->starts_at->format('H:i') }} - {{ $booking->ends_at->format('H:i') }}
                            </span>
                        </div>
                    </div>

                    @if ($booking->status === 'active')
                        <div class="surface-subtle mt-4 flex flex-wrap items-center justify-between gap-3 rounded-2xl p-4 text-xs">
                            <div>
                                <p class="font-bold">
                                    {{ $nextWaiting ? 'Pengantre Berikutnya: ' . $nextWaiting->group_name : 'Belum ada pengguna di Waiting List' }}
                                </p>
                                <p class="mt-0.5 text-[11px] text-[#47625A] dark:text-[#98B4AC]">
                                    Pada H-5 menit sebelum selesai, pengingat otomatis terkirim ke kedua pihak.
                                </p>
                            </div>
                            <button type="button" wire:click="simulateHMinus5"
                                class="rounded-lg border border-amber-500/40 bg-amber-500/15 px-3.5 py-1.5 text-xs font-semibold text-amber-900 hover:bg-amber-500/25 dark:text-amber-300">
                                Uji Trigger H-5 Menit
                            </button>
                        </div>

                        <div class="mt-6 space-y-3 border-t border-dashed border-[#CBE8DC] pt-5 dark:border-[#213531]">
                            <p class="text-xs font-bold">Etiquette Checklist Sebelum Akhiri Sesi</p>
                            <label class="flex items-center gap-2.5 text-xs">
                                <input type="checkbox" wire:model.live="checkDeskClean" class="rounded border-[#CBE8DC] text-[#0F6E56]">
                                Meja sudah bersih dari sampah atau sisa makanan/minuman
                            </label>
                            <label class="flex items-center gap-2.5 text-xs">
                                <input type="checkbox" wire:model.live="checkChairsArranged" class="rounded border-[#CBE8DC] text-[#0F6E56]">
                                Kursi / bantal leseh sudah dirapikan kembali ke posisi semula
                            </label>
                            <label class="flex items-center gap-2.5 text-xs">
                                <input type="checkbox" wire:model.live="checkPersonalItems" class="rounded border-[#CBE8DC] text-[#0F6E56]">
                                Seluruh barang pribadi dan perangkat sudah dibawa
                            </label>

                            <div class="pt-2">
                                <x-ui.slide-to-finish
                                    :enabled="$checklistComplete"
                                    :unit-code="$booking->facilityUnit->code"
                                />
                            </div>
                        </div>
                    @else
                        <div class="mt-5 flex items-center justify-between rounded-2xl border border-amber-500/30 bg-amber-500/12 p-4 text-xs dark:bg-amber-950/35">
                            <p class="text-amber-900 dark:text-amber-200">
                                Menunggu giliran <span class="font-mono font-bold">{{ $booking->facilityUnit->code }}</span>. Anda dapat membatalkan antrean jika berhalangan.
                            </p>
                            <button type="button" wire:click="finishSession"
                                class="surface-card rounded-lg px-3.5 py-1.5 font-semibold text-rose-700 hover:border-rose-400 dark:text-rose-300">
                                Batalkan Antrean
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <div class="surface-card rounded-2xl p-5 lg:col-span-5">
                <div class="flex items-center justify-between border-b border-[#CBE8DC] pb-3.5 dark:border-[#213531]">
                    <div>
                        <h2 class="text-base font-bold tracking-tight">Log Notifikasi WhatsApp &amp; Email</h2>
                        <p class="text-[11px] text-[#47625A] dark:text-[#98B4AC]">Riwayat pesan otomatis dari Sistem AntreKita.</p>
                    </div>
                    <span class="rounded-md bg-[#25D366]/20 px-2.5 py-0.5 font-mono text-[10px] font-bold text-[#0F6E56] dark:text-[#90DEC3]">WA Gateway</span>
                </div>

                <div class="mt-4 space-y-2.5">
                    @forelse ($logs as $log)
                        <div class="surface-subtle rounded-xl p-3.5 text-xs">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-mono text-[11px] font-bold {{ $log->channel === 'whatsapp' ? 'text-[#0F6E56] dark:text-[#90DEC3]' : 'text-[#47625A] dark:text-[#98B4AC]' }}">
                                    {{ strtoupper($log->channel) }} &rarr; {{ $log->recipient }}
                                </span>
                                <span class="font-mono text-[10px] text-[#47625A] dark:text-[#98B4AC]">{{ $log->sent_at->format('H:i:s') }}</span>
                            </div>
                            <p class="mt-1 leading-relaxed">{{ $log->message }}</p>
                        </div>
                    @empty
                        <p class="py-6 text-center text-xs text-[#47625A]">Belum ada notifikasi yang dikirimkan.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
