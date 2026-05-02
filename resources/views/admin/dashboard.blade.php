<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | SMK PGRI Gumelar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 overflow-x-hidden">

    <!-- NAVBAR -->
    <nav class="w-full bg-white text-black px-8 py-4 shadow flex justify-between items-center">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo_SMK_PGRI_GUMELAR.jpeg') }}" 
                 alt="Logo SMK PGRI Gumelar" 
                 class="w-10 h-10 rounded-full">
            <h1 class="font-semibold text-xl tracking-wide">SMK PGRI GUMELAR</h1>
        </div>

        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button class="bg-red-600 text-white px-5 py-2 rounded-lg hover:bg-red-700 font-medium">
                Keluar
            </button>
        </form>
    </nav>

    <!-- BACKGROUND -->
    <div class="bg-[#0a1b3d] min-h-screen w-full pt-10 pb-20 flex justify-center">
        <div class="bg-[#d5eaff] rounded-2xl shadow-2xl flex gap-6 p-8 w-11/12 max-w-7xl">

            <!-- SIDEBAR -->
            <div class="w-64 bg-white rounded-xl shadow p-6 flex-shrink-0">

                <!-- Foto Profil -->
                <div class="flex flex-col items-center">
                    <div class="w-20 h-20 bg-gray-300 rounded-full flex items-center justify-center text-xl font-bold text-gray-700">
                        AA
                    </div>
                    <p class="mt-3 font-semibold text-gray-700 text-center">Hi Admin</p>
                </div>

                <!-- Menu -->
                <div class="mt-10">
                    <h3 class="text-xs text-gray-500 uppercase mb-2">Menu</h3>
                    <nav class="space-y-2">

                        <a href="{{ route('admin.dashboard') }}"
                           class="block px-4 py-2 rounded-md text-sm font-medium bg-gray-300 text-gray-900">
                            Dashboard
                        </a>

                        <a href="{{ route('admin.dataSiswa') }}"
                           class="block px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-100 text-gray-700">
                            Data Siswa
                        </a>

                        <a href="{{ route('admin.dataPembayaran') }}"
                           class="block px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-100 text-gray-700">
                            Data Pembayaran
                        </a>

                        <a href="{{ route('admin.dataKelas') }}"
                           class="block px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-100 text-gray-700">
                            Data Kelas
                        </a>

                    </nav>
                </div>
            </div>

            <!-- MAIN CONTENT -->
            <div class="flex-1">

                <!-- Judul -->
                <h2 class="text-3xl font-bold mb-6 text-[#0a1b3d]">Dashboard</h2>

                <!-- STATISTIK -->
                <div class="grid grid-cols-4 gap-4">

                    <div class="bg-white p-5 rounded-xl shadow">
                        <p class="text-sm text-gray-500">Siswa Terdaftar</p>
                        <p class="text-2xl font-bold mt-2">{{ $totalSiswa }}</p>
                    </div>

                    <div class="bg-white p-5 rounded-xl shadow">
                        <p class="text-sm text-gray-500">Total Tagihan</p>
                        <p class="text-2xl font-bold mt-2">Rp.{{ number_format($totalTagihan, 0, ',', '.') }}</p>
                    </div>

                    <div class="bg-white p-5 rounded-xl shadow">
                        <p class="text-sm text-gray-500">Lunas</p>
                        <p class="text-2xl font-bold mt-2">{{ $lunas }}</p>
                    </div>

                    <div class="bg-white p-5 rounded-xl shadow">
                        <p class="text-sm text-gray-500">Belum Lunas</p>
                        <p class="text-2xl font-bold mt-2">{{ $belumLunas }}</p>
                    </div>

                </div>

                <!-- TABEL PEMBAYARAN -->
                <div class="bg-white rounded-xl shadow p-6 mt-8">

                    <h3 class="text-lg font-bold mb-4">Pembayaran Terbaru</h3>

                    <!-- SHOW ENTRIES -->
                    <div class="flex items-center justify-end mb-4">
                        <div class="dataTables_length">
                            <label class="right">
                                Show
                                <select id="showEntries" class="border border-gray-300 rounded px-2 py-1 text-sm">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="100">100</option>
                                </select>
                                entries
                            </label>
                        </div>
                    </div>

                    <table id="tabelPembayaran" class="display w-full">

                        <thead>
                            <tr class="bg-blue-900 text-white text-left">
                                <th>No</th>
                                <th>Nama</th>
                                <th>NIS</th>
                                <th>Bulan</th>
                                <th>Tahun Ajaran</th>
                                <th>Tanggal Bayar</th>
                                <th>Jumlah</th>
                                <th class="relative">

                                    <div class="flex items-center gap-2">
                                        Status
                                        <div class="relative">
                                            <button id="statusFilterBtn" 
                                                    class="text-white hover:bg-blue-800 px-2 py-1 rounded">
                                                ▼
                                            </button>
                                            <div id="statusDropdown" 
                                                 class="absolute right-0 mt-1 bg-white text-gray-800 rounded shadow-lg hidden z-10 min-w-max">
                                                <button class="status-filter-opt block w-full text-left px-4 py-2 hover:bg-gray-100 text-sm" data-status="">
                                                    Semua
                                                </button>
                                                <button class="status-filter-opt block w-full text-left px-4 py-2 hover:bg-green-100 text-green-600 font-bold text-sm" data-status="Lunas">
                                                    Lunas
                                                </button>
                                                <button class="status-filter-opt block w-full text-left px-4 py-2 hover:bg-red-100 text-red-600 font-bold text-sm" data-status="Belum Lunas">
                                                    Belum Lunas
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($pembayaranTerbaru as $key => $p)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $p->nama }}</td>
                                <td>{{ $p->nis }}</td>
                                <td>{{ $p->nama_bulan ?? '-' }}</td>
                                <td>{{ $p->tahun_ajaran ?? '-' }}</td>
                                <td>{{ $p->tanggal_bayar ?? '-' }}</td>
                                <td>{{ $p->jumlah ? number_format($p->jumlah, 0, ',', '.') : '-' }}</td>

                                    @php
                                        $status = $p->status ?? 'Belum Lunas';
                                        $warna = $p->status === 'lunas' ? 'text-green-600' : ($p->status === 'pending' ? 'text-yellow-600' : 'text-red-600');
                                    @endphp

                                    <td class="{{ $warna }} font-semibold">{{ ucfirst($status) }}</td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>

                    </table>

                    <!-- DATATABLES CDN -->
                    <link rel="stylesheet" 
                          href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
                    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
                    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

                    <!-- DATATABLES SCRIPT -->
                    <script>
                        let table = $('#tabelPembayaran').DataTable({
                            searching: false,
                            lengthChange: false,
                            paging: true,
                            ordering: false,
                            language: { emptyTable: "Tidak ada data pembayaran" }
                        });

                        $('#showEntries').on('change', function () {
                            table.page.len($(this).val()).draw();
                        });

                        $('#statusFilterBtn').on('click', function (e) {
                            e.stopPropagation();
                            $('#statusDropdown').toggleClass('hidden');
                        });

                        $(document).on('click', function () {
                            $('#statusDropdown').addClass('hidden');
                        });

                        $('.status-filter-opt').on('click', function (e) {
                            e.preventDefault();
                            let status = $(this).data('status');
                            $('#statusDropdown').addClass('hidden');

                            if (status === '') {
                                location.reload();
                                return;
                            }

                            $.ajax({
                                url: "{{ route('admin.pembayaran.filter') }}",
                                method: "GET",
                                data: { status: status },
                                success: function (data) {
                                    table.clear().draw();

                                    if (data.length === 0) {
                                        table.row.add([
                                            '',
                                            'Tidak ada data pembayaran',
                                            '',
                                            '',
                                            '',
                                            '',
                                            '',
                                            ''
                                        ]).draw(false);
                                    } else {
                                        data.forEach((item, index) => {
                                            let warna = item.status === 'lunas'
                                                        ? 'color: green;'
                                                        : (item.status === 'pending' ? 'color: orange;' : 'color: red;');

                                            let jumlahText = '-';
                                            if (item.jumlah) {
                                                if (typeof item.jumlah === 'string' && item.jumlah.includes('Rp')) {
                                                    jumlahText = item.jumlah;
                                                } else {
                                                    jumlahText = parseInt(item.jumlah).toLocaleString('id-ID');
                                                }
                                            }

                                            table.row.add([
                                                index + 1,
                                                item.nama,
                                                item.nis,
                                                item.nama_bulan ?? '-',
                                                item.tahun_ajaran ?? '-',
                                                item.tanggal_bayar ?? '-',
                                                jumlahText,
                                                `<span style="${warna} font-weight: bold;">${item.status ?? 'Belum Lunas'}</span>`
                                            ]).draw(false);
                                        });
                                    }
                                }
                            });
                        });
                    </script>

                </div>
            </div>

        </div>
    </div>

</body>
</html>