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
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background-color:#F7FCFA;border:1px solid #CBE8DC;border-radius:16px;overflow:hidden;">
                    <tr>
                        <td style="background-color:#0F6E56;padding:20px 24px;color:#F7FCFA;">
                            <div style="font-size:18px;font-weight:800;letter-spacing:-0.02em;">AntreKita</div>
                            <div style="font-size:12px;opacity:0.85;margin-top:2px;">Sistem Antrean Ruang Belajar Terbuka</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 12px;font-size:14px;">Halo <strong>{{ $user->name }}</strong>,</p>
                            <div style="background-color:#EEF8F4;border:1px solid #90DEC3;border-radius:12px;padding:16px;margin-bottom:18px;">
                                <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#0F6E56;margin-bottom:6px;">
                                    {{ $booking->institution?->name ?? 'Mitra AntreKita' }} &middot; {{ $booking->facilityUnit?->code ?? '-' }}
                                </div>
                                <div style="font-size:14px;line-height:1.6;color:#0E241E;">
                                    {{ $bodyMessage }}
                                </div>
                            </div>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="font-size:12px;color:#47625A;border-top:1px dashed #CBE8DC;padding-top:14px;">
                                <tr>
                                    <td style="padding:4px 0;">Slot Waktu</td>
                                    <td align="right" style="padding:4px 0;font-weight:700;color:#0E241E;">
                                        {{ $booking->starts_at?->format('H:i') }} - {{ $booking->ends_at?->format('H:i') }} WIB
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:4px 0;">Kelompok / Pengguna</td>
                                    <td align="right" style="padding:4px 0;font-weight:700;color:#0E241E;">
                                        {{ $booking->group_name }} ({{ $booking->people_count }} orang)
                                    </td>
                                </tr>
                            </table>
                            <div style="margin-top:22px;text-align:center;">
                                <a href="{{ url('/session') }}" style="display:inline-block;background-color:#0F6E56;color:#F7FCFA;text-decoration:none;font-size:13px;font-weight:700;padding:11px 22px;border-radius:10px;">
                                    Buka Halaman Sesi Aktif
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:14px 24px;background-color:#EEF8F4;border-top:1px solid #CBE8DC;font-size:11px;color:#47625A;text-align:center;">
                            Pesan ini dikirim otomatis oleh sistem AntreKita. Mohon tidak membalas email ini.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
