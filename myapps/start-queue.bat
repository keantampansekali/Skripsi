@echo off
echo Starting Laravel Queue Worker...
echo.
echo This window must remain open for queue to work.
echo Close this window to stop queue worker.
echo.

REM Sesuaikan path dengan lokasi aplikasi Anda
cd /d "C:\Users\Keannn\Skripsi\myapps"

REM Jalankan queue worker
php artisan queue:work --sleep=3 --tries=3 --max-time=3600

pause

