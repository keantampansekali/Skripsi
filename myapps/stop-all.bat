@echo off
echo ========================================
echo Stopping All Services
echo ========================================
echo.

echo [1/3] Stopping Nginx...
cd /d "C:\nginx"
nginx.exe -s stop
timeout /t 1 /nobreak >nul

echo [2/3] Stopping PHP-CGI processes...
taskkill /F /IM php-cgi.exe 2>nul
timeout /t 1 /nobreak >nul

echo [3/3] Stopping PHP processes (queue/reverb)...
taskkill /F /FI "WINDOWTITLE eq Laravel Queue*" 2>nul
taskkill /F /FI "WINDOWTITLE eq Laravel Reverb*" 2>nul
timeout /t 1 /nobreak >nul

echo.
echo ========================================
echo All services stopped!
echo ========================================
echo.
pause

