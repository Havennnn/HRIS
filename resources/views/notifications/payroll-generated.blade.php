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
        .amount-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin: 16px 0; }
        .amount-box { background: white; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; text-align: center; }
        .amount-box .label { font-size: 12px; color: #64748b; text-transform: uppercase; }
        .amount-box .value { font-size: 18px; font-weight: 700; color: #1a1a2e; }
        .footer { padding: 16px; text-align: center; font-size: 12px; color: #94a3b8; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-approved { background: #dcfce7; color: #166534; }
        .btn { display: inline-block; padding: 10px 20px; background: #1e40af; color: white; text-decoration: none; border-radius: 6px; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Payroll Generated</h1>
        </div>
        <div class="body">
            <p>Your payroll for the period <strong>{{ $payroll->pay_period_start->format('M d, Y') }}</strong> to <strong>{{ $payroll->pay_period_end->format('M d, Y') }}</strong> has been generated.</p>

            <div class="amount-grid">
                <div class="amount-box">
                    <div class="label">Gross Pay</div>
                    <div class="value">₱{{ number_format($payroll->gross_pay, 2) }}</div>
                </div>
                <div class="amount-box">
                    <div class="label">Net Pay</div>
                    <div class="value">₱{{ number_format($payroll->net_pay, 2) }}</div>
                </div>
            </div>

            <p>Status: <span class="badge badge-{{ $payroll->status->value === 1 ? 'pending' : 'approved' }}">{{ $payroll->status->label() }}</span></p>

            <p style="text-align: center;">
                <a href="{{ url('/payrolls/' . $payroll->id) }}" class="btn">View Payroll Details</a>
            </p>
        </div>
        <div class="footer">
            <p>HRIS — Human Resource Information System</p>
        </div>
    </div>
</body>
</html>
