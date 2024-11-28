
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Reminder</title>
</head>
<body>
    <h1>Hello {{ $user->name }},</h1>
<p>Your subscription for {{ $subscription->type }} will expire on {{ $subscription->end_date }}.</p>
<p>Renew now to continue enjoying our services.</p>
<p>Thank you,<br>Your Company</p>
</body>
</html>
