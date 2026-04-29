<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun | SMK PGRI Gumelar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white text-white flex flex-col min-h-screen overflow-hidden">

    <!-- Navbar -->
    <nav class="w-full bg-white text-black px-8 py-4 shadow">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo_SMK_PGRI_GUMELAR.jpeg') }}" alt="Logo SMK PGRI Gumelar"
                class="w-10 h-10 rounded-full">
            <span class="font-semibold text-sm md:text-base">SMK PGRI GUMELAR</span>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="bg-[#0a1b3d] flex-grow flex flex-col items-center justify-center p-3">

        <!-- Daftar -->
        <h1 class="text-2xl font-bold text-white mb-3">Daftar Akun</h1>

        <form action="{{ route('siswa.daftar') }}" method="POST"
            class="bg-white text-black px-20 py-8 rounded-2xl shadow-2xl w-full max-w-2xl">
            @csrf

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-4 w-full">

                <div class="flex items-center gap-20">
                    <label class="text-sm font-medium text-gray-700 w-40">Nama Lengkap</label>
                    <input type="text" name="name" class="rounded-lg bg-blue-100 px-3 py-1 flex-1">
                </div>

                <div class="flex items-center gap-20">
                    <label class="text-sm font-medium text-gray-700 w-40">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="rounded-lg bg-blue-100 px-3 py-1 flex-1">
                </div>

                <div class="flex items-center gap-20">
                    <label class="text-sm font-medium text-gray-700 w-40">Kelas</label>
                    <select name="kelas" class="rounded-lg bg-blue-100 px-3 py-1 flex-1">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>

                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-20">
                    <label class="text-sm font-medium text-gray-700 w-40">NIS</label>
                    <input type="text" name="nis" class="rounded-lg bg-blue-100 px-3 py-1 flex-1">
                </div>

                <div class="flex items-center gap-20">
                    <label class="text-sm font-medium text-gray-700 w-40">E-mail</label>
                    <input type="email" name="email" class="rounded-lg bg-blue-100 px-3 py-1 flex-1">
                </div>

                <div class="flex items-center gap-20">
                    <label class="text-sm font-medium text-gray-700 w-40">Password</label>
                    <div class="relative flex-1">
                        <input type="password" name="password" id="passwordInput"
                            class="rounded-lg bg-blue-100 px-3 py-1 w-full">
                        <span onclick="togglePassword()"
                            class="absolute right-3 top-1 cursor-pointer select-none">👁</span>
                    </div>
                </div>

                <div class="flex items-center gap-20">
                    <label class="text-sm font-medium text-gray-700 w-40">Nomor Telepon</label>
                    <input type="text" name="telepon" class="rounded-lg bg-blue-100 px-3 py-1 flex-1">
                </div>

            </div>

            <div class="relative mt-6 flex items-center">
                <a href="{{ url('/') }}" class="text-red-500 hover:underline text-sm">Kembali</a>
                <button type="submit"
                    class="absolute left-1/2 -translate-x-1/2 bg-blue-700 text-white px-10 py-1 rounded-lg hover:bg-blue-900">Daftar
                </button>

            </div>
        </form>
    </main>

    <!-- Footer -->
    <footer class="bg-white text-black py-2 px-4 text-sm">Kontak Kami</footer>

    <script>
        function togglePassword() {
            let pass = document.getElementById("passwordInput");
            pass.type = pass.type === "password" ? "text" : "password";
        }
    </script>

</body>

</html>