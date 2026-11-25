<div wire:poll.5s class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <span class="rounded bg-[#CAFFA1] px-2.5 py-0.5 font-mono text-[10px] font-bold text-[#1A241E]">KARTU BACA DIGITAL &middot; HANDOVER H-5 MENIT</span>
            <h1 class="mt-1.5 font-serif text-3xl font-bold tracking-tight">Sesi Aktif &amp; Antrean Anda</h1>
        </div>
        <a href="{{ route('visitor.institutions') }}" wire:navigate
            class="paper-card rounded-xl px-4 py-2 text-xs font-bold hover:border-[#14452F] dark:hover:border-[#CAFFA1]">
            Lihat Semua Lokasi
        </a>
    </div>

    @if (! $booking)
        <div class="paper-card rounded-3xl p-8 text-center">
            <h2 class="font-serif text-xl font-bold">Anda Tidak Memiliki Booking Aktif</h2>
            <p class="mt-1 text-xs text-[#4E5C52] dark:text-[#B5C4B6]">Pilih fasilitas dan posisi meja/kursi pada denah spasial untuk memulai sesi baru.</p>
            <a href="{{ route('visitor.institutions') }}" wire:navigate
                class="mt-4 inline-flex rounded-xl bg-[#14452F] px-5 py-2.5 text-sm font-bold text-[#CAFFA1] hover:bg-[#0F3524] dark:bg-[#CAFFA1] dark:text-[#1A241E]">
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
                <div class="paper-card rounded-3xl p-6 {{ $isPreExpiry ? 'ring-2 ring-amber-400' : '' }}">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-bold text-[#4E5C52] dark:text-[#9CB09E]">{{ $booking->institution->name }}</p>
                            <h2 class="mt-0.5 font-mono text-2xl font-bold">{{ $booking->facilityUnit->code }}</h2>
                            <p class="text-xs text-[#4E5C52] dark:text-[#B5C4B6]">{{ $booking->facilityUnit->name }} &middot; {{ $booking->group_name }} ({{ $booking->people_count }} org)</p>
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
                        class="mt-5 rounded-2xl border border-[#D9D1BA] bg-[#EFEAD8]/75 p-4 dark:border-[#27382F] dark:bg-[#1C2922]"
                    >
                        <p class="text-xs font-bold text-[#4E5C52] dark:text-[#9CB09E]">
                            {{ $booking->status === 'active' ? 'Sisa Waktu Penggunaan (Live Countdown)' : 'Estimasi Giliran Anda Dimulai Dalam' }}
                        </p>
                        <div class="mt-1 flex flex-wrap items-baseline justify-between gap-2">
                            <span class="font-mono text-3xl font-bold tracking-tight" x-text="display">00:00:00</span>
                            <span class="rounded-md bg-[#CAFFA1] px-2.5 py-0.5 font-mono text-xs font-bold text-[#1A241E]">
                                Slot: {{ $booking->starts_at->format('H:i') }} - {{ $booking->ends_at->format('H:i') }}
                            </span>
                        </div>
                    </div>

                    @if ($booking->status === 'active')
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-[#D9D1BA] bg-[#EFEAD8]/50 p-3.5 text-xs dark:border-[#27382F] dark:bg-[#1C2922]">
                            <div>
                                <p class="font-bold">
                                    {{ $nextWaiting ? 'User Waiting List Berikutnya: ' . $nextWaiting->group_name : 'Belum ada pengguna di Waiting List' }}
                                </p>
                                <p class="text-[11px] text-[#4E5C52] dark:text-[#B5C4B6]">
                                    Pada H-5 menit sebelum selesai, sistem mengirim WA &amp; Email otomatis ke kedua pihak.
                                </p>
                            </div>
                            <button type="button" wire:click="simulateHMinus5"
                                class="rounded-lg border border-amber-400 bg-amber-100 px-3 py-1.5 text-xs font-bold text-amber-950 hover:bg-amber-200 dark:border-amber-700 dark:bg-amber-950/60 dark:text-amber-300">
                                Uji Trigger H-5 Menit
                            </button>
                        </div>

                        <div class="mt-6 space-y-3 border-t border-dashed border-[#D9D1BA] pt-5 dark:border-[#27382F]">
                            <p class="text-xs font-bold">Etiquette Checklist Sebelum Akhiri Sesi</p>
                            <label class="flex items-center gap-2.5 text-xs">
                                <input type="checkbox" wire:model.live="checkDeskClean" class="rounded border-[#D9D1BA] text-[#14452F]">
                                Meja sudah bersih dari sampah atau sisa makanan/minuman
                            </label>
                            <label class="flex items-center gap-2.5 text-xs">
                                <input type="checkbox" wire:model.live="checkChairsArranged" class="rounded border-[#D9D1BA] text-[#14452F]">
                                Kursi sudah dirapikan kembali ke posisi semula
                            </label>
                            <label class="flex items-center gap-2.5 text-xs">
                                <input type="checkbox" wire:model.live="checkPersonalItems" class="rounded border-[#D9D1BA] text-[#14452F]">
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
                        <div class="mt-5 flex items-center justify-between rounded-xl border border-amber-300 bg-amber-100/70 p-4 text-xs dark:border-amber-700 dark:bg-amber-950/40">
                            <p class="text-amber-950 dark:text-amber-200">
                                Menunggu giliran <span class="font-mono font-bold">{{ $booking->facilityUnit->code }}</span>. Anda dapat membatalkan antrean jika berhalangan.
                            </p>
                            <button type="button" wire:click="finishSession"
                                class="rounded-lg border border-rose-300 bg-[#FAF7EC] px-3 py-1.5 font-bold text-rose-700 hover:bg-rose-50 dark:bg-[#151F1A] dark:text-rose-300">
                                Batalkan Antrean
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <div class="paper-card rounded-3xl p-5 lg:col-span-5">
                <div class="flex items-center justify-between border-b border-[#D9D1BA] pb-3 dark:border-[#27382F]">
                    <div>
                        <h2 class="font-serif text-base font-bold">Log Notifikasi WhatsApp &amp; Email</h2>
                        <p class="text-[11px] text-[#4E5C52] dark:text-[#9CB09E]">Riwayat pesan otomatis dari Sistem AntreKita.</p>
                    </div>
                    <span class="rounded-md bg-[#25D366]/20 px-2 py-0.5 font-mono text-[10px] font-bold text-[#14452F] dark:text-[#CAFFA1]">WA Gateway</span>
                </div>

                <div class="mt-4 space-y-2.5">
                    @forelse ($logs as $log)
                        <div class="rounded-xl border border-[#D9D1BA] bg-[#EFEAD8]/60 p-3 text-xs dark:border-[#27382F] dark:bg-[#1C2922]">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-mono text-[11px] font-bold {{ $log->channel === 'whatsapp' ? 'text-[#14452F] dark:text-[#CAFFA1]' : 'text-[#4E5C52] dark:text-[#B5C4B6]' }}">
                                    {{ strtoupper($log->channel) }} &rarr; {{ $log->recipient }}
                                </span>
                                <span class="font-mono text-[10px] text-[#4E5C52] dark:text-[#9CB09E]">{{ $log->sent_at->format('H:i:s') }}</span>
                            </div>
                            <p class="mt-1 leading-relaxed">{{ $log->message }}</p>
                        </div>
                    @empty
                        <p class="py-6 text-center text-xs text-[#4E5C52]">Belum ada notifikasi yang dikirimkan.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
