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
        <div class="pointer-events-auto rounded-xl border border-[#1B5E3A]/30 bg-[#FBF8EE] p-3.5 shadow-lg dark:border-[#CAFFA1]/30 dark:bg-[#18211C]">
            <div class="flex items-start gap-3">
                <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#25D366] text-[#FBF8EE]">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
                    </svg>
                </span>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-xs font-bold text-[#1C241C] dark:text-[#F3EFE4]" x-text="toast.title"></p>
                        <button type="button" @click="toasts = toasts.filter(t => t.id !== toast.id)" class="text-xs text-[#4B5649] hover:text-[#1C241C] dark:text-[#9AA897]">&times;</button>
                    </div>
                    <p x-show="toast.recipient" class="font-mono text-[11px] font-bold text-[#1B5E3A] dark:text-[#CAFFA1]" x-text="toast.recipient"></p>
                    <p class="mt-1 text-xs leading-relaxed text-[#4B5649] dark:text-[#B8C4B5]" x-text="toast.message"></p>
                </div>
            </div>
        </div>
    </template>
</div>
