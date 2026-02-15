<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transaction Notification</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f8fafc; padding: 24px;">
    <div
        style="max-width: 560px; margin: 0 auto; background: #ffffff; padding: 24px; border-radius: 8px; border: 1px solid #e5e7eb;">
        <h2 style="margin: 0 0 16px; color: #111827;">Transaction Notification</h2>
        <p style="margin: 0 0 12px; color: #111827;">Dear {{ $customerName ?: 'Customer' }},</p>
        <p style="margin: 0 0 12px; color: #111827;">
            Rs. {{ number_format($transaction->amount, 2) }} has been {{ $actionText }} your account
            {{ $maskedAccount }}
            on {{ $transaction->created_at?->format('Y-m-d H:i') }}.
        </p>
        <p style="margin: 0; color: #111827;">Available balance: Rs. {{ number_format($transaction->balance_after, 2) }}.
        </p>
        <p style="margin: 16px 0 0; color: #6b7280; font-size: 12px;">If you did not perform this transaction, please
            contact the bank immediately.<small>9862414236</small></p>
    </div>
</body>

</html>
