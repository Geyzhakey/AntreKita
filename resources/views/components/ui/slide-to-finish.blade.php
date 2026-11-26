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
            ? 'border-[#0F6E56]/40 bg-[#90DEC3]/35 dark:border-[#90DEC3]/40 dark:bg-[#192825]'
            : 'border-[#CBE8DC] bg-[#E2F3EC] opacity-75 dark:border-[#213531] dark:bg-[#131F1D]'"
    >
        <div
            class="absolute inset-y-0 left-0 bg-[#90DEC3]/40 transition-all duration-75"
            :style="`width: ${progress}%`"
        ></div>

        <div
            @mousedown="startDrag($event)"
            @touchstart.passive="startDrag($event)"
            :style="`transform: translateX(${(progress / 100) * Math.max(120, ($refs.track?.clientWidth || 280) - 60)}px)`"
            class="relative z-10 flex h-11 w-12 items-center justify-center rounded-lg shadow-xs transition-transform duration-75"
            :class="completed
                ? 'bg-emerald-500 text-[#F7FCFA]'
                : (enabled ? 'cursor-grab active:cursor-grabbing bg-[#0F6E56] text-[#90DEC3] dark:bg-[#90DEC3] dark:text-[#0B1F19]' : 'cursor-not-allowed bg-[#CBE8DC] text-[#47625A] dark:bg-[#213531] dark:text-[#98B4AC]')"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </div>

        <div class="pointer-events-none relative z-0 flex-1 text-center text-xs font-semibold tracking-tight"
            :class="enabled ? 'text-[#0E241E] dark:text-[#90DEC3]' : 'text-[#47625A] dark:text-[#98B4AC]'">
            <span x-show="!completed && enabled">Geser ke kanan jika {{ $unitCode }} sudah rapi &amp; selesai &raquo;</span>
            <span x-show="!completed && !enabled">Centang daftar etiket kebersihan terlebih dahulu</span>
            <span x-show="completed" class="text-emerald-700 dark:text-emerald-300">Sesi Selesai &mdash; Memperbarui Status Unit...</span>
        </div>
    </div>
</div>
