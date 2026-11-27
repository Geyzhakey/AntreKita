<div wire:poll.5s="checkRealtimeStatus" wire:key="booking-status-{{ $booking?->id ?? 'none' }}-{{ $booking?->status ?? 'idle' }}" class="space-y-6">
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
                            status: '{{ $booking->status }}',
                            startsAt: {{ $booking->starts_at->timestamp }},
                            endsAt: {{ $booking->ends_at->timestamp }},
                            started: false,
                            display: '00:00:00',
                            tick() {
                                const now = Math.floor(Date.now() / 1000);
                                this.started = now >= this.startsAt;

                                // ketika waktunya belum mulai, timernya tidak berjalan
                                if (this.status !== 'active' || !this.started) {
                                    const totalDuration = Math.max(0, this.endsAt - this.startsAt);
                                    const h = String(Math.floor(totalDuration / 3600)).padStart(2, '0');
                                    const m = String(Math.floor((totalDuration % 3600) / 60)).padStart(2, '0');
                                    const s = String(totalDuration % 60).padStart(2, '0');
                                    this.display = `${h}:${m}:${s}`;
                                    return;
                                }

                                // kalau waktunya sudah mulai, timernya berjalan
                                const diff = Math.max(0, this.endsAt - now);
                                const h = String(Math.floor(diff / 3600)).padStart(2, '0');
                                const m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
                                const s = String(diff % 60).padStart(2, '0');
                                this.display = `${h}:${m}:${s}`;
                            }
                        }"
                        x-init="tick(); setInterval(() => tick(), 1000)"
                        class="surface-subtle mt-5 rounded-2xl p-5"
                    >
                        <p class="text-xs font-semibold text-[#47625A] dark:text-[#98B4AC]"
                           x-text="status === 'active' && started ? 'Sisa Waktu Penggunaan (Live Countdown)' : 'Durasi Total Reservasi (belum berjalan)'">
                        </p>
                        <div class="mt-1.5 flex flex-wrap items-baseline justify-between gap-2">
                            <span class="font-mono text-3xl font-extrabold tracking-tight tabular-nums" x-text="display">00:00:00</span>
                            <span class="rounded-md bg-[#90DEC3] px-3 py-1 font-mono text-xs font-bold text-[#0E241E]">
                                Slot: {{ $booking->starts_at->format('H:i') }} - {{ $booking->ends_at->format('H:i') }}
                            </span>
                        </div>
                    </div>

                    @if ($booking->status === 'active')
                        <div class="surface-subtle mt-4 rounded-2xl p-4 text-xs">
                            <p class="font-bold">
                                {{ $nextWaiting ? 'Pengantre Berikutnya: ' . $nextWaiting->group_name : 'Belum ada pengguna di Waiting List' }}
                            </p>
                            <p class="mt-0.5 text-[11px] text-[#47625A] dark:text-[#98B4AC]">
                                Pada H-5 menit sebelum selesai, pengingat otomatis terkirim ke WhatsApp &amp; Email secara real-time.
                            </p>
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
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-[#CBE8DC] pb-3.5 dark:border-[#213531]">
                    <div>
                        <h2 class="text-base font-bold tracking-tight">Log Notifikasi WhatsApp &amp; Email</h2>
                        <p class="text-[11px] text-[#47625A] dark:text-[#98B4AC]">Status pengiriman langsung dari Sistem AntreKita.</p>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button type="button" wire:click="toggleGatewaySetup"
                            class="rounded-md px-2 py-0.5 font-mono text-[10px] font-bold transition hover:opacity-80 {{ $waConfigured ? 'bg-[#25D366]/20 text-[#0F6E56] dark:text-[#90DEC3]' : 'bg-amber-500/15 text-amber-800 dark:text-amber-300' }}">
                            WA: {{ $waConfigured ? 'Aktif' : 'Belum Setup' }}
                        </button>
                        <button type="button" wire:click="toggleGatewaySetup"
                            class="rounded-md px-2 py-0.5 font-mono text-[10px] font-bold transition hover:opacity-80 {{ $emailConfigured ? 'bg-[#25D366]/20 text-[#0F6E56] dark:text-[#90DEC3]' : 'bg-amber-500/15 text-amber-800 dark:text-amber-300' }}">
                            SMTP: {{ $emailConfigured ? 'Aktif' : 'Belum Setup' }}
                        </button>
                    </div>
                </div>

                @if (! $waConfigured || ! $emailConfigured || $showGatewaySetup)
                    <div class="surface-subtle mt-4 space-y-4 rounded-xl border border-[#CBE8DC] p-4 text-xs dark:border-[#213531]">
                        <div class="flex items-center justify-between gap-2">
                            <p class="font-bold text-[#0F6E56] dark:text-[#90DEC3]">Hubungkan Pengiriman Otomatis (1-Klik di Browser)</p>
                            @if ($waConfigured && $emailConfigured)
                                <button type="button" wire:click="toggleGatewaySetup" class="text-[11px] font-semibold text-[#47625A] hover:underline dark:text-[#98B4AC]">Tutup</button>
                            @endif
                        </div>

                        @if ($gatewayError)
                            <div class="rounded-lg bg-rose-500/10 px-3 py-2 text-[11px] font-semibold text-rose-700 dark:text-rose-300">
                                {{ $gatewayError }}
                            </div>
                        @endif

                        {{-- WhatsApp Built-in Gateway --}}
                        <div class="rounded-xl border border-[#CBE8DC] bg-[#F7FCFA]/80 p-3 dark:border-[#213531] dark:bg-[#0D1514]/60">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-semibold">1. WhatsApp Otomatis</span>
                                @if ($waConfigured)
                                    <span class="rounded bg-emerald-500/15 px-2 py-0.5 font-mono text-[10px] font-bold text-emerald-800 dark:text-emerald-300">
                                        Terhubung{{ !empty($localWa['phone']) ? ': +' . $localWa['phone'] : '' }}
                                    </span>
                                @else
                                    <span class="rounded bg-amber-500/15 px-2 py-0.5 font-mono text-[10px] font-bold text-amber-800 dark:text-amber-300">
                                        {{ $localWa['running'] ? 'Menunggu Scan' : 'Gateway Offline' }}
                                    </span>
                                @endif
                            </div>

                            @if ($waConfigured)
                                <div class="mt-2 flex items-center justify-between gap-2 text-[11px] text-[#47625A] dark:text-[#98B4AC]">
                                    <span>Bot WhatsApp aktif dan mengirim pesan secara otomatis.</span>
                                    <button type="button" wire:click="resetWaGateway" class="font-semibold text-rose-600 hover:underline dark:text-rose-400">
                                        Ganti Perangkat
                                    </button>
                                </div>
                            @elseif (! $localWa['running'])
                                <p class="mt-1.5 text-[11px] text-[#47625A] dark:text-[#98B4AC]">
                                    Gateway lokal sedang bersiap. Halaman ini akan terhubung otomatis dalam beberapa detik.
                                </p>
                            @else
                                @if (! empty($localWa['qr']))
                                    <div class="mt-2.5 flex flex-col items-center gap-2 rounded-lg bg-[#EEF8F4] p-3 dark:bg-[#131F1D]">
                                        <img src="{{ $localWa['qr'] }}" alt="QR Tautkan WhatsApp AntreKita" class="h-44 w-44 rounded-lg border border-[#CBE8DC]">
                                        <p class="text-center text-[11px] text-[#47625A] dark:text-[#98B4AC]">
                                            Buka <strong>WhatsApp di HP &rsaquo; Perangkat Tertaut &rsaquo; Tautkan Perangkat</strong> lalu scan QR ini. Pesan tertunda langsung terkirim otomatis!
                                        </p>
                                    </div>
                                @else
                                    <p class="mt-2 text-[11px] text-[#47625A] dark:text-[#98B4AC]">Menyiapkan QR Code WhatsApp... ({{ $localWa['status'] }})</p>
                                @endif

                                <div class="mt-2.5 flex flex-wrap items-center gap-2">
                                    <input type="text" wire:model="wa_pairing_phone" placeholder="0812..."
                                        class="input-field flex-1 rounded-lg px-2.5 py-1.5 font-mono text-[11px]">
                                    <button type="button" wire:click="requestWaPairingCode"
                                        class="rounded-lg bg-[#0F6E56] px-2.5 py-1.5 text-[11px] font-semibold text-[#F7FCFA] hover:opacity-90 dark:bg-[#90DEC3] dark:text-[#0B1F19]">
                                        Minta Kode 8-Digit
                                    </button>
                                </div>
                                @if ($wa_pairing_code || !empty($localWa['pairingCode']))
                                    <div class="mt-2 rounded-lg bg-[#90DEC3]/30 px-3 py-2 text-center font-mono text-sm font-extrabold tracking-widest text-[#0E241E] dark:text-[#90DEC3]">
                                        KODE TAUTAN: {{ $wa_pairing_code ?: $localWa['pairingCode'] }}
                                    </div>
                                @endif
                            @endif
                        </div>

                        {{-- Email SMTP 1-Click --}}
                        <div class="rounded-xl border border-[#CBE8DC] bg-[#F7FCFA]/80 p-3 dark:border-[#213531] dark:bg-[#0D1514]/60">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-semibold">2. Email Gmail Otomatis</span>
                                @if ($emailConfigured)
                                    <span class="rounded bg-emerald-500/15 px-2 py-0.5 font-mono text-[10px] font-bold text-emerald-800 dark:text-emerald-300">SMTP Aktif</span>
                                @else
                                    <a href="https://myaccount.google.com/apppasswords" target="_blank" rel="noopener noreferrer"
                                        class="text-[10px] font-semibold text-[#0F6E56] underline dark:text-[#90DEC3]">
                                        Buat App Password Google &nearr;
                                    </a>
                                @endif
                            </div>

                            <form wire:submit="saveSmtpInline" class="mt-2 space-y-2">
                                <input type="email" wire:model="smtp_username" required placeholder="Email Gmail pengirim (contoh: nama@gmail.com)"
                                    class="input-field w-full rounded-lg px-2.5 py-1.5 text-[11px]">
                                <input type="password" wire:model="smtp_password" required placeholder="16-Digit App Password Google"
                                    class="input-field w-full rounded-lg px-2.5 py-1.5 font-mono text-[11px]">
                                @error('smtp_username') <p class="text-[10px] text-rose-600">{{ $message }}</p> @enderror
                                @error('smtp_password') <p class="text-[10px] text-rose-600">{{ $message }}</p> @enderror
                                <button type="submit"
                                    class="btn-brand w-full rounded-lg px-3 py-1.5 text-[11px] font-semibold">
                                    Simpan &amp; Kirim Email Sekarang
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                <div class="mt-4 space-y-2.5">
                    @forelse ($logs as $log)
                        @php
                            $logStatus = $log->status ?? 'sent';
                            $badgeClass = match ($logStatus) {
                                'sent' => 'bg-emerald-500/15 text-emerald-800 dark:text-emerald-300',
                                'failed' => 'bg-rose-500/15 text-rose-800 dark:text-rose-300',
                                default => 'bg-amber-500/15 text-amber-800 dark:text-amber-300',
                            };
                            $badgeLabel = match ($logStatus) {
                                'sent' => 'Terkirim',
                                'failed' => 'Gagal',
                                default => 'Perlu Setup',
                            };
                        @endphp
                        <div class="surface-subtle rounded-xl p-3.5 text-xs">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="font-mono text-[11px] font-bold {{ in_array($log->channel, ['whatsapp', 'account'], true) ? 'text-[#0F6E56] dark:text-[#90DEC3]' : 'text-[#47625A] dark:text-[#98B4AC]' }}">
                                    {{ $log->channel === 'account' ? 'AKUN' : strtoupper($log->channel) }} &rarr; {{ $log->channel === 'account' ? '@' . $log->recipient : $log->recipient }}
                                </span>
                                <div class="flex items-center gap-1.5">
                                    <span class="rounded px-1.5 py-0.5 font-mono text-[10px] font-bold {{ $badgeClass }}">{{ $badgeLabel }}</span>
                                    <span class="font-mono text-[10px] text-[#47625A] dark:text-[#98B4AC]">{{ $log->sent_at->format('H:i:s') }}</span>
                                </div>
                            </div>
                            <p class="mt-1 leading-relaxed">{{ $log->message }}</p>
                            @if ($log->error_message)
                                <p class="mt-1.5 rounded-lg bg-rose-500/10 px-2.5 py-1 font-mono text-[10px] text-rose-700 dark:text-rose-300">
                                    {{ $log->error_message }}
                                </p>
                            @endif
                            @if ($logStatus !== 'sent' || $log->channel === 'whatsapp')
                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    @if ($logStatus !== 'sent')
                                        <button type="button" wire:click="retryNotification({{ $log->id }})"
                                            class="rounded-md border border-[#0F6E56]/40 px-2 py-0.5 text-[10px] font-semibold text-[#0F6E56] hover:bg-[#90DEC3]/25 dark:border-[#90DEC3]/40 dark:text-[#90DEC3]">
                                            Kirim Ulang
                                        </button>
                                    @endif
                                    @if ($log->channel === 'whatsapp' && $logStatus !== 'sent')
                                        <a href="https://wa.me/{{ preg_replace('/[^\d]/', '', $log->recipient) }}?text={{ urlencode($log->message) }}"
                                            target="_blank" rel="noopener noreferrer"
                                            class="rounded-md bg-[#25D366]/20 px-2 py-0.5 text-[10px] font-semibold text-[#0F6E56] hover:bg-[#25D366]/30 dark:text-[#90DEC3]">
                                            Kirim via WA &nearr;
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="py-6 text-center text-xs text-[#47625A]">Belum ada notifikasi yang dikirimkan.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
