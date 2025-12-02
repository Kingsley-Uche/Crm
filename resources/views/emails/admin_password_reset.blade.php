<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 30px;
            background-color: #f9f9f9;
        }

        h2 {
            color: #2c3e50;
        }

        .highlight {
            font-size: 18px;
            color: #d35400;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #7f8c8d;
        }

        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hello {{ ucfirst($admin->fName) }} {{ ucfirst($admin->lName) }},</h2>

        <p>We received a request to reset your admin account password.</p>

        <p>Your new system-generated password is:</p>

        <p class="highlight">{{ $newPassword }}</p>

        <p>Please use this password to log in, and ensure to update your password immediately for security reasons.</p>

        <p>If you did not request this password reset, please contact the system administrator immediately.</p>

        <p>Thank you,<br>
        <strong>CTR TRIANGLE TMO</strong></p>

        <div class="footer">
            <p>&copy; {{ date('Y') }} CTR TRIANGLE TMO. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
