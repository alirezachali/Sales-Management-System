@echo off
chcp 65001 > nul
echo ====================================
echo Setup Sales Management System
echo ====================================
echo.

REM Check for composer presence
echo [1/6] Checking the Composer installation...
where composer >nul 2>nul
if errorlevel 1 (
    echo ❌ Composer is not installed!
    pause
    exit /b 1
)
echo ✓ Composer found

REM Check for Node.js presence
echo.
echo [2/6] Checking the Node.js installation...
where node >nul 2>nul
if errorlevel 1 (
    echo ⚠ Node.js is not installed!
) else (
    echo ✓ Node.js found
)

REM Installing PHP dependencies
echo.
echo [3/6] Installing PHP dependencies with Composer...
call cd laravel-app
call composer install
if errorlevel 1 (
    echo ❌ Error in (composer install)
    pause
    exit /b 1
)
echo ✓ PHP dependencies installed.

REM Copy the .env file
echo.
echo [4/6] Setting up the .env file...
if not exist .env (
    if exist .env.example (
        copy .env.example .env
        echo ✓ .env file created.
    ) else (
        echo ⚠ .env.example file not found.
    )
) else (
    echo ✓ The .env file already exists.
)

REM Generate App key
echo.
echo [5/6] Generate App key (APP_KEY)...
call php artisan key:generate
if errorlevel 1 (
    echo ⚠ Error in key generation (Maybe it was already produced)
)

REM Installing Node dependencies
echo.
echo [6/6] Installing Node.js dependencies...
where npm >nul 2>nul
if errorlevel 1 (
    echo ⚠ Node dependencies installation failed. NPM is not installed.
) else (
    call npm install
    if errorlevel 1 (
        echo ⚠ Error in (npm install)
    ) else (
        echo ✓ Node dependencies installed successfully.
    )
)

echo.
echo ====================================
echo ✓ Program setup is complete.
echo ====================================
echo.
echo Next steps:
echo 1. Check the (.env) file settings (Database connection settings)
echo 2. Run the following command to create the database:
echo    php artisan migrate
echo.
pause
