<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pembayaran | Admin</title>
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
            <button class="bg-red-600 text-white px-5 py-2 rounded-md hover:bg-red-700">
                Keluar
            </button>
        </form>
    </nav>

    <div class="bg-[#0a1b3d] min-h-screen w-full pt-10 pb-20 flex justify-center">

        <div class="bg-[#d5eaff] rounded-2xl shadow-2xl flex gap-6 p-8 w-11/12 max-w-7xl">

            <!-- SIDEBAR -->
            <div class="w-64 bg-white rounded-xl shadow p-6 flex-shrink-0">

                <div class="flex flex-col items-center">
                    <div class="w-20 h-20 bg-gray-300 rounded-full flex items-center justify-center text-xl font-bold text-gray-700">
                        AA
                    </div>

                    <p class="mt-3 font-semibold text-gray-700 text-center">Hi Admin</p>
                </div>

                <div class="mt-10">
                    <h3 class="text-xs text-gray-500 uppercase mb-2">Menu</h3>

                    <nav class="space-y-2">
                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-200">
                            Dashboard
                        </a>

                        <a href="{{ route('admin.dataSiswa') }}" class="block px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-200">
                            Data Siswa
                        </a>

                        <a href="{{ route('admin.dataPembayaran') }}" class="block px-4 py-2 rounded-md text-sm font-medium bg-gray-300 font-semibold">
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

                <h2 class="text-3xl font-bold mb-6 text-[#0a1b3d]">Data Pembayaran</h2>

                <div class="flex justify-between items-end mb-4 gap-4">
                    <div class="flex flex-col gap-2">
                        <div>
                            Show
                            <select id="showEntries" class="px-2 py-1 border rounded text-sm">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="100">100</option>
                            </select>
                            entries
                        </div>

                        <input id="customSearch" type="search" placeholder="Cari nama, NIS, kelas..." class="border px-3 py-1 rounded text-sm shadow-sm" />
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('admin.pembayaran.export.xlsx') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-2">
                            📊 XLSX
                        </a>
                        <a href="{{ route('admin.pembayaran.export.pdf') }}" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-2">
                            📄 PDF
                        </a>
                    </div>
                </div>

                <!-- DataTables CSS -->
                <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">

                <table id="tabelPembayaran" class="display w-full text-sm">
                    <thead>
                        <tr class="bg-blue-900 text-white text-left">
                            <th class="px-3 py-2">No.</th>
                            <th class="px-3 py-2">Nama</th>
                            <th class="px-3 py-2">NIS</th>
                            <th class="px-3 py-2">Kelas</th>
                            <th class="px-3 py-2">Bulan</th>
                            <th class="px-3 py-2">Tahun Ajaran</th>
                            <th class="px-3 py-2">Tanggal Bayar</th>
                            <th class="px-3 py-2">Jumlah</th>
                            <th class="px-3 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse ($pembayaran as $key => $p)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-3 py-2">{{ $key + 1 }}</td>
                            <td class="px-3 py-2">{{ $p->nama ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $p->nis ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $p->nama_kelas ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $p->nama_bulan ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $p->tahun_ajaran ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $p->tanggal_bayar ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $p->jumlah ?? '-' }}</td>
                            <td class="px-3 py-2">
                                <span class="px-2 py-1 rounded text-xs font-semibold {{ $p->status === 'lunas' ? 'bg-green-200 text-green-800' : ($p->status === 'pending' ? 'bg-yellow-200 text-yellow-800' : 'bg-red-200 text-red-800') }}">
                                    {{ ucfirst($p->status ?? 'Belum Lunas') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-gray-500">Tidak ada data pembayaran</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- DataTables JS -->
                <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
                <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

                <script>
                    let table = $('#tabelPembayaran').DataTable({
                        searching: true,
                        lengthChange: false,
                        paging: true,
                        ordering: false,
                        dom: 'rtip'
                    });

                    $('#tabelPembayaran_filter').remove();

                    $('#customSearch').on('input', function() {
                        table.search(this.value).draw();
                    });

                    $('#showEntries').on('change', function() {
                        let val = $(this).val();
                        table.page.len(val).draw();
                    });
                </script>

            </div>
        </div>
    </div>

</body>
</html>
