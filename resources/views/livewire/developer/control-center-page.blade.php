<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <span class="rounded-md bg-[#90DEC3] px-2.5 py-0.5 font-mono text-[10px] font-bold uppercase text-[#0E241E]">
                DEVELOPER.AI &middot; OTORITAS PENUH SISTEM
            </span>
            <h1 class="mt-2 text-3xl font-extrabold tracking-[-0.025em]">
                Developer Command Center
            </h1>
            <p class="mt-1 text-xs text-[#47625A] dark:text-[#98B4AC]">
                Inspeksi lengkap akun Administrator, pembekuan sementara, penghapusan terproteksi (5x konfirmasi), dan kendali global website.
            </p>
        </div>

        <div class="flex flex-wrap gap-2.5">
            <div class="surface-card rounded-2xl px-4 py-2.5 text-center">
                <div class="font-mono text-lg font-bold dark:text-[#90DEC3]">{{ $admins->count() }}</div>
                <div class="text-[10px] font-semibold text-[#47625A] dark:text-[#98B4AC]">Instansi Admin</div>
            </div>
            <div class="surface-card rounded-2xl px-4 py-2.5 text-center">
                <div class="font-mono text-lg font-bold dark:text-[#90DEC3]">{{ $totalVisitors }}</div>
                <div class="text-[10px] font-semibold text-[#47625A] dark:text-[#98B4AC]">Visitor Terdaftar</div>
            </div>
            <div class="surface-card rounded-2xl px-4 py-2.5 text-center">
                <div class="font-mono text-lg font-bold dark:text-[#90DEC3]">{{ $activeBookingsCount }}</div>
                <div class="text-[10px] font-semibold text-[#47625A] dark:text-[#98B4AC]">Booking / Antrean</div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-12">
        <div class="space-y-6 lg:col-span-4">
            <section class="surface-card rounded-2xl p-5">
                <h2 class="text-lg font-bold tracking-tight">Terbitkan Akun Administrator</h2>
                <p class="mt-0.5 text-[11px] text-[#47625A] dark:text-[#98B4AC]">Buat akun dan Kode Daftar instansi setelah verifikasi WhatsApp.</p>

                <form wire:submit="createAdminAccount" class="mt-4 space-y-3">
                    <div>
                        <label class="block text-xs font-semibold">Nama Instansi</label>
                        <input type="text" wire:model="institution_name" required placeholder="Perpustakaan Kampus"
                            class="input-field mt-1 w-full rounded-xl px-3 py-2 text-xs">
                        @error('institution_name') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-xs font-semibold">Kode Daftar</label>
                            <input type="text" wire:model="registration_code" required placeholder="LBR2JKT26"
                                class="input-field mt-1 w-full rounded-xl px-3 py-2 font-mono text-xs uppercase">
                            @error('registration_code') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold">Jenis Gedung</label>
                            <input type="text" wire:model="building_type" required placeholder="Perpustakaan"
                                class="input-field mt-1 w-full rounded-xl px-3 py-2 text-xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-xs font-semibold">Nama PIC / Admin</label>
                            <input type="text" wire:model="pic_name" required placeholder="Dr. Rina"
                                class="input-field mt-1 w-full rounded-xl px-3 py-2 text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold">Kota</label>
                            <input type="text" wire:model="city" required placeholder="Depok"
                                class="input-field mt-1 w-full rounded-xl px-3 py-2 text-xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-xs font-semibold">Email Admin</label>
                            <input type="email" wire:model="pic_email" required placeholder="admin@kampus.ac.id"
                                class="input-field mt-1 w-full rounded-xl px-3 py-2 text-xs">
                            @error('pic_email') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold">WhatsApp PIC</label>
                            <input type="text" wire:model="pic_whatsapp" placeholder="0812..."
                                class="input-field mt-1 w-full rounded-xl px-3 py-2 font-mono text-xs">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold">Alamat Gedung</label>
                        <input type="text" wire:model="address" required placeholder="Jl. Kampus Terpadu..."
                            class="input-field mt-1 w-full rounded-xl px-3 py-2 text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold">Tujuan Permintaan</label>
                        <textarea wire:model="purpose" required rows="2" placeholder="Pemetaan kursi & ruang diskusi..."
                            class="input-field mt-1 w-full rounded-xl px-3 py-2 text-xs"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold">Password Instansi</label>
                        <input type="text" wire:model="password" required placeholder="Minimal 6 karakter"
                            class="input-field mt-1 w-full rounded-xl px-3 py-2 font-mono text-xs">
                    </div>

                    <button type="submit"
                        class="btn-brand w-full rounded-lg px-4 py-2.5 text-xs font-semibold shadow-xs">
                        Buat Akun &amp; Terbitkan Kode Daftar
                    </button>
                </form>
            </section>

            <section class="surface-card rounded-2xl p-5">
                <h2 class="text-lg font-bold tracking-tight">Kontrol Penuh Website</h2>
                <form wire:submit="savePlatformSettings" class="mt-4 space-y-3">
                    <div>
                        <label class="block text-xs font-semibold">Nomor WhatsApp Developer Resmi</label>
                        <input type="text" wire:model="developer_whatsapp" required placeholder="081222822531"
                            class="input-field mt-1 w-full rounded-xl px-3 py-2 font-mono text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold">Pengumuman Global (Broadcast Banner)</label>
                        <input type="text" wire:model="global_announcement" placeholder="Kosongkan jika tidak ada pengumuman"
                            class="input-field mt-1 w-full rounded-xl px-3 py-2 text-xs">
                    </div>

                    <div class="surface-subtle space-y-2.5 rounded-xl p-3">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-[#0F6E56] dark:text-[#90DEC3]">Gateway WhatsApp Real-Time</p>
                        <div>
                            <label class="block text-[11px] font-semibold">Provider Gateway</label>
                            <select wire:model="whatsapp_driver" class="input-field mt-1 w-full rounded-xl px-3 py-1.5 text-xs">
                                <option value="fonnte">Fonnte (api.fonnte.com)</option>
                                <option value="wablas">Wablas</option>
                                <option value="waha">WAHA (Self-Hosted)</option>
                                <option value="meta">Meta WhatsApp Cloud API</option>
                                <option value="webhook">Custom Webhook</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold">API Token / Key</label>
                            <input type="password" wire:model="whatsapp_api_token" placeholder="Masukkan token gateway aktif"
                                class="input-field mt-1 w-full rounded-xl px-3 py-1.5 font-mono text-xs">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold">Custom Endpoint URL (Opsional untuk Fonnte)</label>
                            <input type="url" wire:model="whatsapp_api_url" placeholder="https://api.fonnte.com/send"
                                class="input-field mt-1 w-full rounded-xl px-3 py-1.5 font-mono text-xs">
                        </div>
                    </div>

                    <label class="surface-subtle flex items-center justify-between rounded-xl p-3 text-xs font-semibold">
                        <span>Kunci Booking Global (Mode Maintenance)</span>
                        <input type="checkbox" wire:model="booking_maintenance_mode" class="rounded border-[#CBE8DC] text-[#0F6E56]">
                    </label>

                    <button type="submit"
                        class="w-full rounded-lg bg-[#0E241E] px-4 py-2.5 text-xs font-semibold text-[#90DEC3] transition hover:opacity-90 dark:bg-[#90DEC3] dark:text-[#0B1F19]">
                        Terapkan Otoritas Global
                    </button>
                </form>
            </section>
        </div>

        <div class="space-y-6 lg:col-span-8">
            <section class="surface-card rounded-2xl p-5">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#CBE8DC] pb-3.5 dark:border-[#213531]">
                    <div>
                        <h2 class="text-lg font-bold tracking-tight">Direktori Lengkap Akun Administrator</h2>
                        <p class="text-[11px] text-[#47625A] dark:text-[#98B4AC]">Inspeksi detail gedung, bekukan sementara, atau hapus permanen (5x konfirmasi).</p>
                    </div>
                    <input type="text" wire:model.live.debounce.250ms="search" placeholder="Cari instansi / kode..."
                        class="input-field rounded-xl px-3.5 py-1.5 text-xs">
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-[#CBE8DC] text-[#47625A] dark:border-[#213531] dark:text-[#98B4AC]">
                                <th class="pb-2.5 font-semibold">Instansi &amp; Kode</th>
                                <th class="pb-2.5 font-semibold">PIC &amp; Kontak</th>
                                <th class="pb-2.5 font-semibold">Unit</th>
                                <th class="pb-2.5 font-semibold">Status</th>
                                <th class="pb-2.5 text-right font-semibold">Otoritas Developer</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#CBE8DC]/70 dark:divide-[#213531]">
                            @forelse ($admins as $adm)
                                <tr>
                                    <td class="py-3">
                                        <div class="font-bold">{{ $adm->institution_label ?? $adm->institution?->name }}</div>
                                        <div class="mt-0.5 flex items-center gap-1.5">
                                            <span class="rounded-md bg-[#90DEC3] px-2 py-0.5 font-mono text-[10px] font-bold text-[#0E241E]">{{ $adm->registration_code }}</span>
                                            <span class="text-[10px] text-[#47625A] dark:text-[#98B4AC]">{{ $adm->institution?->building_type ?? 'Fasilitas' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="font-semibold">{{ $adm->name }}</div>
                                        <div class="font-mono text-[10px] text-[#47625A] dark:text-[#98B4AC]">{{ $adm->email }} &middot; {{ $adm->whatsapp_number ?? '-' }}</div>
                                    </td>
                                    <td class="py-3 font-mono font-bold">
                                        {{ $adm->institution?->facilityUnits?->count() ?? 0 }}
                                    </td>
                                    <td class="py-3">
                                        @if ($adm->is_frozen)
                                            <x-ui.status-badge status="in_used" label="Dibekukan" />
                                        @else
                                            <x-ui.status-badge status="available" label="Aktif" />
                                        @endif
                                    </td>
                                    <td class="py-3 text-right">
                                        <div class="inline-flex flex-wrap justify-end gap-1.5">
                                            <button type="button" wire:click="openInspect({{ $adm->id }})"
                                                class="surface-subtle rounded-lg px-3 py-1 text-[11px] font-semibold hover:border-[#0F6E56]">
                                                Inspeksi
                                            </button>

                                            @if ($adm->is_frozen)
                                                <button type="button" wire:click="unfreezeAdmin({{ $adm->id }})"
                                                    class="btn-brand rounded-lg px-3 py-1 text-[11px] font-semibold">
                                                    Aktifkan
                                                </button>
                                            @else
                                                <button type="button" wire:click="openFreezeModal({{ $adm->id }})"
                                                    class="rounded-lg border border-amber-500/40 bg-amber-500/15 px-3 py-1 text-[11px] font-semibold text-amber-900 dark:text-amber-300">
                                                    Bekukan
                                                </button>
                                            @endif

                                            <button type="button" wire:click="startDelete({{ $adm->id }})"
                                                class="rounded-lg border border-rose-500/40 bg-rose-500/15 px-3 py-1 text-[11px] font-semibold text-rose-800 dark:text-rose-300">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-[#47625A]">Tidak ada akun Administrator ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="grid gap-6 md:grid-cols-2">
                <section class="surface-card rounded-2xl p-5">
                    <h2 class="text-base font-bold tracking-tight">4 Akun Developer.Ai (Utama &amp; Cadangan)</h2>
                    <div class="mt-3 space-y-2">
                        @foreach ($developers as $dev)
                            <div class="surface-subtle flex items-center justify-between rounded-xl p-2.5 text-xs">
                                <div>
                                    <div class="font-bold">{{ $dev->name }}</div>
                                    <div class="font-mono text-[10px] text-[#47625A] dark:text-[#98B4AC]">{{ $dev->email }}</div>
                                </div>
                                <span class="rounded-md bg-[#90DEC3] px-2.5 py-0.5 font-mono text-[10px] font-bold text-[#0E241E]">{{ $dev->registration_code }}</span>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="surface-card rounded-2xl p-5">
                    <h2 class="text-base font-bold tracking-tight">Log Audit Otoritas Developer</h2>
                    <div class="mt-3 max-h-60 space-y-2 overflow-y-auto pr-1">
                        @foreach ($auditLogs as $log)
                            <div class="surface-subtle rounded-xl p-2.5 text-[11px]">
                                <div class="flex items-center justify-between font-mono text-[10px] font-bold text-[#0F6E56] dark:text-[#90DEC3]">
                                    <span>{{ $log->action }}</span>
                                    <span>{{ $log->created_at->format('d/m H:i') }}</span>
                                </div>
                                <div class="mt-0.5 font-bold">{{ $log->target_label }}</div>
                                <div class="text-[#47625A] dark:text-[#98B4AC]">{{ $log->details }}</div>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- Modal 1: Inspeksi Detail Admin -->
    @if ($inspectAdmin)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/55 p-4 backdrop-blur-sm">
            <div class="surface-card max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl p-6">
                <div class="flex items-start justify-between gap-4 border-b border-[#CBE8DC] pb-3 dark:border-[#213531]">
                    <div>
                        <span class="rounded-md bg-[#90DEC3] px-2.5 py-0.5 font-mono text-[10px] font-bold text-[#0E241E]">INSPEKSI LENGKAP ADMIN</span>
                        <h3 class="mt-1.5 text-xl font-bold tracking-tight">{{ $inspectAdmin->institution_label }}</h3>
                        <p class="font-mono text-xs text-[#47625A] dark:text-[#98B4AC]">Kode Daftar: {{ $inspectAdmin->registration_code }}</p>
                    </div>
                    <button type="button" wire:click="closeInspect" class="text-xl font-bold">&times;</button>
                </div>

                <div class="mt-4 grid gap-3 text-xs sm:grid-cols-2">
                    <div class="surface-subtle rounded-xl p-3.5">
                        <p class="font-bold">Data Pengelola (PIC)</p>
                        <p class="mt-1">Nama: {{ $inspectAdmin->name }}</p>
                        <p>Email: {{ $inspectAdmin->email }}</p>
                        <p>WhatsApp: {{ $inspectAdmin->whatsapp_number ?? '-' }}</p>
                        <p>Status: {{ $inspectAdmin->is_frozen ? 'DIBEKUKAN (' . $inspectAdmin->frozen_reason . ')' : 'AKTIF' }}</p>
                    </div>
                    <div class="surface-subtle rounded-xl p-3.5">
                        <p class="font-bold">Detail Gedung &amp; Tujuan</p>
                        <p class="mt-1">Jenis: {{ $inspectAdmin->institution?->building_type ?? '-' }}</p>
                        <p>Kota: {{ $inspectAdmin->institution?->city ?? '-' }}</p>
                        <p>Alamat: {{ $inspectAdmin->institution?->address ?? '-' }}</p>
                        <p>Tujuan: {{ $inspectAdmin->purpose ?? $inspectAdmin->institution?->purpose ?? '-' }}</p>
                    </div>
                </div>

                @if ($inspectAdmin->institution)
                    <div class="mt-4">
                        <p class="text-xs font-bold">Unit Terpetakan ({{ $inspectAdmin->institution->facilityUnits->count() }})</p>
                        <div class="mt-2 grid max-h-60 grid-cols-2 gap-2 overflow-y-auto sm:grid-cols-3">
                            @foreach ($inspectAdmin->institution->facilityUnits as $u)
                                <div class="surface-subtle rounded-xl p-2 text-[11px]">
                                    <div class="flex items-center justify-between font-mono font-bold">
                                        <span>{{ $u->code }}</span>
                                        <span>Lt.{{ $u->floor }}</span>
                                    </div>
                                    <div class="text-[#47625A] dark:text-[#98B4AC]">{{ $u->seatTypeLabel() }} &middot; {{ $u->capacity }} org</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Modal 2: Pembekuan Sementara -->
    @if ($freezeTarget)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/55 p-4 backdrop-blur-sm">
            <div class="surface-card w-full max-w-md rounded-2xl border-amber-400 p-6">
                <h3 class="text-lg font-bold tracking-tight">Bekukan Akun Sementara</h3>
                <p class="mt-1 text-xs text-[#47625A] dark:text-[#98B4AC]">
                    Akun <strong>{{ $freezeTarget->institution_label }}</strong> ({{ $freezeTarget->registration_code }}) tidak akan bisa login dan seluruh unitnya dinonaktifkan sementara.
                </p>
                <div class="mt-4">
                    <label class="block text-xs font-semibold">Alasan Pembekuan</label>
                    <input type="text" wire:model="freezeReason" placeholder="Contoh: Audit ulang kapasitas gedung..."
                        class="input-field mt-1 w-full rounded-xl px-3 py-2 text-xs">
                </div>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" wire:click="$set('freezeAdminId', null)" class="surface-subtle rounded-lg px-4 py-2 text-xs font-semibold">Batal</button>
                    <button type="button" wire:click="confirmFreeze" class="rounded-lg bg-amber-500 px-4 py-2 text-xs font-bold text-[#0E241E]">Bekukan Sekarang</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal 3: Konfirmasi Penghapusan 5x -->
    @if ($deleteTarget && $deleteStep > 0)
        @php
            $stepMessages = [
                1 => 'Konfirmasi Tahap 1/5: Anda hendak menghapus akun Administrator "' . $deleteTarget->institution_label . '". Lanjutkan ke tahap 2?',
                2 => 'Konfirmasi Tahap 2/5: Seluruh data gedung, pemetaan kursi/ruangan, dan kode daftar ' . $deleteTarget->registration_code . ' akan ikut terhapus.',
                3 => 'Konfirmasi Tahap 3/5: Seluruh riwayat booking aktif dan waiting list visitor di lokasi ini akan hilang permanen.',
                4 => 'Konfirmasi Tahap 4/5: Tindakan ini TIDAK BISA dibatalkan (irreversible). Apakah Anda benar-benar yakin?',
                5 => 'Konfirmasi Final Tahap 5/5: Ketik Kode Daftar "' . $deleteTarget->registration_code . '" untuk membuka tombol Hapus Permanen.',
            ];
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/65 p-4 backdrop-blur-sm">
            <div class="surface-card w-full max-w-md rounded-2xl border-2 border-rose-500 p-6">
                <div class="flex items-center justify-between">
                    <span class="rounded-md bg-rose-600 px-2.5 py-0.5 font-mono text-[10px] font-bold text-white">
                        HAPUS PERMANEN &middot; TAHAP {{ $deleteStep }} / 5
                    </span>
                    <button type="button" wire:click="cancelDelete" class="text-lg font-bold">&times;</button>
                </div>

                <div class="mt-3 flex gap-1">
                    @for ($i = 1; $i <= 5; $i++)
                        <div class="h-1.5 flex-1 rounded-full {{ $i <= $deleteStep ? 'bg-rose-600' : 'bg-[#CBE8DC] dark:bg-[#213531]' }}"></div>
                    @endfor
                </div>

                <p class="mt-4 text-xs font-semibold leading-relaxed">
                    {{ $stepMessages[$deleteStep] }}
                </p>

                @if ($deleteStep === 5)
                    <div class="mt-4">
                        <input type="text" wire:model="deleteConfirmCode" placeholder="Ketik {{ $deleteTarget->registration_code }}"
                            class="w-full rounded-xl border border-rose-400 bg-rose-50 px-3 py-2 font-mono text-sm uppercase text-rose-950 outline-none dark:bg-rose-950/40 dark:text-rose-200">
                        @if ($deleteError)
                            <p class="mt-1 text-[11px] font-bold text-rose-600">{{ $deleteError }}</p>
                        @endif
                    </div>
                @endif

                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" wire:click="cancelDelete"
                        class="surface-subtle rounded-lg px-4 py-2 text-xs font-semibold">
                        Batalkan
                    </button>

                    @if ($deleteStep < 5)
                        <button type="button" wire:click="advanceDeleteStep"
                            class="rounded-lg bg-rose-600 px-4 py-2 text-xs font-semibold text-white hover:bg-rose-700">
                            Ya, Lanjut Konfirmasi ({{ $deleteStep }}/5) &rarr;
                        </button>
                    @else
                        <button type="button" wire:click="executeDelete"
                            class="rounded-lg bg-rose-700 px-4 py-2 text-xs font-semibold text-white hover:bg-rose-800">
                            Hapus Permanen Sekarang (5/5)
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
