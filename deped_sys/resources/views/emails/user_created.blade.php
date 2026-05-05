<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Admin Portal</title>
</head>
<body style="font-family: 'Inter', Arial, sans-serif; background-color: #f9fafb; margin: 0; padding: 20px 10px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.06); border: 1px solid #f1f1f1;">
        
        <!-- Official Banner from Google Drive -->
        <div style="width: 100%; overflow: hidden;">
            <img src="https://lh3.googleusercontent.com/u/0/d/1yFN8P0TXfG29-TIqk6e1EVMOJWzY_8rZ" alt="DepEd Zamboanga City Banner" style="width: 100%; height: auto; display: block;">
        </div>

        <div style="padding: 40px 40px 30px;">
            <h2 style="color: #111827; font-size: 24px; font-weight: 800; margin: 0 0 15px; letter-spacing: -0.5px;">Hello, {{ $user->name }}!</h2>
            <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin: 0;">
                Your administrative account for the <strong>DepEd Zamboanga City Admin Portal</strong> has been successfully created.
            </p>

            <div style="margin: 35px 0; background-color: #f9fafb; border-radius: 16px; border: 1px solid #f3f4f6; padding: 25px;">
                <p style="margin: 0 0 15px; font-size: 12px; font-weight: 800; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px;">Login Credentials</p>
                
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; color: #6b7280; font-size: 14px; width: 140px;">Email Address</td>
                        <td style="padding: 8px 0; color: #111827; font-size: 14px; font-weight: 600;">{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #6b7280; font-size: 14px;">Temp Password</td>
                        <td style="padding: 8px 0; color: #a52a2a; font-size: 15px; font-weight: 700; font-family: monospace;">{{ $tempPassword }}</td>
                    </tr>
                </table>
            </div>

            <div style="text-align: center; margin-bottom: 35px;">
                <a href="{{ route('login') }}" style="display: inline-block; background-color: #a52a2a; color: #ffffff; padding: 16px 35px; border-radius: 12px; font-weight: 700; text-decoration: none; font-size: 15px;">
                    Access Admin Portal
                </a>
                <p style="margin: 15px 0 0; font-size: 13px; color: #9ca3af; font-style: italic;">
                    * You will be prompted to set a permanent password upon login.
                </p>
            </div>
        </div>

        <div style="background-color: #f9fafb; padding: 30px 40px; border-top: 1px solid #f3f4f6; text-align: center;">
            <p style="margin: 0; font-size: 11px; color: #9ca3af; line-height: 1.5;">
                This is an automated system message.<br>
                &copy; 2026 DepEd Zamboanga City
            </p>
        </div>
    </div>
</body>
</html>