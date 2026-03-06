<!DOCTYPE html>
<html>
<head>
    <title>Welcome to DepEd Zamboanga City Admin Portal</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h2 style="color: #a52a2a;">Hello, {{ $user->name }}!</h2>
        <p>An administrator has created an account for you on the DepEd Zamboanga City Admin Portal.</p>
        
        <div style="background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 0;"><strong>Here are your login credentials:</strong></p>
            <ul style="list-style-type: none; padding-left: 0;">
                <li><strong>Email:</strong> {{ $user->email }}</li>
                <li><strong>Temporary Password:</strong> <span style="font-family: monospace; background: #e0e0e0; padding: 2px 6px; border-radius: 3px;">{{ $tempPassword }}</span></li>
            </ul>
        </div>

        <p>You can log in to the portal here: <a href="{{ route('login') }}" style="color: #a52a2a; font-weight: bold;">Login Page</a></p>
        <p><em>Note: You will be required to change your password upon your first login.</em></p>
        
        <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">
        <p style="font-size: 12px; color: #777;">This is an automated message. Please do not reply directly to this email.</p>
    </div>
</body>
</html>