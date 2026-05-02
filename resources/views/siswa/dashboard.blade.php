<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa | SMK PGRI Gumelar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 h-screen overflow-hidden">

    <!-- Navbar -->
    <nav class="w-full bg-white text-black px-8 py-4 shadow flex justify-between items-center">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo_SMK_PGRI_GUMELAR.jpeg') }}" alt="Logo SMK PGRI Gumelar"
                class="w-10 h-10 rounded-full">
            <span class="font-semibold text-sm md:text-base">SMK PGRI GUMELAR</span>
        </div>
        <!-- Logout -->
        <form action="{{ route('siswa.logout') }}" method="POST">
            @csrf
            <button class="bg-red-600 text-white px-5 py-1 rounded-md hover:bg-red-700">
                Keluar
            </button>
        </form>

    </nav>

    <!-- Background Section -->
    <div class="bg-[#0a1b3d] h-screen flex justify-center items-start pt-10">

        <div class="bg-[#d5eaff] p-6 rounded-2xl shadow-2xl flex gap-6 max-w-6xl w-full">

            <!-- CARD PROFIL -->
            <div class="w-64 bg-white rounded-xl shadow p-6 flex-shrink-0 relative -ml-9z-10">

                <!-- Foto Profil -->
                <div class="flex flex-col items-center">
                    <div class="w-20 h-20 bg-gray-300 rounded-full flex items-center justify-center text-xl font-bold text-gray-700">
                        {{ strtoupper(substr(explode(' ', $siswa->nama ?? 'Siswa')[0], 0, 1) . (isset(explode(' ', $siswa->nama ?? 'Siswa')[1]) ? substr(explode(' ', $siswa->nama ?? 'Siswa')[1], 0, 1) : '')) }}
                    </div>
                    <p class="mt-3 font-semibold text-gray-700 text-center">
                        Hi, {{ explode(' ', $siswa->nama ?? 'Siswa')[0] }}
                    </p>
                </div>

                <!-- Menu -->
                <div class="mt-10">
                    <h3 class="text-xs text-gray-500 uppercase mb-2">Menu</h3>

                    <nav class="space-y-2">
                        <a href="{{ route('siswa.dashboard') }}"
                            class="block px-4 py-2 rounded-md text-sm font-medium
                            {{ request()->routeIs('siswa.dashboard') ? 'bg-gray-300 font-semibold' : 'hover:bg-gray-200' }}">
                            Dashboard
                        </a>

                        <a href="{{ route('siswa.pembayaran') }}"
                            class="block px-4 py-2 rounded-md text-sm font-medium
                            {{ request()->routeIs('siswa.pembayaran') ? 'bg-gray-300 font-semibold' : 'hover:bg-gray-200' }}">
                            Pembayaran Siswa
                        </a>
                    </nav>
                </div>

            </div>

            <!-- KONTEN UTAMA -->
            <div class="flex-1">

                <!-- Card Informasi Siswa -->
                <div class="bg-white rounded-xl p-3 shadow mb-4">
                    <h2 class="text-xl font-bold mb-4 border-b-4 border-[#0a1b3d] pb-2">
                        Informasi Siswa
                    </h2>

                    <div class="bg-purple-100 rounded-lg overflow-hidden">
                        <table class="w-full text-sm">
                            <tr class="bg-purple-50">
                                <td class="p-3 flex">
                                    <span class="w-28 font-medium">NIS</span>
                                    <span class="mx-2">:</span>
                                    <span class="mx-2">{{ $siswa->nis }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-3 flex">
                                    <span class="w-28 font-medium">Nama Siswa</span>
                                    <span class="mx-2">:</span>
                                    <span class="mx-2">{{ $siswa->nama }}</span>
                                </td>
                            </tr>
                            <tr class="bg-purple-50">
                                <td class="p-3 flex">
                                    <span class="w-28 font-medium">Kelas</span>
                                    <span class="mx-2">:</span>
                                    <span class="mx-2">{{  $siswa->kelas->nama_kelas ?? '-' }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Card Tagihan -->
                <div class="bg-white rounded-xl p-4 shadow overflow-y-auto" style="max-height: 340px;">
                    <h2 class="text-xl font-bold mb-4 border-b-4 border-[#0a1b3d] pb-2">
                        Tagihan SPP Bulanan — TA {{ $tahunAjaran }}
                    </h2>

                    <div class="bg-purple-100 rounded-lg overflow-hidden">
                        <table class="w-full text-sm text-center">
                            <thead class="bg-purple-200 sticky top-0">
                                <tr>
                                    <th class="p-3">No.</th>
                                    <th class="p-3">Bulan</th>
                                    <th class="p-3">Tagihan</th>
                                    <th class="p-3">Status</th>
                                    <th class="p-3">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($tagihan as $index => $data)
                                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-purple-50' }}">
                                        <td class="p-3">{{ $index + 1 }}.</td>
                                        <td class="p-3">{{ $data['nama_bulan'] }}</td>
                                        <td class="p-3">Rp. {{ number_format($data['nominal'], 0, ',', '.') }}</td>
                                        <td class="p-3">
                                            @if ($data['status'] === 'lunas')
                                                <span class="text-green-600 font-semibold">Lunas</span>
                                            @elseif ($data['status'] === 'pending')
                                                <span class="text-yellow-600 font-semibold">Pending</span>
                                            @else
                                                <span class="text-red-600 font-semibold">Belum Lunas</span>
                                            @endif
                                        </td>
                                        <td class="p-3">
                                            @if ($data['status'] === 'belum' || $data['status'] === 'gagal')
                                                <a href="{{ route('siswa.pembayaran', ['bulan' => $data['bulan'], 'tahun_ajaran' => $data['tahun_ajaran']]) }}"
                                                    class="bg-green-600 text-white px-4 py-1 rounded-lg hover:bg-green-700 text-xs">
                                                    Bayar
                                                </a>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white">
                                        <td colspan="5" class="p-3">Tidak ada tagihan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>

        </div>

    </div>

</body>

</html>
