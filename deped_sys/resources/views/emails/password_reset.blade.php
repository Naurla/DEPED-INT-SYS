<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verification Code</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 40px 20px;">
    <div style="max-width: 500px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid #e5e7eb;">
        
        <div style="padding: 20px; text-align: center; border-bottom: 1px solid #f3f4f6;">
            <h3 style="margin: 0; color: #333; font-size: 16px; text-transform: uppercase; letter-spacing: 0.5px;">Department of Education</h3>
            <p style="margin: 5px 0 0; color: #6b7280; font-size: 12px; text-transform: uppercase; font-weight: bold;">Zamboanga City Division</p>
        </div>

        <div style="background-color: #a52a2a; padding: 15px; text-align: center;">
            <h2 style="margin: 0; color: #ffffff; font-size: 18px; letter-spacing: 2px; font-weight: bold;">VERIFICATION CODE</h2>
        </div>

        <div style="padding: 30px; text-align: center;">
            <p style="margin: 0 0 20px; color: #555; font-size: 15px; line-height: 1.5;">
                Here is your verification code.<br>
                Don't share it with anyone else.
            </p>

            <div style="background-color: #f9f9f9; border: 2px dashed #ccc; border-radius: 8px; padding: 15px 30px; margin: 0 auto 20px; display: inline-block;">
                <span style="font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #333; font-family: monospace;">{{ $code }}</span>
            </div>

            <p style="margin: 0; color: #888; font-size: 13px;">
                Code will expire in 10 minutes.
            </p>
        </div>

    </div>
</body>
</html>