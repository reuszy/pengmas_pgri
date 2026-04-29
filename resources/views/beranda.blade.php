<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Siswa | SMK PGRI Gumelar</title>
    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-white flex flex-col min-h-screen overflow-hidden">

    <!-- Navbar -->
    <nav class="w-full bg-white text-black flex justify-between items-center px-8 py-4 shadow-md relative">
        <!-- Kiri: Logo + Nama Sekolah -->
        <div class="flex items-center space-x-3">
            <img src="{{ asset('images/logo_SMK_PGRI_GUMELAR.jpeg') }}" alt="Logo SMK PGRI Gumelar"
                class="w-10 h-10 rounded-full">
            <span class="font-semibold text-sm md:text-base">SMK PGRI GUMELAR</span>
        </div>

        <!-- Kanan: Tombol -->
        <div class="flex space-x-4 items-center">

            <!-- Tombol Daftar -->
            <a href="{{ route('siswa.daftar.form') }}"
                class="bg-[#0a1b3d] text-white px-5 py-1.5 rounded-full text-sm hover:bg-blue-900 transition">
                Daftar
            </a>

            <!-- Dropdown Masuk -->
            <div class="relative">
                <!-- Tombol Masuk -->
                <button onclick="toggleDropdown()"
                    class="bg-[#0a1b3d] text-white px-5 py-1.5 rounded-full text-sm hover:bg-blue-900 transition">
                    Masuk
                </button>

                <!-- Menu Dropdown -->
                <div id="dropdownMenu"
                    class="hidden absolute right-0 mt-2 w-32 bg-white border rounded-md shadow-lg text-black">

                    <!-- Admin → langsung ke login admin -->
                    <a href="{{ url('/admin/masuk') }}" class="block px-4 py-2 rounded-full text-sm hover:bg-gray-100">
                        Admin
                    </a>

                    <!-- Siswa → ke login siswa -->
                    <a href="{{  route('siswa.login.submit') }}"
                        class="block px-4 py-2 rounded-full text-sm hover:bg-gray-100">
                        Siswa
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <script>
        function toggleDropdown() {
            document.getElementById('dropdownMenu').classList.toggle('hidden');
        }

        // Klik di luar dropdown untuk menutup
        document.addEventListener('click', function (event) {
            const dropdown = document.getElementById('dropdownMenu');
            const button = event.target.closest('button');

            if (!event.target.closest('#dropdownMenu') && !button) {
                dropdown.classList.add('hidden');
            }
        });
    </script>

    <!-- Bagian Tengah (Hero Section) -->
    <section class="flex flex-col justify-center items-center text-center bg-[#0a1b3d] flex-grow px-4">
        <h1 class="text-2xl md:text-3xl font-bold mb-3">
            Sistem Pembayaran SPP<br>SMK PGRI Gumelar
        </h1>
        <p class="text-xs md:text-sm max-w-md">
            Membantu siswa dan sekolah dalam menciptakan proses pembayaran SPP yang teratur,
            efisien, dan terpercaya di SMK PGRI Gumelar
        </p>
    </section>

    <!-- Footer -->
    <footer class="bg-white text-black text-left py-2 px-4 text-sm">
        Kontak Kami
    </footer>

</body>

</html>