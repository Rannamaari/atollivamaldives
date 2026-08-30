@php
    $logoPath = public_path('logo/optimized/atolliva-logo.png');
    $logoData = file_exists($logoPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath)) : null;
    $addressLines = array_filter($company['address_lines'] ?? []);
    $taxRows = collect($quotation->taxes ?? []);
    $orderedTaxRows = collect([
        ['name' => 'Service Charge', 'total' => (float) optional($taxRows->firstWhere('name', 'Service Charge'))['total'] ?? 0],
        ['name' => 'TGST', 'total' => (float) optional($taxRows->firstWhere('name', 'TGST'))['total'] ?? 0],
        ['name' => 'Green Tax', 'total' => (float) optional($taxRows->firstWhere('name', 'Green Tax'))['total'] ?? 0],
    ]);
    $paymentLines = preg_split('/\r\n|\r|\n/', trim((string) $quotation->payment_notes)) ?: [];
    $noteLines = preg_split('/\r\n|\r|\n/', trim((string) $quotation->notes)) ?: [];
    $itineraryLines = preg_split('/\r\n|\r|\n/', trim((string) $quotation->itinerary)) ?: [];
    $termsLines = preg_split('/\r\n|\r|\n/', trim((string) ($company['quotation_terms'] ?? \App\Models\SiteSetting::current()->quotation_terms))) ?: [];
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $quotation->quotation_number }} | Atolliva Maldives Quotation</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{color-scheme:light}
        *{box-sizing:border-box}
        @page{size:A4 portrait;margin:16mm 14mm 14mm}
        html,body{margin:0;padding:0;font-family:'Inter',Arial,sans-serif;color:#10336b;background:#dfeafc}
        body{min-width:210mm}
        .page{width:182mm;min-height:267mm;margin:8mm auto;padding:10.5mm 10.5mm 7.5mm;background:#fff;box-shadow:0 18px 48px rgba(16,51,107,.14)}
        .toolbar{display:flex;justify-content:flex-end;gap:12px;margin-bottom:18px}
        .toolbar a,.toolbar button{display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:0 18px;border:1px solid #1f66d1;border-radius:999px;background:#fff;color:#1f66d1;font-size:13px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;cursor:pointer;text-decoration:none}
        .header,.section-grid,.summary-grid,.footer-grid,.trip-grid{display:grid;gap:18px}
        .header{grid-template-columns:minmax(0,1fr) auto;align-items:start;padding-bottom:10px;border-bottom:1.5px solid #9bc1ff}
        .brand{display:flex;align-items:center;gap:16px}
        .brand img{width:34mm;height:auto;display:block}
        .contact{display:grid;gap:2px;font-size:8.6pt;line-height:1.35}
        .contact strong{font-size:9.1pt}
        h1{margin:6mm 0 3mm;font:600 21pt/.95 'Cormorant Garamond',serif;letter-spacing:-.02em;color:#123f8d}
        .section-grid{grid-template-columns:minmax(0,1.18fr) 60mm;align-items:start;gap:6.5mm}
        .bill-card{padding-top:4px}
        .meta-card,.totals-box{border:1px solid #8bb5ff;border-radius:4px;padding:2.7mm 3.2mm;background:#fff;page-break-inside:avoid}
        .meta-row,.total-row{display:grid;grid-template-columns:minmax(24mm,1fr) auto;gap:4mm;padding:1.75mm 0;border-bottom:1px solid #d5e4ff;align-items:baseline}
        .meta-row:last-child,.total-row:last-child{border-bottom:0}
        .meta-row strong,.total-row strong,.total-row span:first-child{white-space:nowrap}
        .meta-row strong{font-size:8.9pt;color:#123f8d}
        .meta-row span{font-size:8.9pt;color:#173a74;text-align:right}
        .meta-row--number span{font-size:10pt;font-weight:700;color:#123f8d}
        .bill-label{display:flex;align-items:center;gap:12px;margin-bottom:3mm;font-size:9.8pt;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#1f66d1}
        .bill-lines{display:grid;gap:1.9mm;font-size:9pt;color:#163b79}
        .bill-line{display:grid;grid-template-columns:29mm 1fr;gap:3mm}
        .bill-line span:last-child{min-height:5.4mm;border-bottom:1px solid #bfd4ff}
        table{width:100%;border-collapse:collapse;margin-top:4.8mm;border:1px solid #8bb5ff;page-break-inside:auto}
        thead{display:table-header-group}
        tr{page-break-inside:avoid}
        th,td{padding:2.35mm 2.8mm;border:1px solid #d2e0ff;vertical-align:top}
        th{background:#f4f8ff;color:#1f66d1;font-size:8.4pt;font-weight:700}
        td{font-size:8.7pt;color:#173a74;line-height:1.35}
        .num{text-align:right;white-space:nowrap}
        .trip-grid{grid-template-columns:repeat(4,minmax(0,1fr));margin-top:4.2mm;gap:2.4mm}
        .trip-box{padding:2.6mm 3mm;border:1px solid #d5e4ff;border-radius:5px;background:#f9fbff;min-height:14.5mm}
        .trip-box span{display:block;font-size:7.1pt;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#6f8ec2}
        .trip-box strong{display:block;margin-top:1.4mm;font-size:8.9pt;font-weight:600;color:#123f8d}
        .summary-grid{grid-template-columns:minmax(0,1fr) 60mm;margin-top:4.8mm;align-items:start;gap:6.5mm}
        .footer-grid{display:grid;gap:4mm}
        .panel{border-top:1.5px solid #d4e2ff;padding-top:3.2mm;page-break-inside:avoid}
        .panel h2{display:flex;align-items:center;gap:10px;margin:0 0 2.3mm;font:600 11.5pt/1 'Cormorant Garamond',serif;letter-spacing:.04em;text-transform:uppercase;color:#1f66d1}
        .panel p,.panel li{margin:0 0 1.3mm;font-size:8.4pt;line-height:1.4;color:#173a74}
        .panel ul{margin:0;padding-left:15px}
        .total-row span{font-size:8.9pt;color:#173a74}
        .total-row strong{font-size:8.9pt;color:#123f8d}
        .total-row--subtotal span:last-child,.total-row--amount span:last-child,.total-row--amount strong:last-child{text-align:right}
        .total-row--divider{margin-top:1mm;padding-top:2.2mm;border-top:1px solid #bfd4ff}
        .total-row--grand{margin-top:1.1mm;padding:2.4mm 2.2mm;border-bottom:0;border-radius:3px;background:#edf4ff}
        .total-row--grand strong{font-size:9.8pt;font-weight:700}
        .muted{color:#5b74a4}
        .tiny{font-size:7.3pt}
        .terms{margin-top:5.5mm;padding-top:3.2mm;border-top:1.5px solid #d4e2ff;page-break-inside:avoid}
        .terms h2{margin:0 0 2.3mm;font:600 11.5pt/1 'Cormorant Garamond',serif;letter-spacing:.04em;text-transform:uppercase;color:#1f66d1}
        .terms ol{margin:0;padding-left:4.5mm}
        .terms li{margin:0 0 1.2mm;font-size:8.2pt;line-height:1.38;color:#173a74}
        .document-footer{margin-top:5.5mm;padding-top:2.4mm;border-top:1px solid #d5e4ff;text-align:center;font-size:7.3pt;line-height:1.38;color:#6f83ad;page-break-inside:avoid}
        @media print{
            html,body{background:#fff}
            body{min-width:auto}
            .page{width:auto;min-height:auto;margin:0;padding:0;box-shadow:none}
            .toolbar{display:none}
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="toolbar">
            <a href="{{ url()->previous() }}">Back</a>
            <button type="button" onclick="window.print()">Print / Save PDF</button>
        </div>

        <section class="header">
            <div class="brand">
                @if($logoData)
                    <img src="{{ $logoData }}" alt="Atolliva Maldives">
                @endif
            </div>
            <div class="contact">
                <strong>{{ $company['name'] ?? 'Atolliva Maldives' }}</strong>
                @foreach($addressLines as $line)
                    <div>{{ $line }}</div>
                @endforeach
                <div>{{ $company['email'] ?? 'hello@atollivamaldives.com' }}</div>
                <div>{{ preg_replace('#^https?://#', '', $company['website'] ?? 'atollivamaldives.com') }}</div>
                <div>{{ $company['phone'] ?? '+960 9996210' }} / {{ $company['secondary_phone'] ?? '+960 7779493' }}</div>
            </div>
        </section>

        <h1>QUOTATION</h1>

        <section class="section-grid">
            <div class="bill-card">
                <div class="bill-label">Bill To</div>
                <div class="bill-lines">
                    <div class="bill-line"><span>Customer Name</span><span>{{ $quotation->customer_name }}</span></div>
                    <div class="bill-line"><span>Company</span><span>{{ $quotation->company_name }}</span></div>
                    <div class="bill-line"><span>Address</span><span>{{ $quotation->customer_address }}</span></div>
                    <div class="bill-line"><span>Phone</span><span>{{ $quotation->customer_phone }}</span></div>
                    <div class="bill-line"><span>Email</span><span>{{ $quotation->customer_email }}</span></div>
                </div>
            </div>
            <div class="meta-card">
                <div class="meta-row meta-row--number"><strong>Quotation No.</strong><span>{{ $quotation->quotation_number }}</span></div>
                <div class="meta-row"><strong>Quotation Date</strong><span>{{ optional($quotation->quotation_date)->format('d M Y') }}</span></div>
                <div class="meta-row"><strong>Valid Until</strong><span>{{ optional($quotation->valid_until)->format('d M Y') }}</span></div>
                <div class="meta-row"><strong>Reference</strong><span>{{ $quotation->reference }}</span></div>
            </div>
        </section>

        <section class="trip-grid">
            <div class="trip-box">
                <span>Stay</span>
                <strong>{{ $quotation->nights }} night{{ $quotation->nights === 1 ? '' : 's' }}</strong>
            </div>
            <div class="trip-box">
                <span>Guests</span>
                <strong>{{ $quotation->adults }} adult{{ $quotation->adults === 1 ? '' : 's' }}@if($quotation->children), {{ $quotation->children }} child{{ $quotation->children === 1 ? '' : 'ren' }}@endif</strong>
            </div>
            <div class="trip-box">
                <span>Check-in</span>
                <strong>{{ optional($quotation->check_in)->format('d M Y') ?: 'To be confirmed' }}</strong>
            </div>
            <div class="trip-box">
                <span>Check-out</span>
                <strong>{{ optional($quotation->check_out)->format('d M Y') ?: 'To be confirmed' }}</strong>
            </div>
        </section>

        <table>
            <thead>
                <tr>
                    <th style="width:70px">#</th>
                    <th>Description</th>
                    <th style="width:120px">Qty</th>
                    <th style="width:180px">Unit Price ({{ $quotation->currency }})</th>
                    <th style="width:180px">Amount ({{ $quotation->currency }})</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items ?? [] as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item['description'] ?? '' }}</strong>
                            @if($index === 0)
                                <div class="muted tiny" style="margin-top:8px;">
                                    {{ $quotation->property_name ?: 'Maldives travel service' }}
                                    <br>Priced at {{ $quotation->currency }} {{ number_format((float) ($item['unit_price'] ?? 0), 2) }} per night
                                </div>
                            @endif
                        </td>
                        <td class="num">{{ number_format((float) ($item['qty'] ?? 0), 2) }}</td>
                        <td class="num">{{ number_format((float) ($item['unit_price'] ?? 0), 2) }}</td>
                        <td class="num">{{ number_format((float) ($item['amount'] ?? 0), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <section class="summary-grid">
            <div class="footer-grid">
                @if(filled(trim((string) $quotation->itinerary)))
                    <div class="panel">
                        <h2>Itinerary / Inclusions</h2>
                        <ul>
                            @foreach($itineraryLines as $line)
                                @if(filled($line))
                                    <li>{{ $line }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="panel">
                    <h2>Payment Details</h2>
                    @forelse($paymentLines as $line)
                        @if(filled($line))
                            <p>{{ $line }}</p>
                        @endif
                    @empty
                        <p class="muted">Payment details can be added here when needed.</p>
                    @endforelse
                </div>
                <div class="panel">
                    <h2>Notes</h2>
                    @forelse($noteLines as $line)
                        @if(filled($line))
                            <p>{{ $line }}</p>
                        @endif
                    @empty
                        <p class="muted">Rates are subject to availability until confirmed.</p>
                    @endforelse
                </div>
            </div>

            <div class="totals-box">
                <div class="total-row total-row--subtotal"><span>Subtotal</span><span>{{ $quotation->currency }} {{ number_format((float) $quotation->subtotal, 2) }}</span></div>
                @foreach($orderedTaxRows as $tax)
                    <div class="total-row total-row--amount">
                        <span>{{ $tax['name'] }}</span>
                        <span>{{ $quotation->currency }} {{ number_format((float) ($tax['total'] ?? 0), 2) }}</span>
                    </div>
                @endforeach
                <div class="total-row total-row--divider">
                    <strong>Total</strong>
                    <strong>{{ $quotation->currency }} {{ number_format((float) $quotation->grand_total, 2) }}</strong>
                </div>
                <div class="total-row total-row--grand"><strong>Total Due ({{ $quotation->currency }})</strong><strong>{{ number_format((float) $quotation->grand_total, 2) }}</strong></div>
            </div>
        </section>

        @if(collect($termsLines)->filter(fn ($line) => filled($line))->isNotEmpty())
            <section class="terms">
                <h2>Terms & Conditions</h2>
                <ol>
                    @foreach($termsLines as $line)
                        @if(filled($line))
                            <li>{{ $line }}</li>
                        @endif
                    @endforeach
                </ol>
            </section>
        @endif

        <section class="document-footer">
            <p>This is a system-generated quotation from Atolliva Maldives and does not require a signature.</p>
            <p>{{ $company['name'] ?? 'Atolliva Maldives' }} • {{ $company['email'] ?? 'hello@atollivamaldives.com' }} • {{ preg_replace('#^https?://#', '', $company['website'] ?? 'atollivamaldives.com') }}</p>
        </section>
    </div>
</body>
</html>
