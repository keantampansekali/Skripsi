@echo off
echo ========================================
echo Starting All Services for Real-Time Updates
echo ========================================
echo.
echo This will open 3 terminal windows:
echo   1. Laravel Application (php artisan serve)
echo   2. Reverb WebSocket Server (php artisan reverb:start)
echo   3. Queue Worker (php artisan queue:work)
echo.
echo Press Ctrl+C in any window to stop that service
echo ========================================
echo.

REM Start Laravel Application
start "Laravel App" cmd /k "cd /d %~dp0 && php artisan serve"

REM Wait 2 seconds
timeout /t 2 /nobreak >nul

REM Start Reverb Server
start "Reverb WebSocket" cmd /k "cd /d %~dp0 && php artisan reverb:start"

REM Wait 2 seconds
timeout /t 2 /nobreak >nul

REM Start Queue Worker
start "Queue Worker" cmd /k "cd /d %~dp0 && php artisan queue:work"

echo.
echo ========================================
echo All services started!
echo ========================================
echo.
echo Open your browser and navigate to:
echo   http://127.0.0.1:8000/master/produk
echo.
echo Check the browser console (F12) for WebSocket connection messages.
echo.
echo To test real-time updates:
echo   1. Open the product list in 2 different browser WINDOWS
echo   2. Edit a product in Window 1
echo   3. Watch Window 2 update automatically!
echo.
echo Press any key to close this window...
pause >nul

