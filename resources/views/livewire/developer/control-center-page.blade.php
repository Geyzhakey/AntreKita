<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <span class="rounded-md bg-[#CAFFA1] px-2.5 py-0.5 font-mono text-[10px] font-bold uppercase text-[#1C241C]">
                DEVELOPER.AI &middot; OTORITAS PENUH SISTEM
            </span>
            <h1 class="mt-2 font-serif text-3xl font-bold tracking-tight text-[#1C241C] dark:text-[#F3EFE4]">
                Developer Command Center
            </h1>
            <p class="mt-1 text-xs text-[#4B5649] dark:text-[#B8C4B5]">
                Inspeksi lengkap akun Administrator, pembekuan sementara, penghapusan terproteksi (5x konfirmasi), dan kendali global website.
            </p>
        </div>

        <div class="flex flex-wrap gap-2.5">
            <div class="rounded-xl border border-[#D7CFB8] bg-[#FBF8EE] px-4 py-2 text-center dark:border-[#2B3831] dark:bg-[#18211C]">
                <div class="font-mono text-lg font-bold text-[#1C241C] dark:text-[#CAFFA1]">{{ $admins->count() }}</div>
                <div class="text-[10px] font-semibold text-[#4B5649] dark:text-[#9AA897]">Instansi Admin</div>
            </div>
            <div class="rounded-xl border border-[#D7CFB8] bg-[#FBF8EE] px-4 py-2 text-center dark:border-[#2B3831] dark:bg-[#18211C]">
                <div class="font-mono text-lg font-bold text-[#1C241C] dark:text-[#CAFFA1]">{{ $totalVisitors }}</div>
                <div class="text-[10px] font-semibold text-[#4B5649] dark:text-[#9AA897]">Visitor Terdaftar</div>
            </div>
            <div class="rounded-xl border border-[#D7CFB8] bg-[#FBF8EE] px-4 py-2 text-center dark:border-[#2B3831] dark:bg-[#18211C]">
                <div class="font-mono text-lg font-bold text-[#1C241C] dark:text-[#CAFFA1]">{{ $activeBookingsCount }}</div>
                <div class="text-[10px] font-semibold text-[#4B5649] dark:text-[#9AA897]">Booking / Antrean</div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-12">
        <!-- Left Column: Provisioning & Platform Controls -->
        <div class="space-y-6 lg:col-span-4">
            <section class="rounded-2xl border border-[#D7CFB8] bg-[#FBF8EE] p-5 dark:border-[#2B3831] dark:bg-[#18211C]">
                <h2 class="font-serif text-lg font-bold text-[#1C241C] dark:text-[#F3EFE4]">Terbitkan Akun Administrator</h2>
                <p class="mt-0.5 text-[11px] text-[#4B5649] dark:text-[#B8C4B5]">Buat akun dan Kode Daftar instansi setelah verifikasi WhatsApp.</p>

                <form wire:submit="createAdminAccount" class="mt-4 space-y-3">
                    <div>
                        <label class="block text-xs font-bold">Nama Instansi</label>
                        <input type="text" wire:model="institution_name" required placeholder="Perpustakaan Pusat ITB"
                            class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-3 py-2 text-xs outline-none focus:border-[#1B5E3A] dark:border-[#2B3831] dark:bg-[#202B25]">
                        @error('institution_name') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-xs font-bold">Kode Daftar</label>
                            <input type="text" wire:model="registration_code" required placeholder="LBR2ITB26"
                                class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-3 py-2 font-mono text-xs uppercase outline-none focus:border-[#1B5E3A] dark:border-[#2B3831] dark:bg-[#202B25]">
                            @error('registration_code') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold">Jenis Gedung</label>
                            <input type="text" wire:model="building_type" required placeholder="Perpustakaan"
                                class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-3 py-2 text-xs outline-none focus:border-[#1B5E3A] dark:border-[#2B3831] dark:bg-[#202B25]">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-xs font-bold">Nama PIC / Admin</label>
                            <input type="text" wire:model="pic_name" required placeholder="Dr. Rina"
                                class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-3 py-2 text-xs outline-none focus:border-[#1B5E3A] dark:border-[#2B3831] dark:bg-[#202B25]">
                        </div>
                        <div>
                            <label class="block text-xs font-bold">Kota</label>
                            <input type="text" wire:model="city" required placeholder="Bandung"
                                class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-3 py-2 text-xs outline-none focus:border-[#1B5E3A] dark:border-[#2B3831] dark:bg-[#202B25]">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-xs font-bold">Email Admin</label>
                            <input type="email" wire:model="pic_email" required placeholder="admin@itb.ac.id"
                                class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-3 py-2 text-xs outline-none focus:border-[#1B5E3A] dark:border-[#2B3831] dark:bg-[#202B25]">
                            @error('pic_email') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold">WhatsApp PIC</label>
                            <input type="text" wire:model="pic_whatsapp" placeholder="0812..."
                                class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-3 py-2 font-mono text-xs outline-none focus:border-[#1B5E3A] dark:border-[#2B3831] dark:bg-[#202B25]">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold">Alamat Gedung</label>
                        <input type="text" wire:model="address" required placeholder="Jl. Ganesa No. 10..."
                            class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-3 py-2 text-xs outline-none focus:border-[#1B5E3A] dark:border-[#2B3831] dark:bg-[#202B25]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold">Tujuan Permintaan</label>
                        <textarea wire:model="purpose" required rows="2" placeholder="Pemetaan kursi & ruang diskusi..."
                            class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-3 py-2 text-xs outline-none focus:border-[#1B5E3A] dark:border-[#2B3831] dark:bg-[#202B25]"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold">Password Instansi</label>
                        <input type="text" wire:model="password" required placeholder="Minimal 6 karakter"
                            class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-3 py-2 font-mono text-xs outline-none focus:border-[#1B5E3A] dark:border-[#2B3831] dark:bg-[#202B25]">
                    </div>

                    <button type="submit"
                        class="w-full rounded-xl bg-[#1B5E3A] px-4 py-2.5 text-xs font-bold text-[#CAFFA1] transition hover:bg-[#14472C] dark:bg-[#CAFFA1] dark:text-[#1C241C]">
                        Buat Akun &amp; Terbitkan Kode Daftar
                    </button>
                </form>
            </section>

            <section class="rounded-2xl border border-[#D7CFB8] bg-[#FBF8EE] p-5 dark:border-[#2B3831] dark:bg-[#18211C]">
                <h2 class="font-serif text-lg font-bold text-[#1C241C] dark:text-[#F3EFE4]">Kontrol Penuh Website</h2>
                <form wire:submit="savePlatformSettings" class="mt-4 space-y-3">
                    <div>
                        <label class="block text-xs font-bold">Nomor WhatsApp Developer Resmi</label>
                        <input type="text" wire:model="developer_whatsapp" required placeholder="6281200000000"
                            class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-3 py-2 font-mono text-xs outline-none focus:border-[#1B5E3A] dark:border-[#2B3831] dark:bg-[#202B25]">
                        <p class="mt-1 text-[10px] text-[#4B5649] dark:text-[#9AA897]">Digunakan pada tombol "Daftar Akun Administrator" di halaman Login.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold">Pengumuman Global (Broadcast Banner)</label>
                        <input type="text" wire:model="global_announcement" placeholder="Kosongkan jika tidak ada pengumuman"
                            class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-3 py-2 text-xs outline-none focus:border-[#1B5E3A] dark:border-[#2B3831] dark:bg-[#202B25]">
                    </div>

                    <label class="flex items-center justify-between rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 p-3 text-xs font-bold dark:border-[#2B3831] dark:bg-[#202B25]">
                        <span>Kunci Booking Global (Mode Maintenance)</span>
                        <input type="checkbox" wire:model="booking_maintenance_mode" class="rounded border-[#D7CFB8] text-[#1B5E3A]">
                    </label>

                    <button type="submit"
                        class="w-full rounded-xl bg-[#1C241C] px-4 py-2 text-xs font-bold text-[#CAFFA1] transition hover:opacity-90 dark:bg-[#CAFFA1] dark:text-[#1C241C]">
                        Terapkan Otoritas Global
                    </button>
                </form>
            </section>
        </div>

        <!-- Right Column: Admin Inspection Table + Dev Accounts + Audit Logs -->
        <div class="space-y-6 lg:col-span-8">
            <section class="rounded-2xl border border-[#D7CFB8] bg-[#FBF8EE] p-5 dark:border-[#2B3831] dark:bg-[#18211C]">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#D7CFB8] pb-3.5 dark:border-[#2B3831]">
                    <div>
                        <h2 class="font-serif text-lg font-bold text-[#1C241C] dark:text-[#F3EFE4]">Direktori Lengkap Akun Administrator</h2>
                        <p class="text-[11px] text-[#4B5649] dark:text-[#B8C4B5]">Inspeksi detail gedung, bekukan sementara, atau hapus permanen (5x konfirmasi).</p>
                    </div>
                    <input type="text" wire:model.live.debounce.250ms="search" placeholder="Cari instansi / kode..."
                        class="rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-3 py-1.5 text-xs outline-none focus:border-[#1B5E3A] dark:border-[#2B3831] dark:bg-[#202B25]">
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-[#D7CFB8] text-[#4B5649] dark:border-[#2B3831] dark:text-[#9AA897]">
                                <th class="pb-2.5 font-bold">Instansi &amp; Kode</th>
                                <th class="pb-2.5 font-bold">PIC &amp; Kontak</th>
                                <th class="pb-2.5 font-bold">Unit</th>
                                <th class="pb-2.5 font-bold">Status</th>
                                <th class="pb-2.5 text-right font-bold">Otoritas Developer</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#D7CFB8]/60 dark:divide-[#2B3831]">
                            @forelse ($admins as $adm)
                                <tr>
                                    <td class="py-3">
                                        <div class="font-bold text-[#1C241C] dark:text-[#F3EFE4]">{{ $adm->institution_label ?? $adm->institution?->name }}</div>
                                        <div class="mt-0.5 flex items-center gap-1.5">
                                            <span class="rounded bg-[#CAFFA1] px-1.5 py-0.5 font-mono text-[10px] font-bold text-[#1C241C]">{{ $adm->registration_code }}</span>
                                            <span class="text-[10px] text-[#4B5649] dark:text-[#9AA897]">{{ $adm->institution?->building_type ?? 'Fasilitas' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="font-semibold">{{ $adm->name }}</div>
                                        <div class="font-mono text-[10px] text-[#4B5649] dark:text-[#9AA897]">{{ $adm->email }} &middot; {{ $adm->whatsapp_number ?? '-' }}</div>
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
                                                class="rounded-lg border border-[#D7CFB8] bg-[#EFEAD8] px-2.5 py-1 text-[11px] font-bold text-[#1C241C] hover:border-[#1B5E3A] dark:border-[#2B3831] dark:bg-[#202B25] dark:text-[#F3EFE4]">
                                                Inspeksi
                                            </button>

                                            @if ($adm->is_frozen)
                                                <button type="button" wire:click="unfreezeAdmin({{ $adm->id }})"
                                                    class="rounded-lg bg-[#1B5E3A] px-2.5 py-1 text-[11px] font-bold text-[#CAFFA1]">
                                                    Aktifkan
                                                </button>
                                            @else
                                                <button type="button" wire:click="openFreezeModal({{ $adm->id }})"
                                                    class="rounded-lg border border-amber-300 bg-amber-100 px-2.5 py-1 text-[11px] font-bold text-amber-900 dark:border-amber-700 dark:bg-amber-950/60 dark:text-amber-300">
                                                    Bekukan
                                                </button>
                                            @endif

                                            <button type="button" wire:click="startDelete({{ $adm->id }})"
                                                class="rounded-lg border border-rose-300 bg-rose-100 px-2.5 py-1 text-[11px] font-bold text-rose-900 dark:border-rose-800 dark:bg-rose-950/60 dark:text-rose-300">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-[#4B5649]">Tidak ada akun Administrator ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="grid gap-6 md:grid-cols-2">
                <section class="rounded-2xl border border-[#D7CFB8] bg-[#FBF8EE] p-5 dark:border-[#2B3831] dark:bg-[#18211C]">
                    <h2 class="font-serif text-base font-bold text-[#1C241C] dark:text-[#F3EFE4]">4 Akun Developer.Ai (Utama &amp; Cadangan)</h2>
                    <div class="mt-3 space-y-2">
                        @foreach ($developers as $dev)
                            <div class="flex items-center justify-between rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 p-2.5 text-xs dark:border-[#2B3831] dark:bg-[#202B25]">
                                <div>
                                    <div class="font-bold">{{ $dev->name }}</div>
                                    <div class="font-mono text-[10px] text-[#4B5649] dark:text-[#9AA897]">{{ $dev->email }}</div>
                                </div>
                                <span class="rounded bg-[#CAFFA1] px-2 py-0.5 font-mono text-[10px] font-bold text-[#1C241C]">{{ $dev->registration_code }}</span>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-2xl border border-[#D7CFB8] bg-[#FBF8EE] p-5 dark:border-[#2B3831] dark:bg-[#18211C]">
                    <h2 class="font-serif text-base font-bold text-[#1C241C] dark:text-[#F3EFE4]">Log Audit Otoritas Developer</h2>
                    <div class="mt-3 max-h-60 space-y-2 overflow-y-auto pr-1">
                        @foreach ($auditLogs as $log)
                            <div class="rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/50 p-2.5 text-[11px] dark:border-[#2B3831] dark:bg-[#202B25]">
                                <div class="flex items-center justify-between font-mono text-[10px] font-bold text-[#1B5E3A] dark:text-[#CAFFA1]">
                                    <span>{{ $log->action }}</span>
                                    <span>{{ $log->created_at->format('d/m H:i') }}</span>
                                </div>
                                <div class="mt-0.5 font-bold">{{ $log->target_label }}</div>
                                <div class="text-[#4B5649] dark:text-[#B8C4B5]">{{ $log->details }}</div>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- Modal 1: Inspeksi Detail Admin -->
    @if ($inspectAdmin)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/55 p-4 backdrop-blur-xs">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl border border-[#D7CFB8] bg-[#FBF8EE] p-6 shadow-xl dark:border-[#2B3831] dark:bg-[#18211C]">
                <div class="flex items-start justify-between gap-4 border-b border-[#D7CFB8] pb-3 dark:border-[#2B3831]">
                    <div>
                        <span class="rounded bg-[#CAFFA1] px-2 py-0.5 font-mono text-[10px] font-bold text-[#1C241C]">INSPEKSI LENGKAP ADMIN</span>
                        <h3 class="mt-1 font-serif text-xl font-bold">{{ $inspectAdmin->institution_label }}</h3>
                        <p class="font-mono text-xs text-[#4B5649] dark:text-[#9AA897]">Kode Daftar: {{ $inspectAdmin->registration_code }}</p>
                    </div>
                    <button type="button" wire:click="closeInspect" class="text-xl font-bold">&times;</button>
                </div>

                <div class="mt-4 grid gap-3 text-xs sm:grid-cols-2">
                    <div class="rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 p-3 dark:border-[#2B3831] dark:bg-[#202B25]">
                        <p class="font-bold">Data Pengelola (PIC)</p>
                        <p class="mt-1">Nama: {{ $inspectAdmin->name }}</p>
                        <p>Email: {{ $inspectAdmin->email }}</p>
                        <p>WhatsApp: {{ $inspectAdmin->whatsapp_number ?? '-' }}</p>
                        <p>Status: {{ $inspectAdmin->is_frozen ? 'DIBEKUKAN (' . $inspectAdmin->frozen_reason . ')' : 'AKTIF' }}</p>
                    </div>
                    <div class="rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 p-3 dark:border-[#2B3831] dark:bg-[#202B25]">
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
                        <div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-3">
                            @foreach ($inspectAdmin->institution->facilityUnits as $u)
                                <div class="rounded-lg border border-[#D7CFB8] bg-[#EFEAD8]/40 p-2 text-[11px] dark:border-[#2B3831] dark:bg-[#202B25]">
                                    <div class="flex items-center justify-between font-mono font-bold">
                                        <span>{{ $u->code }}</span>
                                        <span>({{ $u->pos_x }},{{ $u->pos_y }})</span>
                                    </div>
                                    <div class="text-[#4B5649] dark:text-[#B8C4B5]">{{ $u->zoneLabel() }} &middot; {{ $u->capacity }} org</div>
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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/55 p-4 backdrop-blur-xs">
            <div class="w-full max-w-md rounded-2xl border border-amber-400 bg-[#FBF8EE] p-6 shadow-xl dark:border-amber-700 dark:bg-[#18211C]">
                <h3 class="font-serif text-lg font-bold text-[#1C241C] dark:text-[#F3EFE4]">Bekukan Akun Sementara</h3>
                <p class="mt-1 text-xs text-[#4B5649] dark:text-[#B8C4B5]">
                    Akun <strong>{{ $freezeTarget->institution_label }}</strong> ({{ $freezeTarget->registration_code }}) tidak akan bisa login dan seluruh unitnya dinonaktifkan sementara.
                </p>
                <div class="mt-4">
                    <label class="block text-xs font-bold">Alasan Pembekuan</label>
                    <input type="text" wire:model="freezeReason" placeholder="Contoh: Audit ulang kapasitas gedung..."
                        class="mt-1 w-full rounded-xl border border-[#D7CFB8] bg-[#EFEAD8]/60 px-3 py-2 text-xs outline-none dark:border-[#2B3831] dark:bg-[#202B25]">
                </div>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" wire:click="$set('freezeAdminId', null)" class="rounded-xl border border-[#D7CFB8] px-4 py-2 text-xs font-bold">Batal</button>
                    <button type="button" wire:click="confirmFreeze" class="rounded-xl bg-amber-500 px-4 py-2 text-xs font-bold text-[#1C241C]">Bekukan Sekarang</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal 3: Konfirmasi Penghapusan 5x (5-Step Safety Lock) -->
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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/65 p-4 backdrop-blur-xs">
            <div class="w-full max-w-md rounded-2xl border-2 border-rose-500 bg-[#FBF8EE] p-6 shadow-2xl dark:bg-[#18211C]">
                <div class="flex items-center justify-between">
                    <span class="rounded bg-rose-600 px-2 py-0.5 font-mono text-[10px] font-bold text-white">
                        HAPUS PERMANEN &middot; TAHAP {{ $deleteStep }} / 5
                    </span>
                    <button type="button" wire:click="cancelDelete" class="text-lg font-bold">&times;</button>
                </div>

                <div class="mt-3 flex gap-1">
                    @for ($i = 1; $i <= 5; $i++)
                        <div class="h-1.5 flex-1 rounded-full {{ $i <= $deleteStep ? 'bg-rose-600' : 'bg-[#D7CFB8] dark:bg-[#2B3831]' }}"></div>
                    @endfor
                </div>

                <p class="mt-4 text-xs font-semibold leading-relaxed text-[#1C241C] dark:text-[#F3EFE4]">
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
                        class="rounded-xl border border-[#D7CFB8] bg-[#EFEAD8] px-4 py-2 text-xs font-bold text-[#1C241C] dark:border-[#2B3831] dark:bg-[#202B25] dark:text-[#F3EFE4]">
                        Batalkan
                    </button>

                    @if ($deleteStep < 5)
                        <button type="button" wire:click="advanceDeleteStep"
                            class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white hover:bg-rose-700">
                            Ya, Lanjut Konfirmasi ({{ $deleteStep }}/5) &rarr;
                        </button>
                    @else
                        <button type="button" wire:click="executeDelete"
                            class="rounded-xl bg-rose-700 px-4 py-2 text-xs font-bold text-white hover:bg-rose-800">
                            Hapus Permanen Sekarang (5/5)
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
