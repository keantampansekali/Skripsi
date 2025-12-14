@echo off
echo ========================================
echo Test Konfigurasi Nginx
echo ========================================
echo.

REM Deteksi lokasi nginx
where nginx.exe >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Nginx tidak ditemukan!
    echo.
    echo Silakan:
    echo 1. Install Nginx
    echo 2. Atau tambahkan nginx ke PATH
    echo 3. Atau edit script ini dan set path nginx
    echo.
    pause
    exit /b 1
)

for %%I in (nginx.exe) do set NGINX_DIR=%%~dp$PATH:I
if "%NGINX_DIR%"=="" (
    set NGINX_DIR=C:\nginx
) else (
    set NGINX_DIR=%NGINX_DIR:~0,-1%
)

echo Testing nginx configuration...
echo.

cd /d "%NGINX_DIR%"
nginx.exe -t

echo.
echo ========================================
if %errorlevel% equ 0 (
    echo [SUCCESS] Konfigurasi nginx valid!
    echo.
    echo Anda bisa menjalankan nginx sekarang.
) else (
    echo [ERROR] Konfigurasi nginx ada masalah!
    echo.
    echo Silakan perbaiki error di atas.
)
echo ========================================
echo.
pause


