@echo off
echo ========================================
echo Deploying to Production Server 72.60.78.65
echo ========================================
echo.

REM Check if there are uncommitted changes
git status --short
if errorlevel 1 (
    echo Error: Unable to check git status
    pause
    exit /b 1
)

echo.
echo Current changes:
git status --short
echo.

REM Commit changes
set /p confirm="Do you want to commit these changes? (y/n): "
if /i "%confirm%"=="y" (
    git add .
    set /p commit_msg="Enter commit message: "
    git commit -m "%commit_msg%"
    
    if errorlevel 1 (
        echo.
        echo Note: No changes to commit or commit failed
    )
) else (
    echo Skipping commit...
)

echo.
echo ========================================
echo Step 1: Pushing to GitHub (Backup)
echo ========================================
git push origin main
if errorlevel 1 (
    echo.
    echo ⚠️ Warning: GitHub push failed. Continue? (y/n)
    set /p continue_github=""
    if /i not "%continue_github%"=="y" (
        echo Deployment cancelled.
        pause
        exit /b 1
    )
)
echo ✅ GitHub backup complete

echo.
echo ========================================
echo Step 2: Pushing to Production Server
echo ========================================
echo.
echo ⚠️ NOTE: Make sure you've added the production remote:
echo git remote add production ssh://username@72.60.78.65/path/to/repo.git
echo.
echo Current remotes:
git remote -v
echo.

set /p push_production="Push to production now? (y/n): "
if /i "%push_production%"=="y" (
    git push production main
    if errorlevel 1 (
        echo.
        echo ❌ Error: Production push failed!
        echo.
        echo Possible issues:
        echo 1. Production remote not configured
        echo 2. SSH authentication failed
        echo 3. Server not reachable
        echo.
        echo To add production remote, run:
        echo git remote add production ssh://username@72.60.78.65/path/to/repo.git
        echo.
        pause
        exit /b 1
    )
    echo ✅ Production push complete
) else (
    echo Skipped production push
    pause
    exit /b 0
)

echo.
echo ========================================
echo Step 3: Run Deployment on Server
echo ========================================
echo.
set /p deploy_server="Run deployment script on server? (y/n): "
if /i "%deploy_server%"=="y" (
    set /p server_user="Enter SSH username: "
    echo.
    echo Connecting to %server_user%@72.60.78.65...
    echo Running deployment script...
    echo.
    
    ssh %server_user%@72.60.78.65 "bash /var/www/html/skripsi/deploy.sh"
    
    if errorlevel 1 (
        echo.
        echo ⚠️ Warning: Server deployment script had issues
        echo Check server logs for details
    ) else (
        echo ✅ Server deployment complete
    )
) else (
    echo.
    echo ⚠️ Remember to manually run deployment on server:
    echo ssh username@72.60.78.65 "bash /var/www/html/skripsi/deploy.sh"
)

echo.
echo ========================================
echo ✅ Deployment Process Complete!
echo ========================================
echo.
echo 🌐 Application URL: http://72.60.78.65
echo.
echo Next steps:
echo 1. Test the application: http://72.60.78.65
echo 2. Check real-time features work
echo 3. Monitor server logs for any issues
echo.
echo To check server logs:
echo ssh username@72.60.78.65 "tail -f /var/www/html/skripsi/storage/logs/laravel.log"
echo.
pause

