<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Receipt - {{ $sale->sale_number }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f1f5f9; padding: 24px 12px;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; background-color: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #142259; padding: 28px 24px; text-align: center; color: #ffffff;">
                            <div style="font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: #93c5fd; margin-bottom: 6px;">
                                Official Digital Receipt
                            </div>
                            <h1 style="margin: 0; font-size: 22px; font-weight: 800; letter-spacing: 0.5px; color: #ffffff;">
                                WELLA METAL CORPORATION
                            </h1>
                            <p style="margin: 4px 0 0 0; font-size: 12px; color: #cbd5e1;">
                                Toril, Davao City, Davao del Sur, Philippines
                            </p>
                        </td>
                    </tr>

                    <!-- Transaction Status Strip -->
                    <tr>
                        <td style="background-color: {{ ($receipt['payment_type'] ?? '') === 'layaway' ? '#fffbeb' : '#f0fdf4' }}; padding: 14px 24px; border-bottom: 1px solid {{ ($receipt['payment_type'] ?? '') === 'layaway' ? '#fde68a' : '#bbf7d0' }};">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: {{ ($receipt['payment_type'] ?? '') === 'layaway' ? '#b45309' : '#15803d' }};">
                                            {{ ($receipt['payment_type'] ?? '') === 'layaway' ? 'Lay-Away Contract Deposit' : 'Full Cash Settlement' }}
                                        </span>
                                        <div style="font-size: 16px; font-weight: 800; font-family: monospace; color: #0f172a; margin-top: 2px;">
                                            {{ $sale->sale_number }}
                                        </div>
                                    </td>
                                    <td align="right" style="vertical-align: middle;">
                                        <span style="display: inline-block; padding: 4px 10px; border-radius: 9999px; font-size: 11px; font-weight: 700; background-color: {{ ($receipt['payment_type'] ?? '') === 'layaway' ? '#fef3c7' : '#dcfce7' }}; color: {{ ($receipt['payment_type'] ?? '') === 'layaway' ? '#92400e' : '#166534' }}; border: 1px solid {{ ($receipt['payment_type'] ?? '') === 'layaway' ? '#fcd34d' : '#86efac' }};">
                                            {{ ($receipt['payment_type'] ?? '') === 'layaway' ? 'Reserved (3 Mos)' : 'Paid in Full' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Metadata Grid -->
                    <tr>
                        <td style="padding: 20px 24px; border-bottom: 1px solid #f1f5f9;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size: 12px;">
                                <tr>
                                    <td width="50%" style="vertical-align: top; padding-right: 12px;">
                                        <div style="color: #64748b; font-size: 10px; font-weight: 700; text-transform: uppercase; margin-bottom: 2px;">Customer Details:</div>
                                        <div style="font-weight: 700; color: #0f172a; font-size: 13px;">{{ $receipt['customer_name'] }}</div>
                                        <div style="color: #475569; margin-top: 2px;">{{ $receipt['customer_phone'] }}</div>
                                        @if(!empty($receipt['customer_email']))
                                            <div style="color: #2563eb; margin-top: 2px;">{{ $receipt['customer_email'] }}</div>
                                        @endif
                                        @if(!empty($receipt['customer_address']))
                                            <div style="color: #64748b; margin-top: 2px;">{{ $receipt['customer_address'] }}</div>
                                        @endif
                                    </td>
                                    <td width="50%" style="vertical-align: top; padding-left: 12px; text-align: right;">
                                        <div style="color: #64748b; font-size: 10px; font-weight: 700; text-transform: uppercase; margin-bottom: 2px;">Transaction Details:</div>
                                        <div style="color: #0f172a; font-weight: 600;">Date: {{ $receipt['sale_date'] }}</div>
                                        <div style="color: #475569; margin-top: 2px;">Cashier: {{ $receipt['cashier'] }}</div>
                                        <div style="color: #475569; margin-top: 2px;">Payment: Cash Basis</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Line Items Table -->
                    <tr>
                        <td style="padding: 20px 24px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size: 12px; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 2px solid #e2e8f0; text-align: left;">
                                        <th style="padding: 8px 0; color: #64748b; font-size: 11px; text-transform: uppercase; font-weight: 700;">Item / Description</th>
                                        <th style="padding: 8px 12px; color: #64748b; font-size: 11px; text-transform: uppercase; font-weight: 700; text-align: center;">Qty</th>
                                        <th style="padding: 8px 12px; color: #64748b; font-size: 11px; text-transform: uppercase; font-weight: 700; text-align: right;">Price</th>
                                        <th style="padding: 8px 0; color: #64748b; font-size: 11px; text-transform: uppercase; font-weight: 700; text-align: right;">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($receipt['items'] as $item)
                                        <tr style="border-bottom: 1px solid #f1f5f9;">
                                            <td style="padding: 10px 0; font-weight: 600; color: #0f172a;">
                                                {{ $item['name'] }}
                                            </td>
                                            <td style="padding: 10px 12px; text-align: center; font-family: monospace; color: #334155;">
                                                {{ $item['quantity'] }}
                                            </td>
                                            <td style="padding: 10px 12px; text-align: right; font-family: monospace; color: #334155;">
                                                ₱{{ number_format($item['unit_price'], 2) }}
                                            </td>
                                            <td style="padding: 10px 0; text-align: right; font-family: monospace; font-weight: 700; color: #0f172a;">
                                                ₱{{ number_format($item['subtotal'], 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>

                    <!-- Financial Summary Box -->
                    <tr>
                        <td style="padding: 0 24px 20px 24px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0; padding: 16px; font-size: 12px;">
                                <tr>
                                    <td style="padding-bottom: 6px; color: #64748b;">Subtotal:</td>
                                    <td align="right" style="padding-bottom: 6px; font-family: monospace; font-weight: 600; color: #334155;">
                                        ₱{{ number_format($receipt['total_amount'], 2) }}
                                    </td>
                                </tr>
                                @if(!empty($receipt['discount_amount']) && $receipt['discount_amount'] > 0)
                                    <tr>
                                        <td style="padding-bottom: 6px; color: #b45309;">
                                            Promo Discount ({{ $receipt['promo_name'] ?? 'Applied' }}):
                                        </td>
                                        <td align="right" style="padding-bottom: 6px; font-family: monospace; font-weight: 700; color: #b45309;">
                                            -₱{{ number_format($receipt['discount_amount'], 2) }}
                                        </td>
                                    </tr>
                                @endif
                                <tr style="border-top: 1px solid #cbd5e1;">
                                    <td style="padding: 8px 0; font-weight: 800; font-size: 14px; color: #0f172a;">
                                        Total Payable Amount:
                                    </td>
                                    <td align="right" style="padding: 8px 0; font-family: monospace; font-weight: 800; font-size: 16px; color: #142259;">
                                        ₱{{ number_format($receipt['net_amount'], 2) }}
                                    </td>
                                </tr>

                                @if(($receipt['payment_type'] ?? '') === 'full')
                                    <!-- Full Cash Details -->
                                    <tr style="border-top: 1px dashed #cbd5e1;">
                                        <td style="padding-top: 8px; color: #475569;">Cash Tendered:</td>
                                        <td align="right" style="padding-top: 8px; font-family: monospace; font-weight: 600; color: #0f172a;">
                                            ₱{{ number_format($receipt['cash_tendered'], 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-top: 4px; color: #15803d; font-weight: 700;">Change Given:</td>
                                        <td align="right" style="padding-top: 4px; font-family: monospace; font-weight: 700; color: #15803d;">
                                            ₱{{ number_format($receipt['change_due'], 2) }}
                                        </td>
                                    </tr>
                                @else
                                    <!-- Lay-Away Reservation Details -->
                                    <tr style="border-top: 1px dashed #cbd5e1;">
                                        <td style="padding-top: 8px; color: #b45309; font-weight: 700;">Downpayment Paid Today:</td>
                                        <td align="right" style="padding-top: 8px; font-family: monospace; font-weight: 700; color: #b45309;">
                                            ₱{{ number_format($receipt['downpayment_amount'], 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-top: 4px; color: #e11d48; font-weight: 700;">Remaining Balance Due:</td>
                                        <td align="right" style="padding-top: 4px; font-family: monospace; font-weight: 800; color: #e11d48;">
                                            ₱{{ number_format($receipt['remaining_balance'], 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-top: 4px; color: #64748b; font-size: 11px;">Reservation Expiry Deadline:</td>
                                        <td align="right" style="padding-top: 4px; font-family: monospace; font-weight: 700; color: #0f172a; font-size: 11px;">
                                            {{ $receipt['expiry_date'] ?? '3 Months' }}
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </td>
                    </tr>

                    <!-- Notice / Terms -->
                    <tr>
                        <td style="padding: 0 24px 20px 24px; font-size: 11px; color: #64748b; line-height: 1.5;">
                            @if(($receipt['payment_type'] ?? '') === 'layaway')
                                <div style="background-color: #fefce8; border-left: 3px solid #eab308; padding: 8px 12px; border-radius: 4px;">
                                    <strong style="color: #854d0e;">Lay-Away Policy:</strong> Reserved items are stored at our facility for up to 3 months. Installment payments can be settled in cash at our Toril branch. Upon full settlement, items will be released for pickup or dispatch.
                                </div>
                            @else
                                <div style="background-color: #f8fafc; border-left: 3px solid #64748b; padding: 8px 12px; border-radius: 4px;">
                                    <strong>Store Pickup / Delivery:</strong> Please present this official digital receipt or reference your order number ({{ $sale->sale_number }}) upon pickup or receipt of metal furniture items. Thank you for choosing Wella Metal Corporation!
                                </div>
                            @endif
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 18px 24px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0;">
                            <div>&copy; {{ date('Y') }} Wella Metal Corporation. All rights reserved.</div>
                            <div style="margin-top: 3px;">Toril, Davao City &middot; Quality Metal &amp; Steel Fabrication</div>
                            <div style="margin-top: 4px; color: #cbd5e1; font-size: 10px;">This is an automated system-generated electronic receipt.</div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
