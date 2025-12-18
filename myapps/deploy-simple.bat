@echo off
echo ========================================
echo Simple Deployment to 72.60.78.65
echo ========================================
echo.

REM Check for uncommitted changes
git status --short
echo.

REM Commit changes
set /p confirm="Commit changes? (y/n): "
if /i "%confirm%"=="y" (
    git add .
    set /p commit_msg="Commit message: "
    git commit -m "%commit_msg%"
    echo.
)

REM Push to GitHub
echo ========================================
echo Pushing to GitHub...
echo ========================================
git push origin main
if errorlevel 1 (
    echo ❌ GitHub push failed!
    pause
    exit /b 1
)
echo ✅ Pushed to GitHub
echo.

REM Deploy to server
echo ========================================
echo Deploying to Server 72.60.78.65
echo ========================================
set /p server_user="Enter SSH username: "
echo.
echo Connecting to server...
echo.

ssh %server_user%@72.60.78.65 "cd /var/www/html/skripsi && git pull origin main && ./deploy.sh"

if errorlevel 1 (
    echo.
    echo ❌ Deployment failed!
    echo.
    echo Manual deployment:
    echo 1. ssh %server_user%@72.60.78.65
    echo 2. cd /var/www/html/skripsi
    echo 3. git pull origin main
    echo 4. ./deploy.sh
    echo.
) else (
    echo.
    echo ========================================
    echo ✅ Deployment Complete!
    echo ========================================
    echo.
    echo 🌐 URL: http://72.60.78.65
    echo.
)

pause

