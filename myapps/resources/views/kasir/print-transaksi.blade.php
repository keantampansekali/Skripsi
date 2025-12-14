<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Transaksi - {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}</title>
    <style>
        @media print {
            body { margin: 0; padding: 10px; }
            .no-print { display: none; }
            @page { margin: 10mm; }
        }
        body {
            font-family: 'Courier New', monospace;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            color: black;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .summary {
            background: #f5f5f5;
            padding: 15px;
            border: 2px solid #000;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background: #e5e5e5;
            padding: 10px;
            text-align: left;
            border: 1px solid #000;
            font-weight: bold;
        }
        td {
            padding: 8px 10px;
            border: 1px solid #ccc;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px dashed #000;
        }
        .total-row {
            font-weight: bold;
            background: #f0f0f0;
        }
        .item-detail {
            font-size: 0.85em;
            color: #666;
            padding-left: 15px;
        }
        .buttons {
            margin-top: 20px;
            text-align: center;
        }
        button {
            padding: 10px 20px;
            margin: 5px;
            font-size: 16px;
            cursor: pointer;
            border: 2px solid #dc2626;
            border-radius: 5px;
            background: white;
            color: #dc2626;
        }
        button:hover {
            background: #dc2626;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN TRANSAKSI KASIR</h1>
        <h2>{{ $cabang }}</h2>
        <p><strong>Tanggal: {{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}</strong></p>
    </div>

    <div class="summary">
        <table>
            <tr>
                <td><strong>Total Transaksi:</strong></td>
                <td class="text-right"><strong>{{ $totalTransaksi }} transaksi</strong></td>
            </tr>
            <tr class="total-row">
                <td><strong>TOTAL PENDAPATAN:</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($totalHari, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No Transaksi</th>
                <th>Waktu</th>
                <th>Items</th>
                <th class="text-right">Subtotal</th>
                <th class="text-right">Diskon</th>
                <th class="text-right">Tax</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksis as $index => $trx)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $trx->no_transaksi }}</strong></td>
                <td>{{ \Carbon\Carbon::parse($trx->created_at)->format('H:i:s') }}</td>
                <td>
                    @foreach($trx->items as $item)
                    <div>{{ $item->nama_produk }} ({{ $item->quantity }}x)</div>
                    @endforeach
                </td>
                <td class="text-right">Rp {{ number_format($trx->subtotal, 0, ',', '.') }}</td>
                <td class="text-right">
                    Rp {{ number_format($trx->diskon, 0, ',', '.') }}
                    @if($trx->tipe_diskon === 'percent')
                    <br><span class="item-detail">({{ $trx->nilai_diskon }}%)</span>
                    @endif
                </td>
                <td class="text-right">Rp {{ number_format($trx->tax, 0, ',', '.') }}</td>
                <td class="text-right"><strong>Rp {{ number_format($trx->total, 0, ',', '.') }}</strong></td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right"><strong>GRAND TOTAL:</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($transaksis->sum('subtotal'), 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($transaksis->sum('diskon'), 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($transaksis->sum('tax'), 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($totalHari, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p><strong>Laporan dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y H:i:s') }}</strong></p>
    </div>

    <div class="buttons no-print">
        <button onclick="window.print()">🖨️ Print</button>
        <button onclick="window.close()">✕ Tutup</button>
    </div>
</body>
</html>

