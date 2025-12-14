<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Kasir')</title>
    <!-- Set theme early to prevent FOUC -->
    <script>
        (function() {
            try {
                const stored = localStorage.getItem('theme');
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                const shouldDark = stored ? stored === 'dark' : prefersDark;
                if (shouldDark) document.documentElement.classList.add('dark');
            } catch (e) {}
        })();
    </script>
    <!-- Tailwind CSS via CDN with dark mode class strategy -->
    <script>
        tailwind = window.tailwind || {};
        tailwind.config = { darkMode: 'class' };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('head')
</head>
<body class="bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-gray-100">
    <div class="min-h-screen">
        <main>
            @yield('content')
        </main>
    </div>
    @vite(['resources/js/app.js'])
    <script>
        // Set idCabang for real-time updates
        window.idCabang = {{ session('id_cabang', 0) }};
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('scripts')
    @yield('modals')
</body>
</html>

