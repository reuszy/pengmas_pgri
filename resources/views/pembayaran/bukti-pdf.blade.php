<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pembayaran SPP</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #333;
            padding: 30px;
            background: #fff;
        }

        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #333;
            padding: 0;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px 30px;
            text-align: center;
            border-bottom: 4px solid #333;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 8px;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .header h2 {
            font-size: 16px;
            margin-bottom: 5px;
            font-weight: normal;
            opacity: 0.95;
        }

        .header p {
            font-size: 11px;
            opacity: 0.9;
            margin-top: 8px;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            background-color: #28a745;
            color: white;
            font-weight: bold;
            border-radius: 25px;
            margin-top: 15px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.pending {
            background-color: #ffc107;
            color: #333;
        }

        .receipt-body {
            padding: 30px;
        }

        .receipt-info {
            margin-bottom: 25px;
            padding: 20px;
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            border-radius: 5px;
        }

        .receipt-info h3 {
            font-size: 14px;
            margin-bottom: 15px;
            color: #667eea;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .info-label {
            display: table-cell;
            width: 180px;
            font-weight: 600;
            color: #555;
            padding: 5px 0;
        }

        .info-separator {
            display: table-cell;
            width: 20px;
            text-align: center;
        }

        .info-value {
            display: table-cell;
            color: #333;
            padding: 5px 0;
        }

        .payment-details {
            margin: 25px 0;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            overflow: hidden;
        }

        .payment-details table {
            width: 100%;
            border-collapse: collapse;
        }

        .payment-details th {
            background-color: #667eea;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: bold;
            font-size: 13px;
        }

        .payment-details td {
            padding: 12px 15px;
            border-bottom: 1px solid #dee2e6;
        }

        .payment-details tr:last-child td {
            border-bottom: none;
        }

        .payment-details tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .total-section {
            margin-top: 25px;
            padding: 20px;
            background-color: #f8f9fa;
            border: 2px dashed #667eea;
            border-radius: 5px;
            text-align: right;
        }

        .total-label {
            font-size: 16px;
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
        }

        .total-amount {
            font-size: 28px;
            font-weight: bold;
            color: #667eea;
        }

        .notes {
            margin-top: 30px;
            padding: 15px;
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            border-radius: 5px;
        }

        .notes h4 {
            font-size: 12px;
            margin-bottom: 8px;
            color: #856404;
            font-weight: bold;
        }

        .notes p {
            font-size: 11px;
            color: #856404;
            line-height: 1.6;
            margin-bottom: 5px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
        }

        .signature-section {
            display: table;
            width: 100%;
            margin-top: 20px;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 15px;
        }

        .signature-box p {
            font-size: 12px;
            margin-bottom: 60px;
            font-weight: 600;
        }

        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            margin: 0 auto;
            padding-top: 5px;
            font-size: 11px;
            color: #666;
        }

        .receipt-footer {
            text-align: center;
            padding: 15px;
            background-color: #f8f9fa;
            border-top: 2px solid #333;
            margin-top: 30px;
        }

        .receipt-footer p {
            font-size: 10px;
            color: #666;
            margin-bottom: 3px;
        }

        .receipt-footer .print-date {
            font-weight: bold;
            color: #333;
            margin-top: 5px;
        }

        .receipt-id {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background-color: #e9ecef;
            border-radius: 5px;
        }

        .receipt-id p {
            font-size: 11px;
            color: #666;
            margin-bottom: 3px;
        }

        .receipt-id .id-number {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(0, 0, 0, 0.05);
            font-weight: bold;
            z-index: -1;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="watermark">{{ $pembayaran->status == 'Lunas' ? 'LUNAS' : 'BELUM LUNAS' }}</div>
    
    <div class="receipt-container">
        <div class="header">
            <h1>SMK PGRI</h1>
            <h2>Bukti Pembayaran SPP</h2>
            <p>Jl. Alamat Sekolah | Telp: (021) 12345678 | Email: smkpgri@example.com</p>
            <span class="status-badge {{ $pembayaran->status != 'Lunas' ? 'pending' : '' }}">
                {{ $pembayaran->status }}
            </span>
        </div>

        <div class="receipt-body">
            <div class="receipt-id">
                <p>No. Bukti Pembayaran</p>
                <div class="id-number">INV-{{ str_pad($pembayaran->id_pembayaran, 6, '0', STR_PAD_LEFT) }}</div>
            </div>

            <div class="receipt-info">
                <h3>Informasi Siswa</h3>
                <div class="info-row">
                    <div class="info-label">NIS</div>
                    <div class="info-separator">:</div>
                    <div class="info-value">{{ $pembayaran->nis }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Nama Siswa</div>
                    <div class="info-separator">:</div>
                    <div class="info-value">{{ $pembayaran->siswa->pengguna->nama_pengguna ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Kelas</div>
                    <div class="info-separator">:</div>
                    <div class="info-value">{{ $pembayaran->kelas->nama_kelas ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email</div>
                    <div class="info-separator">:</div>
                    <div class="info-value">{{ $pembayaran->siswa->email ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">No. Telepon</div>
                    <div class="info-separator">:</div>
                    <div class="info-value">{{ $pembayaran->siswa->nomor_telepon ?? '-' }}</div>
                </div>
            </div>

            <div class="receipt-info">
                <h3>Detail Pembayaran</h3>
                <div class="info-row">
                    <div class="info-label">Tanggal Pembayaran</div>
                    <div class="info-separator">:</div>
                    <div class="info-value">
                        {{ $pembayaran->tanggal_bayar ? \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d F Y, H:i') : '-' }}
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Jenis Pembayaran</div>
                    <div class="info-separator">:</div>
                    <div class="info-value">{{ $pembayaran->jenis_pembayaran ?? 'SPP Bulanan' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Metode Pembayaran</div>
                    <div class="info-separator">:</div>
                    <div class="info-value">{{ $pembayaran->metode_pembayaran ?? 'Tunai' }}</div>
                </div>
            </div>

            <div class="payment-details">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60%">Keterangan</th>
                            <th style="width: 40%; text-align: right;">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $pembayaran->jenis_pembayaran ?? 'Pembayaran SPP' }}</td>
                            <td style="text-align: right; font-weight: bold;">
                                Rp{{ number_format($pembayaran->jumlah, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="total-section">
                <div class="total-label">Total Pembayaran</div>
                <div class="total-amount">Rp{{ number_format($pembayaran->jumlah, 0, ',', '.') }}</div>
            </div>

            <div class="notes">
                <h4>⚠ Catatan Penting:</h4>
                <p>• Bukti pembayaran ini adalah sah dan merupakan tanda terima pembayaran.</p>
                <p>• Harap simpan bukti pembayaran ini sebagai arsip.</p>
                <p>• Jika ada pertanyaan, silakan hubungi bagian keuangan sekolah.</p>
                <p>• Pembayaran yang sudah dilakukan tidak dapat dibatalkan atau dikembalikan.</p>
            </div>

            <div class="footer">
                <div class="signature-section">
                    <div class="signature-box">
                        <p>Petugas Keuangan</p>
                        <div class="signature-line">
                            (...................................)
                        </div>
                    </div>
                    <div class="signature-box">
                        <p>Penerima</p>
                        <div class="signature-line">
                            (...................................)
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="receipt-footer">
            <p>Dokumen ini dicetak secara otomatis oleh sistem</p>
            <p class="print-date">Tanggal Cetak: {{ $tanggal_cetak }}</p>
            <p>&copy; {{ date('Y') }} SMK PGRI - Sistem Pembayaran SPP</p>
        </div>
    </div>
</body>
</html>
