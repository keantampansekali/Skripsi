<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login</title>
    <script>
        tailwind = window.tailwind || {};
        tailwind.config = { darkMode: 'class' };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Set theme before page renders to prevent flash
        (function() {
            try {
                const stored = localStorage.getItem('theme');
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                const shouldDark = stored ? stored === 'dark' : prefersDark;
                if (shouldDark) document.documentElement.classList.add('dark');
            } catch (e) {}
        })();
    </script>
</head>
<body class="min-h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Masuk</h1>
                <p class="mt-1 text-gray-600 dark:text-gray-300">Gunakan username dan password</p>
            </div>
            
        </div>

        @if(session('message'))
            <div class="mt-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded">
                {{ session('message') }}
            </div>
        @endif

        @if($errors->has('csrf'))
            <div class="mt-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded">
                Session expired. Silakan refresh halaman dan coba lagi.
            </div>
        @endif

        <form class="mt-6 space-y-4" method="POST" action="{{ route('login') }}">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1" for="username">Username</label>
                <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus autocomplete="username" class="w-full border rounded-md px-3 py-2 dark:bg-gray-900 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                @error('username')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" for="password">Password</label>
                <input id="password" name="password" type="password" required autocomplete="current-password" class="w-full border rounded-md px-3 py-2 dark:bg-gray-900 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" for="id_cabang">Cabang</label>
                <select id="id_cabang" name="id_cabang" required class="w-full border rounded-md px-3 py-2 dark:bg-gray-900 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="" disabled {{ old('id_cabang') ? '' : 'selected' }}>Pilih cabang</option>
                    @php
                        $allCabangs = \App\Models\Cabang::all();
                        // Jika ada accessibleCabangs (dari old username), filter cabang
                        $cabangsToShow = isset($accessibleCabangs) && $accessibleCabangs->isNotEmpty() 
                            ? $accessibleCabangs 
                            : $allCabangs;
                    @endphp
                    @foreach($cabangsToShow as $cabang)
                        <option value="{{ $cabang->id_cabang }}" {{ old('id_cabang') == $cabang->id_cabang ? 'selected' : '' }}>{{ $cabang->nama_cabang }}</option>
                    @endforeach
                    @php
                        // Hanya tampilkan pesan jika username valid (ada di database) dan tidak ada error username/password
                        $showNoAccessMessage = isset($accessibleCabangs) 
                            && $accessibleCabangs->isEmpty() 
                            && old('username') 
                            && !$errors->has('username');
                    @endphp
                    @if($showNoAccessMessage)
                        <option value="" disabled>User tidak memiliki akses ke cabang manapun</option>
                    @endif
                </select>
                @error('id_cabang')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-center justify-between">
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="remember" class="rounded" /> Remember me
                </label>
                <a href="/" class="text-sm text-blue-600 hover:underline">Beranda</a>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white rounded-md py-2 hover:bg-blue-700">Masuk</button>
        </form>
    </div>
    
    <script>
        // Ensure input fields are not disabled or reset
        document.addEventListener('DOMContentLoaded', function() {
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            const cabangSelect = document.getElementById('id_cabang');
            
            // Ensure inputs are enabled
            if (usernameInput) {
                usernameInput.disabled = false;
                usernameInput.readOnly = false;
            }
            if (passwordInput) {
                passwordInput.disabled = false;
                passwordInput.readOnly = false;
            }
            if (cabangSelect) {
                cabangSelect.disabled = false;
            }
        });
    </script>
</body>
</html>

