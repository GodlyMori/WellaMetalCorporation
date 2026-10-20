<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lay-Away Due Reminder - {{ $sale->sale_number }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f1f5f9; padding: 24px 12px;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; background-color: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #142259; padding: 26px 24px; text-align: center; color: #ffffff;">
                            <div style="font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: #f59e0b; margin-bottom: 6px;">
                                Lay-Away Account Notification
                            </div>
                            <h1 style="margin: 0; font-size: 22px; font-weight: 800; letter-spacing: 0.5px; color: #ffffff;">
                                WELLA METAL CORPORATION
                            </h1>
                            <p style="margin: 4px 0 0 0; font-size: 12px; color: #cbd5e1;">
                                Toril, Davao City, Davao del Sur, Philippines
                            </p>
                        </td>
                    </tr>

                    <!-- Alert Banner -->
                    <tr>
                        <td style="background-color: #fffbeb; padding: 16px 24px; border-bottom: 1px solid #fde68a;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #b45309;">
                                            Payment Due Notice &bull; {{ $daysRemaining }} Days Remaining
                                        </div>
                                        <div style="font-size: 16px; font-weight: 800; font-family: monospace; color: #0f172a; margin-top: 2px;">
                                            Contract #{{ $sale->sale_number }}
                                        </div>
                                    </td>
                                    <td align="right" style="vertical-align: middle;">
                                        <span style="display: inline-block; padding: 5px 12px; border-radius: 9999px; font-size: 11px; font-weight: 700; background-color: #fef3c7; color: #92400e; border: 1px solid #fcd34d;">
                                            Expires {{ $sale->layaway_expires_at?->format('M d, Y') }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Customer Greeting -->
                    <tr>
                        <td style="padding: 24px; border-bottom: 1px solid #f1f5f9;">
                            <p style="margin: 0 0 12px 0; font-size: 14px; font-weight: 600; color: #0f172a;">
                                Dear {{ $sale->customer_name }},
                            </p>
                            <p style="margin: 0 0 12px 0; font-size: 13px; line-height: 1.6; color: #475569;">
                                This is a friendly reminder from <strong>Wella Metal Corporation</strong> regarding your active lay-away reservation. Your 90-day reservation period will conclude on <strong>{{ $sale->layaway_expires_at?->format('F j, Y') }}</strong> ({{ $daysRemaining }} calendar day{{ $daysRemaining === 1 ? '' : 's' }} left).
                            </p>
                            <p style="margin: 0; font-size: 13px; line-height: 1.6; color: #475569;">
                                To complete your order and schedule delivery or pickup of your reserved metalcraft items, kindly settle the remaining balance below before the deadline.
                            </p>
                        </td>
                    </tr>

                    <!-- Balance Summary Strip -->
                    <tr>
                        <td style="padding: 20px 24px; background-color: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size: 13px;">
                                <tr>
                                    <td style="color: #64748b; padding-bottom: 6px;">Total Contract Value:</td>
                                    <td align="right" style="font-family: monospace; font-weight: 600; color: #0f172a; padding-bottom: 6px;">₱{{ number_format($sale->amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="color: #64748b; padding-bottom: 6px;">Total Payments Received:</td>
                                    <td align="right" style="font-family: monospace; font-weight: 600; color: #166534; padding-bottom: 6px;">-₱{{ number_format($sale->amount_paid, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding-top: 8px; border-top: 1.5px dashed #cbd5e1; font-weight: 700; color: #0f172a; font-size: 14px;">Remaining Balance Payable:</td>
                                    <td align="right" style="padding-top: 8px; border-top: 1.5px dashed #cbd5e1; font-family: monospace; font-weight: 800; color: #b45309; font-size: 16px;">₱{{ number_format($sale->remaining_balance, 2) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Reserved Items Table -->
                    <tr>
                        <td style="padding: 20px 24px;">
                            <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; margin-bottom: 10px;">
                                Reserved Metalcraft Items
                            </div>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size: 12px; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid #cbd5e1;">
                                        <th align="left" style="padding: 6px 0; color: #64748b; font-weight: 600;">Item</th>
                                        <th align="center" style="padding: 6px 0; color: #64748b; font-weight: 600;">Qty</th>
                                        <th align="right" style="padding: 6px 0; color: #64748b; font-weight: 600;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sale->items as $item)
                                        <tr style="border-bottom: 1px solid #f1f5f9;">
                                            <td style="padding: 8px 0; color: #0f172a; font-weight: 500;">
                                                {{ $item->product?->name ?? 'Custom Metalcraft Product' }}
                                            </td>
                                            <td align="center" style="padding: 8px 0; color: #475569;">
                                                {{ $item->quantity }}
                                            </td>
                                            <td align="right" style="padding: 8px 0; font-family: monospace; font-weight: 600; color: #0f172a;">
                                                ₱{{ number_format($item->subtotal, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" style="padding: 8px 0; color: #0f172a;">
                                                {{ $sale->product_name ?: 'Reserved Metalcraft Unit' }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </td>
                    </tr>

                    <!-- Payment Instructions & Footer -->
                    <tr>
                        <td style="padding: 18px 24px; background-color: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 11px; color: #64748b; line-height: 1.5;">
                            <div style="font-weight: 700; color: #334155; margin-bottom: 4px;">Payment Instructions:</div>
                            <p style="margin: 0 0 8px 0;">
                                Installments can be paid directly at our showroom or via GCash/Bank Transfer. Please present your contract number <strong>{{ $sale->sale_number }}</strong> to our cashier when making payments.
                            </p>
                            <p style="margin: 0; color: #94a3b8; font-size: 10px;">
                                &copy; {{ date('Y') }} Wella Metal Corporation. All rights reserved. This is an automated notification.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
