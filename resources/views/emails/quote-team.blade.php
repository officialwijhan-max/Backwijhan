<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>New quote request</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1a1a1a; line-height: 1.6;">
    <h2 style="margin-bottom: 4px;">New quote request</h2>
    <p style="color: #666; margin-top: 0;">Submitted via {{ $quoteRequest->source }} ({{ strtoupper($quoteRequest->locale) }})</p>

    <table cellpadding="6" cellspacing="0" style="border-collapse: collapse; width: 100%; max-width: 560px;">
        <tr><td style="font-weight: bold; width: 160px;">Name</td><td>{{ $quoteRequest->name }}</td></tr>
        <tr><td style="font-weight: bold;">Company</td><td>{{ $quoteRequest->company ?? '—' }}</td></tr>
        <tr><td style="font-weight: bold;">Email</td><td>{{ $quoteRequest->email }}</td></tr>
        <tr><td style="font-weight: bold;">Phone</td><td>{{ $quoteRequest->phone ?? '—' }}</td></tr>
        <tr><td style="font-weight: bold;">Project type</td><td>{{ $quoteRequest->project_type }}</td></tr>
        <tr><td style="font-weight: bold;">Budget</td><td>{{ $quoteRequest->budget_range }}</td></tr>
        <tr><td style="font-weight: bold; vertical-align: top;">Description</td><td>{{ $quoteRequest->description }}</td></tr>
        <tr><td style="font-weight: bold;">Submitted</td><td>{{ $quoteRequest->submitted_at?->toDayDateTimeString() }}</td></tr>
    </table>

    <p style="margin-top: 24px; color: #666; font-size: 12px;">Wijhan admin — Quote request #{{ $quoteRequest->id }}</p>
</body>
</html>
