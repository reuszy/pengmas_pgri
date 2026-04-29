<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil | SMK PGRI Gumelar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen overflow-hidden">

    <!-- NAVBAR -->
    <nav class="w-full bg-white text-black px-8 py-4 shadow flex justify-between items-center">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo_SMK_PGRI_GUMELAR.jpeg') }}"
                 class="w-10 h-10 rounded-full">
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
    <div class="bg-[#0a1b3d] min-h-screen flex justify-center items-start pt-10 overflow-y-auto">

        <div class="bg-[#d5eaff] p-6 rounded-2xl shadow-2xl max-w-4xl w-full">

            <!-- CARD PUTIH -->
            <div class="bg-white rounded-xl p-6 shadow">

                <!-- JUDUL -->
                <h2 class="text-2xl font-bold text-center mb-6 border-b-4 border-[#0a1b3d] pb-2">
                    Pembayaran
                </h2>

                <!-- NOTIFIKASI (PERSIS SEPERTI GAMBAR KAMU) -->
                <div class="bg-blue-100 p-6 rounded-xl shadow border border-blue-300 mb-8">
                    <p class="text-xl font-semibold text-gray-700 text-center">
                        Pembayaran telah berhasil!
                    </p>
                </div>

                <!-- BUTTON LIHAT BUKTI -->
                <div class="flex justify-center">
                    <a href="{{ route('pembayaran.qris.bukti', $pembayaran->id_pembayaran) }}"
                       class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 shadow font-medium">
                        Lihat Bukti Pembayaran
                    </a>
                </div>

            </div>

        </div>

    </div>
</body>
</html>
