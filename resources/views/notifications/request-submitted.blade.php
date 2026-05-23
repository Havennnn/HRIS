<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #1a1a2e; }
        .container { max-width: 560px; margin: 0 auto; padding: 24px; }
        .header { background: #1e40af; color: white; padding: 24px; border-radius: 8px 8px 0 0; }
        .header h1 { margin: 0; font-size: 20px; }
        .body { padding: 24px; background: #f8fafc; border: 1px solid #e2e8f0; }
        .detail-row { margin: 8px 0; }
        .detail-row .label { font-size: 12px; color: #64748b; text-transform: uppercase; }
        .detail-row .value { font-size: 16px; font-weight: 600; color: #1a1a2e; }
        .footer { padding: 16px; text-align: center; font-size: 12px; color: #94a3b8; }
        .btn { display: inline-block; padding: 10px 20px; background: #1e40af; color: white; text-decoration: none; border-radius: 6px; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Request Submitted</h1>
        </div>
        <div class="body">
            <p><strong>{{ $request->employee?->name ?? 'An employee' }}</strong> has submitted a new request.</p>

            <div class="detail-row">
                <div class="label">Type</div>
                <div class="value">{{ $request->type?->label() ?? 'Request' }}</div>
            </div>

            <div class="detail-row">
                <div class="label">Date</div>
                <div class="value">{{ $request->requested_date?->format('M d, Y') }}</div>
            </div>

            @if ($request->end_date)
            <div class="detail-row">
                <div class="label">End Date</div>
                <div class="value">{{ $request->end_date?->format('M d, Y') }}</div>
            </div>
            @endif

            @if ($request->message)
            <div class="detail-row">
                <div class="label">Message</div>
                <div class="value">{{ $request->message }}</div>
            </div>
            @endif

            <p style="text-align: center;">
                <a href="{{ url('/requests/' . $request->id) }}" class="btn">Review Request</a>
            </p>
        </div>
        <div class="footer">
            <p>HRIS — Human Resource Information System</p>
        </div>
    </div>
</body>
</html>
