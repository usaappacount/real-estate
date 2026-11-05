<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $inv->invoice_number }}.pdf</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Readex+Pro:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body, body *, table, th, td, div, span, b, .font-mono {
            font-family: 'Readex Pro', 'Tahoma', 'Arial', sans-serif !important;
            letter-spacing: normal !important;
            word-spacing: normal !important;
            font-feature-settings: "liga" 1, "calt" 1 !important;
            text-rendering: optimizeLegibility !important;
        }
        body { background: #f8fafc; }
        tr, thead, tfoot, .pdf-block { page-break-inside: avoid !important; break-inside: avoid !important; }
    </style>
</head>
<body class="p-6">
    <div class="max-w-2xl mx-auto mb-4 flex justify-between items-center bg-slate-900 text-white p-4 rounded-xl no-print">
        <span class="text-xs font-bold">جاري تحميل ملف الفاتورة الضريبية الأصلية PDF تلقائياً...</span>
        <button onclick="downloadNow()" class="px-4 py-1.5 rounded-lg bg-emerald-600 text-xs font-bold">📥 تحميل PDF</button>
    </div>

    <div id="invoicePdfArea" class="max-w-2xl mx-auto p-8 rounded-2xl border-2 border-slate-900 bg-white space-y-5 text-slate-900" dir="rtl">
        <div class="flex items-start justify-between border-b-2 border-slate-900 pb-4 pdf-block">
            <div>
                <div class="text-lg font-extrabold text-emerald-800">{{ $inv->invoice_type == 'tax' ? 'فاتورة ضريبية' : 'فاتورة ضريبية مبسطة' }}</div>
                <div class="text-xs text-slate-500">Tax Invoice — متوافقة مع هيئة الزكاة والضريبة والجمارك (ZATCA)</div>
                <div class="mt-2 text-xs font-bold">رقم الفاتورة: {{ $inv->invoice_number }}</div>
                <div class="text-xs">تاريخ الإصدار: {{ $inv->issue_date }}</div>
            </div>
            <div class="text-center">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($inv->zatca_qr) }}" class="w-24 h-24 border rounded-xl p-1 mx-auto">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4 text-xs pdf-block">
            <div class="p-3.5 rounded-xl bg-slate-50 border">
                <div class="font-bold text-slate-500">المُصدِر (البائع):</div>
                <div class="font-extrabold text-sm">{{ $inv->seller_name }}</div>
                <div>الرقم الضريبي: <b>{{ $inv->seller_tax_number ?: 'غير مسجل' }}</b></div>
                <div>العنوان: {{ $inv->seller_address ?: '-' }}</div>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border">
                <div class="font-bold text-slate-500">العميل (المشتري):</div>
                <div class="font-extrabold text-sm">{{ $inv->buyer_name }}</div>
                <div>الرقم الضريبي: <b>{{ $inv->buyer_tax_number ?: 'فاتورة مبسطة' }}</b></div>
                <div>العنوان: {{ $inv->buyer_address ?: '-' }}</div>
            </div>
        </div>
        <table class="w-full text-xs text-right border border-slate-300">
            <thead class="bg-slate-900 text-white">
                <tr>
                    <th class="py-2.5 px-3">البيان</th>
                    <th class="py-2.5 px-3">قبل الضريبة</th>
                    <th class="py-2.5 px-3">الضريبة ({{ (float)$inv->vat_rate }}%)</th>
                    <th class="py-2.5 px-3">الإجمالي شامل الضريبة</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-3 px-3 font-bold">{{ $inv->description }}</td>
                    <td class="py-3 px-3">{{ number_format($inv->subtotal, 2) }} ر.س</td>
                    <td class="py-3 px-3 text-amber-700 font-bold">{{ number_format($inv->vat_amount, 2) }} ر.س</td>
                    <td class="py-3 px-3 font-extrabold text-emerald-700">{{ number_format($inv->total_amount, 2) }} ر.س</td>
                </tr>
            </tbody>
        </table>
    </div>
    <script>
        async function downloadNow() {
            if (document.fonts && document.fonts.ready) await document.fonts.ready;
            const el = document.getElementById('invoicePdfArea');
            html2pdf().set({
                margin: 10,
                filename: '{{ $inv->invoice_number }}.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true, letterRendering: false, backgroundColor: '#ffffff' },
                pagebreak: { mode: ['css', 'legacy'], avoid: ['tr', '.pdf-block'] },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            }).from(el).save();
        }
        window.onload = function() { setTimeout(downloadNow, 600); };
    </script>
</body>
</html>