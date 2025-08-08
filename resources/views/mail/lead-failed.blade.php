<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Action Required: Resubmit Your Details</title>
</head>
<body style="font-family: sans-serif; line-height:1.4; color:#444;">
    <h2 style="color:#222">Resubmit Your Details</h2>
    <p>Hello{{ $lead->first_name ? ' ' . e($lead->first_name) : '' }},</p>
    <p>We weren’t able to process your submission. To complete your application, click the button below:</p>
    <p>
        <a href="{{ config('app.frontend_url'). '/form/' . $lead->id }}" style="display:inline-block;padding:10px 20px;background:#2d3748;color:#fff;text-decoration:none;border-radius:4px;">
            Review &amp; Resubmit
        </a>
    </p>
    <p>If that doesn’t work, copy &amp; paste:</p>
    <p style="word-break:break-all;"><a href="{{ config('app.frontend_url'). '/form/' . $lead->id}}">{{ config('app.frontend_url'). '/form/' . $lead->id }}</a></p>
    <p>Thanks,<br>- H</p>
</body>
</html>
