<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="x-apple-disable-message-formating">
    <title>Login Message</title>
    <style>
        table,
        td,
        div,
        h1,
        p1 {
            font-family: Arial, Helvetica, sans-serif;
        }

    </style>
</head>

<body style="margin:0;padding:0">
    <table role="presentation"
        style="width:100%;border-collapse:collapse;border:0;border-spacing:0;background:#ffffff;">
        <tr>
            <td style="padding:0;text-align:center">
                <table role="presentation"
                    style="width:602px;border-collapse:collapse;border:1px solid #cccccc;border-spacing:0;text-align:left;margin:auto">
                    <tr>
                        <td style="padding:40px 0 30px 0;background-color:#fff;text-align:center">
                            <img src="{{ asset('img/edc_logo_png.png') }}" width="300">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:36px 30px 42px 30px;">
                            <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;">
                                <tr>
                                    <td style="padding:0px 0px 10px 0px;">
                                        Yth. <b>{{ $nama }}</b><br/>
                                        Notifikasi Keamanan - Update Password Berhasil
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:0;">
                                        <table>
                                            <tr>
                                                <td>Tanggal akses </td>
                                                <th>: {{ $tanggal }} WIB</th>
                                            </tr>
                                            <tr>
                                                <td>IP Address</td>
                                                <th>: {{ $ipaddress }}</th>
                                            </tr>
                                            <tr>
                                                <td>Perangkat</td>
                                                <th>: {{ $device }}</th>
                                            </tr>
                                            <tr>
                                                <td>Sistem Operasi</td>
                                                <th>: {{ $os }}</th>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 0px 0px 0px;">
                                        Sistem mendeteksi adanya penggantian password pada Akun Aplikasi EDC Anda.<br/>
                                        Jika ini memang Anda, Anda tidak perlu melakukan apa-apa.<br/>
                                        Jika bukan, silahkan ubah segera password dan hubungi Sub Bagian Teknologi Informasi.<br/>
                                        Terima Kasih.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0px 30px 42px 30px;">
                            <a href="http://edc.ptpn4.com">Buka Aplikasi EDC</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
