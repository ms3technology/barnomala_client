<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Message</title>
</head>
<body style="margin:0;padding:24px;font-family:Arial,sans-serif;color:#0f172a;background:#f8fafc;">
    <div style="max-width:640px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:24px;">
        <h2 style="margin:0 0 16px;font-size:20px;color:#0f172a;">School Website Visitor</h2>
        <p style="margin:0 0 8px;"><strong>Name:</strong> {{ $details['name'] ?? '' }}</p>
        <p style="margin:0 0 8px;"><strong>Email:</strong> {{ $details['email'] ?? '' }}</p>
        <p style="margin:16px 0 8px;"><strong>Message:</strong></p>
        <p style="margin:0;white-space:pre-wrap;line-height:1.6;">{{ $details['message'] ?? '' }}</p>
    </div>
</body>
</html>
