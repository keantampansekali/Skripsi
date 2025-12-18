@echo off
echo ========================================
echo Starting Laravel Reverb Server
echo ========================================
echo.
echo Make sure you have:
echo 1. Configured .env file correctly
echo 2. Run: php artisan config:clear
echo 3. Run: npm run build (or npm run dev)
echo.
echo This window must stay open for Reverb to work!
echo Press Ctrl+C to stop the server.
echo.
echo ========================================
echo.

cd /d "%~dp0"
php artisan reverb:start

pause

