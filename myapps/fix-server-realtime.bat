@echo off
echo ========================================
echo Fix Real-Time on Production Server
echo ========================================
echo.

set /p server_user="Enter SSH username for 72.60.78.65: "
echo.

echo Connecting to server and fixing real-time...
echo.

REM Upload fix script and run it
ssh %server_user%@72.60.78.65 "cd /var/www/html/skripsi && cat > fix-realtime-production.sh" < fix-realtime-production.sh

ssh %server_user%@72.60.78.65 "cd /var/www/html/skripsi && chmod +x fix-realtime-production.sh && ./fix-realtime-production.sh"

if errorlevel 1 (
    echo.
    echo ❌ Fix failed! Manual steps:
    echo.
    echo 1. SSH to server:
    echo    ssh %server_user%@72.60.78.65
    echo.
    echo 2. Edit .env:
    echo    cd /var/www/html/skripsi
    echo    nano .env
    echo    Change REVERB_HOST=localhost to REVERB_HOST=72.60.78.65
    echo    Change VITE_REVERB_HOST="localhost" to VITE_REVERB_HOST="72.60.78.65"
    echo.
    echo 3. Rebuild:
    echo    npm run build
    echo    sudo systemctl restart reverb
    echo.
) else (
    echo.
    echo ========================================
    echo ✅ Fix Complete!
    echo ========================================
    echo.
    echo Test the application:
    echo 1. Visit: http://72.60.78.65
    echo 2. Press F12 to open console
    echo 3. Look for: "Connected to Reverb"
    echo 4. Check host shows: 72.60.78.65 (not localhost)
    echo.
    echo Test real-time:
    echo - Open 2 browser windows
    echo - Edit product in window 1
    echo - Watch it update in window 2
    echo.
)

pause

