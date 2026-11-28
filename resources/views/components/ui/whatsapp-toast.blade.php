<div
    x-data="{
        toasts: [],
        push(detail) {
            const payload = Array.isArray(detail) ? detail[0] : detail;
            if (!payload || !payload.message) return;
            const id = Date.now() + Math.random();
            this.toasts.push({
                id,
                title: payload.title || 'Notifikasi AntreKita',
                recipient: payload.recipient || '',
                message: payload.message,
            });
            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }, 7000);
        }
    }"
    @wa-notify.window="push($event.detail)"
    class="pointer-events-none fixed right-4 bottom-4 z-50 flex w-full max-w-sm flex-col gap-2.5"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div class="surface-card ak-fade-up pointer-events-auto rounded-2xl border-[#0F6E56]/25 p-3.5 shadow-lg backdrop-blur-xl dark:border-[#90DEC3]/25">
            <div class="flex items-start gap-3">
                <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-[#0F6E56] text-[#90DEC3] dark:bg-[#90DEC3] dark:text-[#0B1F19]">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="4" width="20" height="16" rx="3" />
                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-xs font-bold text-[#0E241E] dark:text-[#E6F2EE]" x-text="toast.title"></p>
                        <button type="button" @click="toasts = toasts.filter(t => t.id !== toast.id)" class="text-xs text-[#47625A] transition hover:text-[#0E241E] dark:text-[#98B4AC]">&times;</button>
                    </div>
                    <p x-show="toast.recipient" class="font-mono text-[11px] font-bold text-[#0F6E56] dark:text-[#90DEC3]" x-text="toast.recipient"></p>
                    <p class="mt-1 text-xs leading-relaxed text-[#47625A] dark:text-[#98B4AC]" x-text="toast.message"></p>
                </div>
            </div>
        </div>
    </template>
</div>
