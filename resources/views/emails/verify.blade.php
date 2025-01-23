<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container{
            display: table;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
            width: 100%;
        }
        .logos{
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        .header {
            display: block;
            width: 100%;
            flex-direction:  column;
            align-items: center;
            gap: 14px;
        }
        .title {
            font-size: 40px;
            line-height: 59px;
            font-weight: 700;
            max-width: 460px;
        }
        .email-body{
            display: block;
            max-width: 440px;
        }
        .button-cont{
            display: flex;
            justify-content: center;
        }
        p{
            line-height: 25px;
        }
        .greeting{
            margin-left: 10px;
        }
    </style>
</head>
<body style="background-color: #F6F6F6;">
    <div style="width: 100%;" class="container">
        <div style="text-align: center; margin: 0 auto;" class="header">
            <div class="logos">
            <img style="margin: auto; margin-top: 20px;" src="{{ env('APP_URL') . '/storage/assets/Logo.png' }}" alt="Logo">
            </div>
            <h1 style="margin: 12px auto 3px auto; text-align: center; " class="title">
            Verify your email address to get started
            </h1>
        </div>
        <div style="margin: 0 auto;" class="email-body">
            <p class="greeting">Hi {{$name}},</p>
            <p>You're almost there! To complete your sign up, please verify your email address.</p>
            <div class="button-cont">
                <a href="{{str_replace('/api', '', $verificationUrl) }}"   style="padding: 11px 44px; background-color: #4B69FD; border-radius: 10px; font-size: 16px; color: white; font-weight: 600; margin-top: 2px; border: none; cursor: pointer; text-decoration:none; margin: 4px auto 0 auto;">Verify now</a>
            </div>
        </div>
    </div>
    
</body>
</html>
