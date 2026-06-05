<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Operator - Sini Antri</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#b10303',
                        'primary-dark': '#8b0202',
                        bg: '#fef2f2',
                        card: '#ffffff',
                        text: '#1e293b',
                        muted: '#64748b',
                        border: '#e2e8f0',
                        success: '#059669',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-[#fef2f2] min-h-screen flex items-center justify-center p-4 font-sans">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">

        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-[#b10303] rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <i class="fa-solid fa-ticket-alt text-3xl text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-[#1e293b]">Login Operator</h1>
            <p class="text-sm text-[#64748b] mt-1">Masuk ke dashboard kelola antrian</p>
        </div>

        <!-- Error Alert -->
        @if ($errors->any())
            <div
                class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm mb-5 flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <!-- Email Field -->
            <label class="block text-sm font-semibold text-[#1e293b] mb-1">
                <i class="fa-regular fa-envelope mr-1"></i> Email
            </label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="loket1@antrian.test" required
                autofocus
                class="w-full px-4 py-3 border-2 border-[#e2e8f0] rounded-xl text-[#1e293b] focus:outline-none focus:border-[#b10303] focus:ring-3 focus:ring-[#b10303]/10 mb-4 transition">

            <!-- Password Field -->
            <label class="block text-sm font-semibold text-[#1e293b] mb-1">
                <i class="fa-solid fa-lock mr-1"></i> Password
            </label>
            <input type="password" name="password" placeholder="••••••••" required
                class="w-full px-4 py-3 border-2 border-[#e2e8f0] rounded-xl text-[#1e293b] focus:outline-none focus:border-[#b10303] focus:ring-3 focus:ring-[#b10303]/10 mb-5 transition">

            <!-- Remember Me -->
            <div class="flex items-center gap-2 mb-6">
                <input type="checkbox" name="remember" id="remember"
                    class="w-4 h-4 text-[#b10303] border-2 border-[#e2e8f0] rounded focus:ring-[#b10303] focus:ring-1">
                <label for="remember" class="text-sm text-[#1e293b] cursor-pointer">Ingat saya</label>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full bg-[#b10303] hover:bg-[#8b0202] text-white font-bold py-3 rounded-xl transition active:scale-95 flex items-center justify-center gap-2">
                <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk ke Dashboard
            </button>
        </form>
    </div>
</body>

</html>
