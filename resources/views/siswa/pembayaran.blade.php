<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Siswa | SMK PGRI Gumelar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.clientKey') }}"></script>
    

</head>

<body class="bg-gray-100 h-screen overflow-hidden">

    <!-- NAVBAR -->
    <nav class="w-full bg-white text-black px-8 py-4 shadow flex justify-between items-center">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo_SMK_PGRI_GUMELAR.jpeg') }}" class="w-10 h-10 rounded-full">
            <span class="font-semibold text-sm md:text-base">SMK PGRI GUMELAR</span>
        </div>

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
                    <div
                        class="w-20 h-20 bg-gray-300 rounded-full flex items-center justify-center text-xl font-bold text-gray-700">
                        {{ substr($siswa->nama, 0, 1) ?? '' }}{{ substr($siswa->nama, 1, 1) ?? '' }}

                    </div>

                    <p class="mt-3 font-semibold text-gray-700 text-center">
                        Hi, {{ $siswa->nama }} !

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

                        <a href="{{ route('siswa.pembayaran.qris') }}"
                            class="block px-4 py-2 rounded-md text-sm font-medium
                            {{ request()->routeIs('siswa.pembayaran') ? 'bg-gray-300 font-semibold' : 'hover:bg-gray-200' }}">
                            Pembayaran Siswa
                        </a>
                    </nav>
                </div>

            </div>

            <!-- KONTEN UTAMA -->
            <div class="flex-1">

                <div class="bg-white rounded-xl p-6 shadow">
                    <h2 class="text-2xl font-bold mb-6 border-b-4 border-[#0a1b3d] pb-2 text-center">
                        Pembayaran SPP — TA {{ $tahunAjaran }}
                    </h2>

                    <div class="grid grid-cols-2 gap-4 text-left max-w-lg mx-auto text-sm mb-6">
                        <div>NIS</div>
                        <div>{{ $siswa->nis }}</div>
                        <div>Nama Siswa</div>
                        <div>{{ $siswa->nama }}</div>
                        <div>Kelas</div>
                        <div>{{ $siswa->kelas->nama_kelas ?? '-'}}</div>
                        <div>Tagihan / Bulan</div>
                        <div>Rp. {{ number_format($tarif->nominal, 0, ',', '.') }}</div>
                    </div>

                    <!-- Tabel Tagihan Bulanan -->
                    <div class="overflow-y-auto" style="max-height: 300px;">
                        <table class="w-full text-sm text-center">
                            <thead class="bg-purple-200 sticky top-0">
                                <tr>
                                    <th class="p-2">No</th>
                                    <th class="p-2">Bulan</th>
                                    <th class="p-2">Tagihan</th>
                                    <th class="p-2">Status</th>
                                    <th class="p-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tagihan as $index => $data)
                                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-purple-50' }}">
                                        <td class="p-2">{{ $index + 1 }}</td>
                                        <td class="p-2">{{ $data['nama_bulan'] }}</td>
                                        <td class="p-2">Rp. {{ number_format($data['nominal'], 0, ',', '.') }}</td>
                                        <td class="p-2">
                                            @if ($data['status'] === 'lunas')
                                                <span class="text-green-600 font-semibold">Lunas</span>
                                            @elseif ($data['status'] === 'pending')
                                                <span class="text-yellow-600 font-semibold">Pending</span>
                                            @else
                                                <span class="text-red-600 font-semibold">Belum Lunas</span>
                                            @endif
                                        </td>
                                        <td class="p-2">
                                            @if ($data['status'] === 'belum' || $data['status'] === 'gagal' || $data['status'] === 'pending')
                                                <button onclick="bayar({{ $data['bulan'] }}, '{{ $data['tahun_ajaran'] }}')"
                                                    class="{{ $data['status'] === 'pending' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-600 hover:bg-green-700' }} text-white px-3 py-1 rounded-lg text-xs">
                                                    {{ $data['status'] === 'pending' ? 'Bayar Ulang' : 'Bayar' }}
                                                </button>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>

                <div class="mt-6 text-center">
                    <a href="{{ route('siswa.dashboard') }}"
                       class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>

        </div>

    </div>

</body>

<script>
function bayar(bulan, tahunAjaran) {
    fetch('{{ route("midtrans.create.qris") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            bulan: bulan,
            tahun_ajaran: tahunAjaran
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.token) {
            snap.pay(data.token, {
                onSuccess: function(result) {
                    window.location.href = '{{ route("siswa.pembayaran") }}';
                },
                onPending: function(result) {
                    alert('Pembayaran sedang diproses.');
                    window.location.href = '{{ route("siswa.pembayaran") }}';
                },
                onError: function(result) {
                    alert('Pembayaran gagal.');
                    window.location.href = '{{ route("siswa.pembayaran") }}';
                },
                onClose: function() {
                    console.log('Popup ditutup');
                }
            });
        } else {
            alert('Error: ' + (data.error || 'Gagal membuat snap token'));
        }
    })
    .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan');
    });
}
</script>


</html>