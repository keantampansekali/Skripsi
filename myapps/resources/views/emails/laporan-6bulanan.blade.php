<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan 6 Bulanan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #4CAF50;
            margin: 0;
            font-size: 28px;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .period {
            background-color: #e8f5e9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
            text-align: center;
        }
        .period strong {
            color: #2e7d32;
            font-size: 18px;
        }
        .cabang-section {
            margin-bottom: 40px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            background-color: #fafafa;
        }
        .cabang-title {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            margin: -20px -20px 20px -20px;
            border-radius: 5px 5px 0 0;
            font-size: 20px;
            font-weight: bold;
        }
        .report-section {
            margin-bottom: 30px;
        }
        .report-title {
            background-color: #2196F3;
            color: white;
            padding: 12px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 18px;
            font-weight: bold;
        }
        .summary-box {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #2196F3;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .summary-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 16px;
            color: #2e7d32;
            margin-top: 10px;
            padding-top: 15px;
            border-top: 2px solid #2e7d32;
        }
        .summary-label {
            font-weight: 600;
            color: #555;
        }
        .summary-value {
            color: #333;
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background-color: white;
        }
        th {
            background-color: #2196F3;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #e0e0e0;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Laporan 6 Bulanan</h1>
            <p>Sistem Manajemen Inventori</p>
        </div>

        <div class="period">
            <strong>Periode: {{ $startDate->locale('id')->format('d F Y') }} - {{ $endDate->locale('id')->format('d F Y') }}</strong>
        </div>

        @foreach($laporanData as $data)
        <div class="cabang-section">
            <div class="cabang-title">
                🏢 {{ $data['cabang']['nama'] }}
            </div>

            <!-- Laporan Penjualan -->
            <div class="report-section">
                <div class="report-title">💰 Laporan Penjualan</div>
                
                <div class="summary-box">
                    <div class="summary-row">
                        <span class="summary-label">Total Transaksi:</span>
                        <span class="summary-value">{{ number_format($data['penjualan']['summary']['total_transaksi'], 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Total Subtotal:</span>
                        <span class="summary-value">Rp {{ number_format($data['penjualan']['summary']['total_subtotal'], 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Total Diskon:</span>
                        <span class="summary-value">Rp {{ number_format($data['penjualan']['summary']['total_diskon'], 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Total Pajak:</span>
                        <span class="summary-value">Rp {{ number_format($data['penjualan']['summary']['total_tax'], 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Total Penjualan:</span>
                        <span class="summary-value">Rp {{ number_format($data['penjualan']['summary']['total_penjualan'], 0, ',', '.') }}</span>
                    </div>
                </div>

                @if(count($data['penjualan']['detail']) > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th class="text-right">Jumlah Transaksi</th>
                            <th class="text-right">Subtotal</th>
                            <th class="text-right">Diskon</th>
                            <th class="text-right">Pajak</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['penjualan']['detail'] as $detail)
                        <tr>
                            <td>{{ $detail['bulan'] }}</td>
                            <td class="text-right">{{ number_format($detail['jumlah_transaksi'], 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($detail['subtotal'], 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($detail['diskon'], 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($detail['tax'], 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($detail['total'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="no-data">Tidak ada data penjualan pada periode ini</div>
                @endif
            </div>

            <!-- Laporan Stok -->
            <div class="report-section">
                <div class="report-title">📦 Laporan Stok (Per {{ $endDate->locale('id')->format('d F Y') }})</div>
                
                <div class="summary-box">
                    <div class="summary-row">
                        <span class="summary-label">Total Bahan Baku:</span>
                        <span class="summary-value">{{ number_format($data['stok']['total_bahan_baku'], 0, ',', '.') }} unit</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Total Produk:</span>
                        <span class="summary-value">{{ number_format($data['stok']['total_produk'], 0, ',', '.') }} unit</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Nilai Bahan Baku:</span>
                        <span class="summary-value">Rp {{ number_format($data['stok']['total_nilai_bahan_baku'], 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Nilai Produk:</span>
                        <span class="summary-value">Rp {{ number_format($data['stok']['total_nilai_produk'], 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Total Nilai Stok:</span>
                        <span class="summary-value">Rp {{ number_format($data['stok']['total_nilai'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Laporan Pembelian -->
            <div class="report-section">
                <div class="report-title">🛒 Laporan Pembelian</div>
                
                <div class="summary-box">
                    <div class="summary-row">
                        <span class="summary-label">Total Pembelian:</span>
                        <span class="summary-value">{{ number_format($data['pembelian']['summary']['total_pembelian'], 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Total Subtotal:</span>
                        <span class="summary-value">Rp {{ number_format($data['pembelian']['summary']['total_subtotal'], 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Total Diskon:</span>
                        <span class="summary-value">Rp {{ number_format($data['pembelian']['summary']['total_diskon'], 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Total PPN:</span>
                        <span class="summary-value">Rp {{ number_format($data['pembelian']['summary']['total_ppn'], 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Total Pembayaran:</span>
                        <span class="summary-value">Rp {{ number_format($data['pembelian']['summary']['total_pembayaran'], 0, ',', '.') }}</span>
                    </div>
                </div>

                @if(count($data['pembelian']['detail']) > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th class="text-right">Jumlah Pembelian</th>
                            <th class="text-right">Subtotal</th>
                            <th class="text-right">Diskon</th>
                            <th class="text-right">PPN</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['pembelian']['detail'] as $detail)
                        <tr>
                            <td>{{ $detail['bulan'] }}</td>
                            <td class="text-right">{{ number_format($detail['jumlah_pembelian'], 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($detail['subtotal'], 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($detail['diskon'], 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($detail['ppn'], 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($detail['total'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="no-data">Tidak ada data pembelian pada periode ini</div>
                @endif
            </div>
        </div>
        @endforeach

        <div class="footer">
            <p>Laporan ini dihasilkan secara otomatis oleh sistem pada {{ now()->locale('id')->format('d F Y H:i:s') }}</p>
            <p>Jika Anda memiliki pertanyaan, silakan hubungi administrator sistem.</p>
        </div>
    </div>
</body>
</html>

