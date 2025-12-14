@echo off
echo ========================================
echo Status Services
echo ========================================
echo.

echo [1] Checking Nginx...
tasklist /FI "IMAGENAME eq nginx.exe" 2>NUL | find /I /N "nginx.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] Nginx is running
) else (
    echo [NOT RUNNING] Nginx is not running
)
echo.

echo [2] Checking PHP-CGI...
tasklist /FI "IMAGENAME eq php-cgi.exe" 2>NUL | find /I /N "php-cgi.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] PHP-CGI is running
) else (
    echo [NOT RUNNING] PHP-CGI is not running
)
echo.

echo [3] Checking Port 80...
netstat -an | findstr ":80 " >nul
if %errorlevel% equ 0 (
    echo [OK] Port 80 is in use
    netstat -an | findstr ":80 "
) else (
    echo [NOT IN USE] Port 80 is not in use
)
echo.

echo [4] Checking Port 9000...
netstat -an | findstr ":9000 " >nul
if %errorlevel% equ 0 (
    echo [OK] Port 9000 is in use (PHP-CGI)
    netstat -an | findstr ":9000 "
) else (
    echo [NOT IN USE] Port 9000 is not in use
)
echo.

echo [5] Checking Port 8080 (Reverb)...
netstat -an | findstr ":8080 " >nul
if %errorlevel% equ 0 (
    echo [OK] Port 8080 is in use (Reverb)
    netstat -an | findstr ":8080 "
) else (
    echo [NOT IN USE] Port 8080 is not in use
)
echo.

echo ========================================
echo Test Connection
echo ========================================
echo.

echo Testing http://localhost...
curl -s -o nul -w "HTTP Status: %%{http_code}\n" http://localhost
if %errorlevel% equ 0 (
    echo [OK] Application is accessible
) else (
    echo [ERROR] Cannot access application
    echo.
    echo Possible issues:
    echo - Nginx is not running
    echo - PHP-CGI is not running
    echo - Port 80 is blocked
)
echo.

pause


