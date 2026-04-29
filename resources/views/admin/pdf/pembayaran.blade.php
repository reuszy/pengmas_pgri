<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Pembayaran SPP</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #333;
        }

        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .header h2 {
            font-size: 14px;
            margin-bottom: 3px;
            font-weight: normal;
        }

        .header p {
            font-size: 11px;
            color: #666;
        }

        .info {
            margin-bottom: 20px;
        }

        .info p {
            font-size: 11px;
            margin-bottom: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead {
            background-color: #f0f0f0;
        }

        table th {
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #333;
            font-size: 11px;
        }

        table td {
            padding: 8px;
            border: 1px solid #666;
            font-size: 11px;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .status-lunas {
            color: #28a745;
            font-weight: bold;
        }

        .status-belum {
            color: #dc3545;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #666;
            text-align: center;
        }

        .summary {
            margin-top: 20px;
            padding: 10px;
            background-color: #f0f0f0;
            border: 1px solid #333;
        }

        .summary p {
            font-size: 11px;
            margin-bottom: 5px;
        }

        .summary strong {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SMK PGRI</h1>
        <h2>Laporan Data Pembayaran SPP</h2>
        <p>Jl. Alamat Sekolah | Telp: (021) 12345678 | Email: smkpgri@example.com</p>
    </div>

    <div class="info">
        <p><strong>Tanggal Cetak:</strong> {{ $tanggal }}</p>
        <p><strong>Total Data:</strong> {{ count($pembayaran) }} siswa</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">No</th>
                <th style="width: 12%;">NIS</th>
                <th style="width: 25%;">Nama Siswa</th>
                <th class="text-center" style="width: 12%;">Kelas</th>
                <th class="text-center" style="width: 15%;">Tanggal Bayar</th>
                <th class="text-right" style="width: 16%;">Jumlah</th>
                <th class="text-center" style="width: 15%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalLunas = 0;
                $totalBelumLunas = 0;
                $totalJumlah = 0;
            @endphp

            @foreach($pembayaran as $index => $p)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $p->nis }}</td>
                    <td>{{ $p->nama }}</td>
                    <td class="text-center">{{ $p->nama_kelas ?? '-' }}</td>
                    <td class="text-center">
                        {{ $p->tanggal_bayar != '-' ? \Carbon\Carbon::parse($p->tanggal_bayar)->format('d-m-Y') : '-' }}
                    </td>
                    <td class="text-right">
                        @if(is_numeric($p->jumlah))
                            Rp{{ number_format($p->jumlah, 0, ',', '.') }}
                        @else
                            {{ $p->jumlah }}
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="{{ $p->status == 'Lunas' ? 'status-lunas' : 'status-belum' }}">
                            {{ $p->status }}
                        </span>
                    </td>
                </tr>

                @php
                    if ($p->status == 'Lunas') {
                        $totalLunas++;
                    } else {
                        $totalBelumLunas++;
                    }

                    if (is_numeric($p->jumlah)) {
                        if ($p->status != 'Lunas') {
                            $totalJumlah += $p->jumlah;
                        }
                    }
                @endphp
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <p><strong>Ringkasan Pembayaran:</strong></p>
        <p>Total Siswa Lunas: <strong>{{ $totalLunas }}</strong></p>
        <p>Total Siswa Belum Lunas: <strong>{{ $totalBelumLunas }}</strong></p>
        <p>Total Tunggakan: <strong>Rp{{ number_format($totalJumlah, 0, ',', '.') }}</strong></p>
    </div>

    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis oleh sistem pada {{ $tanggal }}</p>
        <p>&copy; {{ date('Y') }} SMK PGRI - Sistem Pembayaran SPP</p>
    </div>
</body>
</html>
