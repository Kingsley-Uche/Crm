<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Permit Created</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333333;
            background-color: #f9f9f9;
            padding: 20px;
        }

        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        h2 {
            color: #0066cc;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            margin: 8px 0;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            text-align: center;
            color: #888888;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hello {{ $permit->fname }} {{ $permit->lname }},</h2>

        <p>We’re excited to inform you that your parking permit has been successfully {{$permit->caption}}. Below are the details:</p>

        <ul>
            <li><strong>Permit Name:</strong> {{ $permit->permit_name }}</li>
            <li><strong>Permit Code:</strong> {{ $permit->uniqueId }}</li>
            <li><strong>Passcode:</strong> {{ $permit->pass_code }}</li>
            <li><strong>Park:</strong> {{ $permit->park->name ?? 'N/A' }}</li>
            <li><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($permit->start_time)->toFormattedDateString() }}</li>
            <li><strong>End Date:</strong> {{ \Carbon\Carbon::parse($permit->end_time)->toFormattedDateString() }}</li>
        </ul>

        <p>You will need to provide your passcode so keep it safe.<br>
        If you have any questions or need assistance, feel free to reply to this email.</p>

        <p>Thank you for choosing CTR TMO.</p>

        <div class="footer">
            &copy; CTR TMO {{ date('Y') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
