<div wire:poll.10s="pollNotifications" class="relative" @click.outside="$wire.set('open', false)">
    <button
        type="button"
        wire:click="toggleOpen"
        class="surface-subtle relative inline-flex h-8 w-8 items-center justify-center rounded-lg text-[#0E241E] transition hover:border-[#0F6E56]/50 dark:text-[#90DEC3] dark:hover:border-[#90DEC3]/50"
        title="Pesan Akun AntreKita"
    >
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M13.73 21a2 2 0 0 1-3.46 0" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        @if ($unreadCount > 0)
            <span class="absolute -top-1 -right-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-[#0F6E56] px-1 font-mono text-[9px] font-bold text-[#F7FCFA] dark:bg-[#90DEC3] dark:text-[#0B1F19]">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    @if ($open)
        <div class="surface-card absolute right-0 z-50 mt-2 w-80 overflow-hidden rounded-2xl border border-[#CBE8DC] shadow-xl sm:w-96 dark:border-[#213531]">
            <div class="flex items-center justify-between border-b border-[#CBE8DC] bg-[#EEF8F4] px-4 py-3 dark:border-[#213531] dark:bg-[#131F1D]">
                <div>
                    <h3 class="text-xs font-bold text-[#0E241E] dark:text-[#E6F2EE]">Pesan Akun</h3>
                    <p class="text-[10px] text-[#47625A] dark:text-[#98B4AC]">Notifikasi reservasi &amp; pengingat H-5 menit</p>
                </div>
                @if ($accountLogs->isNotEmpty())
                    <button type="button" wire:click="markAllAsRead"
                        class="text-[10px] font-semibold text-[#0F6E56] hover:underline dark:text-[#90DEC3]">
                        Tandai semua dibaca
                    </button>
                @endif
            </div>

            <div class="max-h-80 divide-y divide-[#CBE8DC]/70 overflow-y-auto dark:divide-[#213531]">
                @forelse ($accountLogs as $log)
                    <div class="px-4 py-3 text-xs {{ $log->read_at === null ? 'bg-[#90DEC3]/15 dark:bg-[#90DEC3]/10' : '' }}">
                        <div class="flex items-center justify-between gap-2">
                            <span class="inline-flex items-center gap-1.5 font-mono text-[10px] font-bold text-[#0F6E56] dark:text-[#90DEC3]">
                                @if ($log->read_at === null)
                                    <span class="h-1.5 w-1.5 rounded-full bg-[#0F6E56] dark:bg-[#90DEC3]"></span>
                                @endif
                                {{ match ($log->trigger_type) {
                                    'h_minus_5_active' => 'PENGINGAT H-5 MENIT',
                                    'h_minus_5_waiting' => 'GILIRAN MENDEKAT',
                                    'handover_ready' => 'UNIT SIAP',
                                    default => 'KONFIRMASI',
                                } }}
                            </span>
                            <span class="font-mono text-[10px] text-[#47625A] dark:text-[#98B4AC]">
                                {{ $log->sent_at?->format('H:i') }} WIB
                            </span>
                        </div>
                        <p class="mt-1 leading-relaxed text-[#0E241E] dark:text-[#E6F2EE]">{{ $log->message }}</p>
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-xs text-[#47625A] dark:text-[#98B4AC]">
                        Belum ada pesan masuk di akun Anda.
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div>
