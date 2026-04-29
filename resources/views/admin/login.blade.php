<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Akun | SMK PGRI Gumelar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white text-white flex flex-col min-h-screen">

    <!-- Navbar -->
    <nav class="w-full bg-white text-black px-8 py-4 shadow">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo_SMK_PGRI_GUMELAR.jpeg') }}" alt="Logo SMK PGRI Gumelar" class="w-10 h-10 rounded-full">
            <span class="font-semibold text-sm md:text-base">SMK PGRI GUMELAR</span>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="bg-[#0a1b3d] flex-grow flex flex-col items-center justify-center p-10">

        <h1 class="text-2xl font-bold text-white mb-20">Login Admin</h1>

        <form action="{{ route('admin.login.submit') }}" method="POST"
              class="bg-white text-black px-10 py-10 rounded-2xl shadow-2xl w-full max-w-md">
            @csrf

            <div class="flex items-center gap-10 mb-5">
                <label class="text-sm font-medium text-gray-700 w-40">Username</label>
                <input type="text" id="username" name="username"
                       class="rounded-lg bg-blue-100 px-2 py-1 w-full mt-2">
            </div>

            <div class="flex items-center gap-10 mb-5 relative">
                <label class="text-sm font-medium text-gray-700 w-40">Password</label>
                <input type="password" id="password" name="password"
                    class="rounded-lg bg-blue-100 px-3 py-1 w-full mt-1">

                <span id="togglePassword" onclick="togglePassword()"
                    class="absolute right-3 top-2 cursor-pointer select-none text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M10 3C5 3 1.73 7.11 1 10c.73 2.89 4 7 9 7s8.27-4.11 9-7c-.73-2.89-4-7-9-7zM10 15a5 5 0 110-10 5 5 0 010 10z" />
                        <path d="M10 7a3 3 0 100 6 3 3 0 000-6z" />
                    </svg>
                </span>
            </div>

            <div class="flex flex-col items-center justify-center mt-8 relative w-full">

                {{-- Error dari hasil login --}}
                @if (session()->has('error'))
                    <div class="text-red-500 text-sm text-center mb-3">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Error jika field kosong --}}
                @if ($errors->has('username') || $errors->has('password'))
                    <div class="text-red-500 text-sm text-center mb-3">
                        Silakan isi Username dan Password!
                    </div>
                @endif

                <!-- Tombol Masuk -->
                <button type="submit"
                        class="bg-blue-700 text-white px-10 py-2 rounded-lg hover:bg-blue-900">
                    Masuk
                </button>

                <a href="{{ url('/') }}" class="text-red-500 hover:underline text-sm absolute left-0 bottom-0 -mb-6">
                    Kembali
                </a>
            </div>
        </form>
        <script>
    function togglePassword() {
        const passField = document.getElementById('password');
        const icon = document.getElementById('togglePassword');

                if (passField.type === "password") {
                    passField.type = "text";
                    icon.innerHTML =
                        "<svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' viewBox='0 0 20 20' fill='currentColor'><path d='M4.03 3.97a.75.75 0 10-1.06 1.06l1.664 1.664A9.953 9.953 0 001 10c.73 2.89 4 7 9 7 1.957 0 3.737-.632 5.205-1.69l2.005 2.005a.75.75 0 101.06-1.06l-14-14zM10 15a5 5 0 01-5-5c0-.847.21-1.645.577-2.33l6.753 6.753A4.978 4.978 0 0110 15zm3.423-2.67l-1.61-1.61a3 3 0 00-3.536-3.536l-1.61-1.61A7.963 7.963 0 0110 5c4.27 0 7.493 3.11 8.423 5-.464.935-1.158 1.752-2.084 2.33z'/></svg>";
                } else {
                    passField.type = "password";
                    icon.innerHTML =
                        "<svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' viewBox='0 0 20 20' fill='currentColor'><path d='M10 3C5 3 1.73 7.11 1 10c.73 2.89 4 7 9 7s8.27-4.11 9-7c-.73-2.89-4-7-9-7zM10 15a5 5 0 110-10 5 5 0 010 10z' /><path d='M10 7a3 3 0 100 6 3 3 0 000-6z' /></svg>";
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
