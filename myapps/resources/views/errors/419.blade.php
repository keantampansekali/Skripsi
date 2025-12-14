<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Page Expired</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 flex items-center justify-center p-4">
    <div class="text-center">
        <h1 class="text-6xl font-bold text-gray-800 dark:text-gray-200 mb-4">419</h1>
        <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-300 mb-4">Page Expired</h2>
        <p class="text-gray-600 dark:text-gray-400 mb-6">Session Anda telah berakhir. Silakan refresh halaman dan coba lagi.</p>
        <a href="{{ route('login') }}" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
            Kembali ke Login
        </a>
    </div>
    <script>
        // Auto redirect setelah 3 detik
        setTimeout(function() {
            window.location.href = '{{ route("login") }}';
        }, 3000);
    </script>
</body>
</html>

