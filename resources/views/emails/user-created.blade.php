<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses ERP Bentang Artha</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f9f9f9;
            padding-bottom: 40px;
        }
        .main {
            background-color: #ffffff;
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
            border-spacing: 0;
            color: #1a1a1a;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        }
        /* Header Hitam Mewah */
        .header {
            background-color: #111111;
            padding: 40px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 20px;
            font-weight: 300;
            letter-spacing: 4px;
            margin: 0;
            text-transform: uppercase;
        }
        .content {
            padding: 50px 40px;
        }
        .welcome-title {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #000000;
        }
        .description {
            font-size: 15px;
            line-height: 1.8;
            color: #555555;
            margin-bottom: 30px;
        }
        /* Credential Box Modern */
        .credential-container {
            border-left: 3px solid #111111;
            background-color: #fcfcfc;
            padding: 25px;
            margin: 30px 0;
        }
        .credential-row {
            margin-bottom: 10px;
        }
        .credential-row:last-child {
            margin-bottom: 0;
        }
        .label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #999999;
            display: block;
            margin-bottom: 4px;
        }
        .value {
            font-size: 16px;
            font-family: 'Courier New', monospace;
            font-weight: 700;
            color: #111111;
        }
        /* Tombol Minimalis */
        .button-wrapper {
            text-align: center;
            padding: 20px 0;
        }
        .btn-dark {
            background-color: #111111;
            color: #ffffff !important;
            padding: 18px 45px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .footer {
            text-align: center;
            padding: 40px;
            font-size: 11px;
            color: #aaaaaa;
            letter-spacing: 1px;
            line-height: 2;
        }
        .divider {
            border-bottom: 1px solid #eeeeee;
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <table class="main">
            <tr>
                <td class="header">
                    <h1>Bentang Artha</h1>
                </td>
            </tr>

            <tr>
                <td class="content">
                    <div class="welcome-title">Halo, {{ $user->name }}</div>
                    <div class="description">
                        Pendaftaran akun Anda pada sistem <strong>ERP Bentang Artha</strong> telah disetujui. 
                        Silakan gunakan kredensial resmi berikut untuk masuk ke dashboard manajemen Anda.
                    </div>

                    <div class="credential-container">
                        <div class="credential-row">
                            <span class="label">Username / Email</span>
                            <span class="value">{{ $user->email }}</span>
                        </div>
                        <div class="divider"></div>
                        <div class="credential-row">
                            <span class="label">Access Password</span>
                            <span class="value">{{ $plainPassword }}</span>
                        </div>
                    </div>

                    <div class="button-wrapper">
                        <a href="{{ url('/erp/login') }}" class="btn-dark">Masuk ke Sistem</a>
                    </div>

                    <p style="font-size: 12px; color: #999999; text-align: center; margin-top: 40px;">
                        * Harap rahasiakan data login Anda dan segera perbarui kata sandi secara berkala.
                    </p>
                </td>
            </tr>

            <tr>
                <td class="footer">
                    &copy; {{ date('Y') }} IT DEPARTMENT — BENTANG ARTHA<br>
                    SYSTEM GENERATED EMAIL. PLEASE DO NOT REPLY.
                </td>
            </tr>
        </table>
    </div>
</body>
</html>