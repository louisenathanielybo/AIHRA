@echo off
REM Add Laragon binaries to PATH
set PATH=C:\laragon\bin\php\php-8.3.26-Win32-vs16-x64;C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin;C:\laragon\bin\nodejs\node-v22;%PATH%

echo Starting Laravel Development Server...
echo.
echo Project: AIHRA HR Assistant
echo URL: http://localhost:8000
echo.
echo Press Ctrl+C to stop the server
echo.

php artisan serve
