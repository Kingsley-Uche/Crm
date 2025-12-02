<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rent Renewed</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f4; }
        .container { max-width: 600px; background: #fff; padding: 30px; margin: auto; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        h2 { color: #333; }
        p { font-size: 16px; color: #444; line-height: 1.6; }
        .footer { margin-top: 20px; font-size: 14px; color: #888; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Dear {{ ucfirst($tenant->first_name) }} {{ ucfirst($tenant->last_name) }},</h2>

        <p>We are pleased to inform you that your rent has been successfully renewed.</p>

        <p><strong>Apartment:</strong> Unit {{ $rentCycle->unit_number }}<br>
           <strong>Address:</strong> {{ $rentCycle->apartment->address ?? 'N/A' }}<br>
           <strong>Rent Fee:</strong> £{{ number_format($rentCycle->rent_fee, 2) }}<br>
           <strong>Start Date:</strong> {{ \Carbon\Carbon::parse($rentCycle->start_date)->toFormattedDateString() }}<br>
           <strong>End Date:</strong> {{ \Carbon\Carbon::parse($rentCycle->end_date)->toFormattedDateString() }}
        </p>

        <p>We appreciate your continued tenancy and look forward to serving you better.</p>

        <div class="footer">
            &copy; {{ date('Y') }} CTR TRIANGLE TMO. All rights reserved.
        </div>
    </div>
</body>
</html>
