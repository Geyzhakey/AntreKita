<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi AntreKita</title>
</head>
<body style="margin:0;padding:0;background-color:#EEF8F4;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;color:#0E241E;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:580px;background-color:#F7FCFA;border:1px solid #CBE8DC;border-radius:18px;overflow:hidden;">
                    <tr>
                        <td style="background-color:#0F6E56;padding:22px 26px;color:#F7FCFA;">
                            @if ($triggerType === 'h_minus_5_active')
                                <div style="font-size:15px;font-weight:800;letter-spacing:0.01em;color:#90DEC3;">
                                    ANTREKITA: 5 MENIT MASA PENGGUNAAN {{ mb_strtoupper($unitDisplay) }} KAMU!
                                </div>
                            @elseif ($triggerType === 'session_completed')
                                <div style="font-size:18px;font-weight:800;letter-spacing:-0.02em;color:#F7FCFA;">
                                    AntreKita &middot; Ringkasan Sesi Selesai
                                </div>
                            @else
                                <div style="font-size:18px;font-weight:800;letter-spacing:-0.02em;color:#F7FCFA;">
                                    AntreKita
                                </div>
                            @endif
                            <div style="font-size:12px;color:#CBE8DC;margin-top:4px;">AntreKita &mdash; Reservasi Ruang dan Meja dengan Lebih Mudah</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:26px;font-size:14px;line-height:1.65;color:#0E241E;">
                            @if ($triggerType === 'h_minus_5_active')
                                <p style="margin:0 0 14px;">Halo, <strong>{{ $user->name }}</strong>!</p>

                                <p style="margin:0 0 14px;">
                                    Kami ingin menginformasikan bahwa masa penggunaan <strong>{{ $unitKind }} {{ $unitDisplay }}</strong> yang sedang kamu gunakan akan segera berakhir dalam <strong>5 menit</strong>.
                                </p>

                                <p style="margin:0 0 10px;font-weight:600;">Berikut informasi penggunaan kamu:</p>

                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#EEF8F4;border:1px solid #90DEC3;border-radius:12px;padding:14px 16px;margin-bottom:18px;font-size:13px;">
                                    <tr>
                                        <td style="padding:5px 0;color:#47625A;">Meja/Ruangan:</td>
                                        <td align="right" style="padding:5px 0;font-weight:700;color:#0E241E;">{{ $unitDisplay }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:5px 0;color:#47625A;">Lokasi:</td>
                                        <td align="right" style="padding:5px 0;font-weight:700;color:#0E241E;">{{ $locationName }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:5px 0;color:#47625A;">Waktu Mulai:</td>
                                        <td align="right" style="padding:5px 0;font-weight:700;color:#0E241E;">{{ $booking->starts_at?->format('H:i') }} WIB</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:5px 0;color:#47625A;">Waktu Berakhir:</td>
                                        <td align="right" style="padding:5px 0;font-weight:700;color:#0F6E56;">{{ $booking->ends_at?->format('H:i') }} WIB</td>
                                    </tr>
                                </table>

                                <p style="margin:0 0 14px;">
                                    Mohon segera persiapkan barang-barang pribadi dan pastikan tidak ada barang yang tertinggal sebelum meninggalkan area tersebut.
                                </p>

                                <p style="margin:0 0 18px;">Terima kasih telah menggunakan AntreKita!</p>
                            @elseif ($triggerType === 'session_completed')
                                <p style="margin:0 0 14px;">Halo, <strong>{{ $user->name }}</strong>!</p>

                                <p style="margin:0 0 14px;">Terima kasih telah menggunakan layanan AntreKita!</p>

                                <p style="margin:0 0 14px;">
                                    Kami ingin menginformasikan bahwa masa penggunaan <strong>{{ $unitKind }} {{ $unitDisplay }}</strong> telah berakhir.
                                </p>

                                <p style="margin:0 0 10px;font-weight:600;">Berikut ringkasan penggunaan kamu:</p>

                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#EEF8F4;border:1px solid #90DEC3;border-radius:12px;padding:14px 16px;margin-bottom:18px;font-size:13px;">
                                    <tr>
                                        <td style="padding:5px 0;color:#47625A;">Meja/Ruangan:</td>
                                        <td align="right" style="padding:5px 0;font-weight:700;color:#0E241E;">{{ $unitDisplay }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:5px 0;color:#47625A;">Lokasi:</td>
                                        <td align="right" style="padding:5px 0;font-weight:700;color:#0E241E;">{{ $locationName }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:5px 0;color:#47625A;">Waktu Mulai:</td>
                                        <td align="right" style="padding:5px 0;font-weight:700;color:#0E241E;">{{ $booking->starts_at?->format('H:i') }} WIB</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:5px 0;color:#47625A;">Waktu Selesai:</td>
                                        <td align="right" style="padding:5px 0;font-weight:700;color:#0E241E;">{{ ($booking->finished_at ?? now())->format('H:i') }} WIB</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:5px 0;color:#47625A;">Status:</td>
                                        <td align="right" style="padding:5px 0;font-weight:700;color:#0F6E56;">Selesai</td>
                                    </tr>
                                </table>

                                <p style="margin:0 0 14px;">
                                    Kami berharap fasilitas yang disediakan dapat membantu aktivitas kamu dengan nyaman dan lancar.
                                </p>

                                <p style="margin:0 0 14px;">
                                    Jangan lupa untuk kembali menggunakan AntreKita ketika kamu membutuhkan reservasi meja atau ruangan berikutnya!
                                </p>

                                <p style="margin:0 0 18px;">Terima kasih telah menjadi bagian dari pengguna AntreKita.</p>
                            @else
                                <p style="margin:0 0 12px;">Halo, <strong>{{ $user->name }}</strong>!</p>
                                <div style="background-color:#EEF8F4;border:1px solid #90DEC3;border-radius:12px;padding:16px;margin-bottom:18px;">
                                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#0F6E56;margin-bottom:6px;">
                                        {{ $locationName }} &middot; {{ $unitDisplay }}
                                    </div>
                                    <div style="font-size:14px;line-height:1.6;color:#0E241E;">
                                        {{ $bodyMessage }}
                                    </div>
                                </div>
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="font-size:12px;color:#47625A;border-top:1px dashed #CBE8DC;padding-top:14px;margin-bottom:18px;">
                                    <tr>
                                        <td style="padding:4px 0;">Slot Waktu</td>
                                        <td align="right" style="padding:4px 0;font-weight:700;color:#0E241E;">
                                            {{ $booking->starts_at?->format('H:i') }} - {{ $booking->ends_at?->format('H:i') }} WIB
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:4px 0;">Pengguna / Kelompok</td>
                                        <td align="right" style="padding:4px 0;font-weight:700;color:#0E241E;">
                                            {{ $booking->group_name }} ({{ $booking->people_count }} orang)
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            <div style="margin-top:20px;border-top:1px solid #CBE8DC;padding-top:16px;font-size:13px;line-height:1.6;">
                                <p style="margin:0;">Salam hangat,</p>
                                <p style="margin:4px 0 0;font-weight:800;color:#0F6E56;">Tim AntreKita</p>
                                <p style="margin:2px 0 0;font-size:12px;font-style:italic;color:#47625A;">AntreKita - Reservasi Ruang dan Meja dengan Lebih Mudah.</p>
                            </div>

                            <div style="margin-top:20px;text-align:center;">
                                <a href="{{ url('/session') }}" style="display:inline-block;background-color:#0F6E56;color:#F7FCFA;text-decoration:none;font-size:13px;font-weight:700;padding:11px 22px;border-radius:10px;">
                                    Buka Halaman Sesi Aktif
                                </a>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
