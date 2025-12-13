@echo off
REM Add Laragon binaries to PATH
set PATH=C:\laragon\bin\php\php-8.3.26-Win32-vs16-x64;C:\laragon\bin\nodejs\node-v22;%PATH%

echo Starting Vite Development Server...
echo.
echo This will watch for frontend changes and rebuild automatically
echo Press Ctrl+C to stop the server
echo.

npm run dev
