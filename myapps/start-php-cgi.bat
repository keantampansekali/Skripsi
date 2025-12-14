@echo off
echo Starting PHP-CGI on port 9000...
echo.
echo This window must remain open for PHP to work.
echo Close this window to stop PHP-CGI.
echo.

REM Sesuaikan path PHP dengan lokasi instalasi PHP Anda
REM Contoh: C:\php\php-cgi.exe
REM Jika PHP sudah di PATH, bisa langsung pakai: php-cgi.exe

REM Ganti path di bawah ini sesuai lokasi PHP Anda
set PHP_PATH=C:\php\php-cgi.exe

REM Jika PHP sudah di PATH, gunakan ini:
REM set PHP_PATH=php-cgi.exe

REM Jalankan PHP-CGI
%PHP_PATH% -b 127.0.0.1:9000

pause

