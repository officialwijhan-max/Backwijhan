<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>New contact enquiry</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1a1a1a; line-height: 1.6;">
    <h2 style="margin-bottom: 4px;">New contact enquiry</h2>
    <p style="color: #666; margin-top: 0;">Submitted via {{ $contact->source }} ({{ strtoupper($contact->locale) }})</p>

    <table cellpadding="6" cellspacing="0" style="border-collapse: collapse; width: 100%; max-width: 560px;">
        <tr><td style="font-weight: bold; width: 160px;">Name</td><td>{{ $contact->name }}</td></tr>
        <tr><td style="font-weight: bold;">Company</td><td>{{ $contact->company ?? '—' }}</td></tr>
        <tr><td style="font-weight: bold;">Email</td><td>{{ $contact->email }}</td></tr>
        <tr><td style="font-weight: bold;">Phone</td><td>{{ $contact->phone ?? '—' }}</td></tr>
        <tr><td style="font-weight: bold;">Project type</td><td>{{ $contact->project_type ?? '—' }}</td></tr>
        <tr><td style="font-weight: bold;">Budget</td><td>{{ $contact->budget_range ?? '—' }}</td></tr>
        <tr><td style="font-weight: bold;">Subject</td><td>{{ $contact->subject ?? '—' }}</td></tr>
        <tr><td style="font-weight: bold; vertical-align: top;">Message</td><td>{{ $contact->message }}</td></tr>
        <tr><td style="font-weight: bold;">Submitted</td><td>{{ $contact->submitted_at?->toDayDateTimeString() }}</td></tr>
    </table>

    <p style="margin-top: 24px; color: #666; font-size: 12px;">Wijhan admin — Contact #{{ $contact->id }}</p>
</body>
</html>
