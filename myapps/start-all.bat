@echo off
echo ========================================
echo Starting All Services for Laravel App
echo ========================================
echo.

REM Sesuaikan path dengan lokasi aplikasi Anda
set APP_PATH=C:\Users\Keannn\Skripsi\myapps
set PHP_PATH=C:\php\php-cgi.exe

echo [1/4] Starting PHP-CGI...
start "PHP-CGI" cmd /k "%PHP_PATH% -b 127.0.0.1:9000"
timeout /t 2 /nobreak >nul

echo [2/4] Starting Nginx...
cd /d "C:\nginx"
start "Nginx" nginx.exe
timeout /t 2 /nobreak >nul

echo [3/4] Starting Queue Worker...
cd /d "%APP_PATH%"
start "Laravel Queue" cmd /k "php artisan queue:work --sleep=3 --tries=3 --max-time=3600"
timeout /t 2 /nobreak >nul

echo [4/4] Starting Reverb WebSocket...
cd /d "%APP_PATH%"
start "Laravel Reverb" cmd /k "php artisan reverb:start --host=127.0.0.1 --port=8080"
timeout /t 2 /nobreak >nul

echo.
echo ========================================
echo All services started!
echo ========================================
echo.
echo Services running:
echo - PHP-CGI (port 9000)
echo - Nginx (port 80)
echo - Queue Worker
echo - Reverb WebSocket (port 8080)
echo.
echo Access your application at: http://localhost
echo.
echo To stop services, close the respective windows or run stop-all.bat
echo.
pause

