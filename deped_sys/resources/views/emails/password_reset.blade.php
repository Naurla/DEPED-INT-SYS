<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Code</title>
</head>
<body style="font-family: 'Inter', Arial, sans-serif; background-color: #f9fafb; margin: 0; padding: 20px 10px;">
    <div style="max-width: 500px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #f1f1f1;">
        
        <!-- Official Banner from Google Drive -->
        <div style="width: 100%; overflow: hidden;">
            <img src="https://lh3.googleusercontent.com/u/0/d/1yFN8P0TXfG29-TIqk6e1EVMOJWzY_8rZ" alt="DepEd Zamboanga City Banner" style="width: 100%; height: auto; display: block;">
        </div>

        <div style="padding: 40px 30px; text-align: center;">
            <div style="margin-bottom: 25px;">
                <h2 style="margin: 0; color: #111827; font-size: 22px; font-weight: 800; letter-spacing: -0.5px;">Verify Your Identity</h2>
                <p style="margin: 10px 0 0; color: #6b7280; font-size: 15px; line-height: 1.6;">
                    Please use the code below to complete your password reset. For security, do not share this code.
                </p>
            </div>

            <!-- Modern Code Box -->
            <div style="background-color: #fdf2f2; border: 1px solid #fecaca; border-radius: 16px; padding: 25px 10px; margin: 30px 0;">
                <span style="font-size: 36px; font-weight: 900; letter-spacing: 12px; color: #a52a2a; font-family: 'Courier New', monospace; margin-left: 12px;">{{ $code }}</span>
            </div>

            <div style="margin-top: 30px; padding-top: 25px; border-top: 1px solid #f3f4f6;">
                <p style="margin: 0; color: #9ca3af; font-size: 12px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">
                    Code expires in <span style="color: #ef4444; font-weight: 700;">10 minutes</span>
                </p>
            </div>
        </div>

        <div style="padding: 25px; text-align: center; background-color: #f9fafb;">
            <p style="margin: 0; font-size: 11px; color: #9ca3af; line-height: 1.5;">
                If you did not request this, you can safely ignore this email.<br>
                &copy; 2026 DepEd Zamboanga City
            </p>
        </div>
    </div>
</body>
</html>