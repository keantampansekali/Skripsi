@echo off
echo Starting Laravel Reverb WebSocket Server...
echo.
echo This window must remain open for WebSocket to work.
echo Close this window to stop Reverb server.
echo.

REM Sesuaikan path dengan lokasi aplikasi Anda
cd /d "C:\Users\Keannn\Skripsi\myapps"

REM Jalankan Reverb
php artisan reverb:start --host=127.0.0.1 --port=8080

pause

