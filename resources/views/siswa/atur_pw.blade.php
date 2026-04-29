<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Ulang Password | SMK PGRI Gumelar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white text-white flex flex-col min-h-screen">

    <!-- Navbar -->
    <nav class="w-full bg-white text-black px-8 py-4 shadow">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo_SMK_PGRI_GUMELAR.jpeg') }}" alt="Logo SMK PGRI Gumelar"
                class="w-10 h-10 rounded-full">
            <span class="font-semibold text-sm md:text-base">SMK PGRI GUMELAR</span>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="bg-[#0a1b3d] flex-grow flex flex-col items-center justify-center p-10">

        <h1 class="text-2xl font-bold text-white mb-20">Atur Ulang Password</h1>

        <!-- FORM -->
        <form action="{{ route('siswa.atur_password.submit') }}" method="POST"
            class="bg-white text-black px-10 py-10 rounded-2xl shadow-2xl w-full max-w-md">
            @csrf

            <!-- Password Baru -->
            <div class="flex items-center gap-10 mb-5 relative">
                <label class="text-sm font-medium text-gray-700 w-40">Password Baru</label>
                <input type="password" id="password" name="password"
                    class="rounded-lg bg-blue-100 px-2 py-1 w-full mt-2">
                
                <span onclick="togglePassword()"
                    class="absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer select-none text-gray-600">👁</span>
            </div>

            <!-- Konfirmasi Password -->
            <div class="flex items-center gap-10 mb-5 relative">
                <label class="text-sm font-medium text-gray-700 w-40">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                    class="rounded-lg bg-blue-100 px-3 py-1 w-full mt-1">

                <span onclick="togglePassword()"
                    class="absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer select-none text-gray-600">👁</span>
            </div>

            <!-- Error Message -->
            <div class="flex flex-col items-center justify-center mt-4 relative w-full">
                @if ($errors->any())
                    <div class="text-red-500 text-sm text-center mb-3">
                        {{ $errors->first() }}
                    </div>
                @endif
            </div>

            <!-- Button Simpan -->
            <div class="flex flex-col items-center justify-center mt-4 relative w-full">
                <button type="submit" class="bg-blue-700 text-white px-10 py-2 rounded-lg hover:bg-blue-900">
                    Simpan
                </button>

                <a href="{{ route('beranda') }}"
                    class="text-red-500 text-sm hover:underline absolute left-0 -bottom-6">
                    Kembali
                </a>
            </div>

        </form>

        <!-- Script Show/Hide Password -->
        <script>
            function togglePassword(fieldId, iconId) {
                const field = document.getElementById(fieldId);
                const icon = document.getElementById(iconId);

                if (field.type === "password") {
                    field.type = "text";
                    icon.textContent = "🙈";  
                } else {
                    field.type = "password";
                    icon.textContent = "👁";  
                }
            }
        </script>

    </main>

    <!-- Footer -->
    <footer class="bg-white text-black py-2 px-4 text-sm">
        Kontak Kami
    </footer>

</body>

</html>
