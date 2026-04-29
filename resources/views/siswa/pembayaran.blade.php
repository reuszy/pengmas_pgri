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

                <div class="bg-white rounded-xl p-6 shadow text-center">
                    <h2 class="text-2xl font-bold mb-6 border-b-4 border-[#0a1b3d] pb-2">
                        Pembayaran
                    </h2>

                    <div class="grid grid-cols-2 gap-4 text-left max-w-lg mx-auto text-sm">

                        <div>Tahun Ajaran</div>
                        <div>{{ date('Y') }}/{{ date('Y') + 1 }}</div>
                        <div>NIS</div>
                        <div>{{ $siswa->nis }}</div>
                        <div>Nama Siswa</div>
                        <div>{{ $siswa->nama }}</div>
                        <div>Kelas</div>
                        <div>{{ $siswa->kelas->nama_kelas ?? '-'}}</div>
                        <div>Jumlah Tagihan</div>
                        <div>Rp. {{ number_format($tarif->nominal,0,",",".") }},00</div>

                    </div>

                    @if ($pembayaran == "belum")
                        <button id="pay-button" class="mt-8 bg-green-600 text-white px-10 py-2 rounded-lg hover:bg-green-700">
                            BAYAR
                        </button>
                    @else
                        <div class="mt-8 text-green-700 font-semibold">
                            Sudah Membayar
                        </div>
                    @endif


                </div>
                <div class="mt-6 text-center">
                    @if ($pembayaran != "belum")
                        <a href="{{ route('pembayaran.bukti.stream', $siswa->nis) }}" target="_blank"
                           class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                            Lihat Bukti Pembayaran (PDF)
                        </a>
                    @endif
                </div>
            </div>

        </div>

    </div>

</body>

<script>
document.getElementById('pay-button').onclick = function () {

    fetch('{{ route("midtrans.create.qris") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        console.log('Response data:', data); // Debug: lihat response
        console.log('Snap object:', typeof snap, snap); // Debug: cek snap
        if (data.snap_token) {
            snap.pay(data.snap_token);
        } else {
            alert('Error: ' + (data.error || 'Gagal membuat snap token'));
        }
    })
    .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan');
    });
};
</script>


</html>