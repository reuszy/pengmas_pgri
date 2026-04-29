<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | SMK PGRI Gumelar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white text-white flex flex-col min-h-screen">

    <!-- Navbar -->
    <nav class="w-full bg-white text-black px-8 py-4 shadow">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo_SMK_PGRI_GUMELAR.jpeg') }}" 
                 alt="Logo SMK PGRI Gumelar"
                 class="w-10 h-10 rounded-full">
            <span class="font-semibold text-sm md:text-base">SMK PGRI GUMELAR</span>
        </div>
    </nav>

    <!-- Main -->
    <main class="bg-[#0a1b3d] flex-grow flex flex-col items-center justify-center p-15">

        <h1 class="text-2xl font-bold text-white mb-20">Lupa Password</h1>

        <form action="{{ route('siswa.lupaPassword.submit') }}" 
              method="POST"
              class="bg-white text-black px-10 py-10 rounded-2xl shadow-2xl w-full max-w-md">

            @csrf

            <!-- NIS -->
            <div class="flex items-center gap-10 mb-5">
                <label class="text-sm font-medium text-gray-700 w-40">NIS</label>
                <input type="text" name="nis"
                       class="rounded-lg bg-blue-100 px-2 py-1 w-full mt-2
                              focus:outline-none focus:ring-2 focus:ring-blue-600"
                       value="{{ old('nis') }}"
                       required>

                @error('nis')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tanggal Lahir -->
            <div class="flex items-center gap-10 mb-5">
                <label class="text-sm font-medium text-gray-700 w-40">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir"
                       class="rounded-lg bg-blue-100 px-3 py-1 w-full mt-2
                              focus:outline-none focus:ring-2 focus:ring-blue-600"
                       value="{{ old('tanggal_lahir') }}"
                       required>

                @error('tanggal_lahir')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Error umum -->
            @if ($errors->any())
    <div class="text-red-500 text-sm mb-4 text-center">
        {{ $errors->first() }}
    </div>
@endif
            <!-- Tombol Selanjutnya-->
            <div class="relative flex justify-center mt-8">
    <button type="submit"
        class="bg-blue-700 text-white px-10 py-2 rounded-lg hover:bg-blue-900">
        Selanjutnya
    </button>

    <!-- Link Kembali di kiri bawah -->
    <a href="{{ route('siswa.login') }}"
        class="text-red-500 text-sm hover:underline absolute left-0 -bottom-6">
        Kembali
    </a>

</div>

        </form>

    </main>

    <footer class="bg-white text-black py-2 px-4 text-sm">
        Kontak Kami
    </footer>

</body>

</html>