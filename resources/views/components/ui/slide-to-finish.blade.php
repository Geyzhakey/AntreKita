@props([
    'enabled' => false,
    'unitCode' => 'Unit',
])

<div
    x-data="{
        dragging: false,
        progress: 0,
        completed: false,
        enabled: @js($enabled),
        startX: 0,
        maxTravel: 0,
        startDrag(e) {
            if (!this.enabled || this.completed) return;
            this.dragging = true;
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            this.startX = clientX;
            this.maxTravel = Math.max(120, this.$refs.track.clientWidth - 56);
        },
        onDrag(e) {
            if (!this.dragging || !this.enabled || this.completed) return;
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const delta = Math.max(0, Math.min(this.maxTravel, clientX - this.startX));
            this.progress = Math.round((delta / this.maxTravel) * 100);
            if (this.progress >= 88) {
                this.completed = true;
                this.dragging = false;
                this.progress = 100;
                $wire.finishSession();
            }
        },
        endDrag() {
            if (!this.dragging) return;
            this.dragging = false;
            if (!this.completed) {
                this.progress = 0;
            }
        }
    }"
    x-effect="enabled = @js($enabled)"
    @mousemove.window="onDrag($event)"
    @mouseup.window="endDrag()"
    @touchmove.window="onDrag($event)"
    @touchend.window="endDrag()"
    class="select-none"
>
    <div
        x-ref="track"
        class="relative flex h-14 w-full items-center overflow-hidden rounded-xl border p-1.5 transition"
        :class="enabled
            ? 'border-[#1B5E3A] bg-[#CAFFA1]/35 dark:border-[#CAFFA1]/50 dark:bg-[#202B25]'
            : 'border-[#D7CFB8] bg-[#EFEAD8] opacity-75 dark:border-[#2B3831] dark:bg-[#18211C]'"
    >
        <div
            class="absolute inset-y-0 left-0 bg-[#10B981]/20 transition-all duration-75"
            :style="`width: ${progress}%`"
        ></div>

        <div
            @mousedown="startDrag($event)"
            @touchstart.passive="startDrag($event)"
            :style="`transform: translateX(${(progress / 100) * Math.max(120, ($refs.track?.clientWidth || 280) - 60)}px)`"
            class="relative z-10 flex h-11 w-12 items-center justify-center rounded-lg shadow-xs transition-transform duration-75"
            :class="completed
                ? 'bg-[#10B981] text-[#FBF8EE]'
                : (enabled ? 'cursor-grab active:cursor-grabbing bg-[#1B5E3A] text-[#CAFFA1] dark:bg-[#CAFFA1] dark:text-[#1C241C]' : 'cursor-not-allowed bg-[#D7CFB8] text-[#4B5649] dark:bg-[#2B3831] dark:text-[#7F8C7C]')"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </div>

        <div class="pointer-events-none relative z-0 flex-1 text-center text-xs font-bold tracking-wide"
            :class="enabled ? 'text-[#1C241C] dark:text-[#CAFFA1]' : 'text-[#4B5649] dark:text-[#9AA897]'">
            <span x-show="!completed && enabled">Geser ke kanan jika {{ $unitCode }} sudah rapi &amp; selesai &raquo;</span>
            <span x-show="!completed && !enabled">Centang daftar kebersihan di atas terlebih dahulu</span>
            <span x-show="completed" class="text-emerald-800 dark:text-emerald-300">Sesi Selesai &mdash; Memperbarui Status Unit...</span>
        </div>
    </div>
</div>
