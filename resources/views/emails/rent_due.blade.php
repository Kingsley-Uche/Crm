<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rent Due Reminder</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f6f6f6;
            padding: 20px;
            margin: 0;
        }
        .email-container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 150px;
            height: auto;
        }
        h1 {
            font-size: 20px;
            color: #333333;
        }
        p {
            font-size: 16px;
            color: #444444;
            line-height: 1.6;
        }
        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #999999;
            text-align: center;
        }
        @media screen and (max-width: 600px) {
            .email-container {
                padding: 15px;
            }
            p, h1 {
                font-size: 16px !important;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <img src="{{ asset('images/company-logo.png') }}" alt="Company Logo" class="logo">
        </div>

        <h1>Rent Due Reminder</h1>

        <p>
            Dear {{ ucfirst(strtolower($account->tenant->first_name)) }} {{ ucfirst(strtolower($account->tenant->last_name)) }},
        </p>

        <p>
            This is a friendly reminder that your rent for unit <strong>{{ $account->unit_number }}</strong> at
            <strong>{{ $account->apartment->address ?? 'your apartment address' }}</strong>
            is currently active.
        </p>

        <p>
            Your rent cycle began on <strong>{{ \Carbon\Carbon::parse($cycle->start_date)->toFormattedDateString() }}</strong>
            and will expire on <strong>{{ \Carbon\Carbon::parse($cycle->end_date)->toFormattedDateString() }}</strong>.
        </p>

        <p>
            Please ensure that your payment is made on time to avoid any late fees or service disruption.
            If you have already made your payment, kindly disregard this notice.
        </p>

        <div class="footer">
            Thank you for staying with us.<br>
            &copy; {{ date('Y') }} CTR Triangle TMO
        </div>
    </div>
</body>
</html>
