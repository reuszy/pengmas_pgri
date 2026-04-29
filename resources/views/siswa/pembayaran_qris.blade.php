<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran QRIS | SMK PGRI Gumelar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
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

    <!-- BACKGROUND BIRU -->
    <div class="bg-[#0a1b3d] h-screen flex justify-center items-start pt-10 overflow-y-auto">

        <div class="bg-[#d5eaff] p-6 rounded-2xl shadow-2xl max-w-4xl w-full">

            <!-- CARD PUTIH -->
            <div class="bg-white rounded-xl p-6 shadow">

                <!-- JUDUL DI TENGAH -->
                <h2 class="text-2xl font-bold text-center mb-6 border-b-4 border-[#0a1b3d] pb-2">
                    Pembayaran
                </h2>

                <!-- INFORMASI -->
                <p class="text-center text-gray-700 mb-4">
                    Klik tombol di bawah untuk melakukan pembayaran SPP Bulanan.
                </p>

                <!-- TOMBOL PEMBAYARAN -->
                <div class="flex justify-center my-6">
                    <button id="pay-button" class="bg-green-600 text-white px-10 py-3 rounded-lg hover:bg-green-700 text-lg font-semibold">
                        Bayar Sekarang
                    </button>
                </div>

                <!-- QR CODE -->
                <div id="qris-section" class="hidden flex justify-center my-6">
                    <div id="qrcode" class="shadow-lg rounded-lg"></div>
                </div>

                <!-- NAMA + JUMLAH -->
                <div class="text-center text-gray-700 mb-8">
                    <p class="font-semibold text-lg">{{ $siswa->nama }}</p>
                    <p class="text-sm">Tagihan: <span class="font-semibold">Rp 180.000</span></p>
                </div>

                <!-- TOMBOL KEMBALI -->
                <div class="flex justify-center">
                    <a href="{{ route('siswa.pembayaran') }}"
                        class="bg-blue-700 text-white px-5 py-2 rounded-md hover:bg-blue-800">
                        Kembali ke Pembayaran
                    </a>
                </div>

                <div class="flex justify-center">
                    <a href="{{ route('pembayaran.qris.success') }}"
                        class="bg-green-600 text-white px-5 py-2 rounded-md hover:bg-green-700">
                        Simulasikan Pembayaran Berhasil
                    </a>
                </div>

            </div>

        </div>

    </div>
</body>

<script>
    document.getElementById('pay-button').onclick = function() {
        fetch('{{ route("midtrans.create.qris") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.qr_string) {
                // Generate QR code
                QRCode.toCanvas(document.getElementById('qrcode'), data.qr_string, {
                    width: 256,
                    height: 256
                }, function (error) {
                    if (error) console.error(error);
                });
                document.getElementById('qris-section').classList.remove('hidden');
                document.getElementById('pay-button').style.display = 'none';
            } else {
                alert('Error: ' + (data.error || 'Gagal membuat QRIS'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan');
        });
    };
</script>

</html>