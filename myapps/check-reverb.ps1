# PowerShell script to check Reverb configuration and connection

Write-Host "=== Laravel Reverb Connection Check ===" -ForegroundColor Cyan
Write-Host ""

# Check if .env exists
if (-not (Test-Path ".env")) {
    Write-Host "❌ .env file not found!" -ForegroundColor Red
    exit 1
}

Write-Host "1. Checking .env configuration..." -ForegroundColor Yellow

# Check REVERB variables
$reverbAppId = (Get-Content .env | Select-String -Pattern "^REVERB_APP_ID=" | Select-Object -First 1) -replace "REVERB_APP_ID=", ""
$reverbAppKey = (Get-Content .env | Select-String -Pattern "^REVERB_APP_KEY=" | Select-Object -First 1) -replace "REVERB_APP_KEY=", ""
$reverbSecret = (Get-Content .env | Select-String -Pattern "^REVERB_APP_SECRET=" | Select-Object -First 1) -replace "REVERB_APP_SECRET=", ""
$reverbHost = (Get-Content .env | Select-String -Pattern "^REVERB_HOST=" | Select-Object -First 1) -replace "REVERB_HOST=", ""
$reverbPort = (Get-Content .env | Select-String -Pattern "^REVERB_PORT=" | Select-Object -First 1) -replace "REVERB_PORT=", ""

# Check VITE variables
$viteReverbKey = (Get-Content .env | Select-String -Pattern "^VITE_REVERB_APP_KEY=" | Select-Object -First 1) -replace "VITE_REVERB_APP_KEY=", ""

Write-Host "   REVERB_APP_ID: " -NoNewline
if ($reverbAppId) { Write-Host $reverbAppId -ForegroundColor Green } else { Write-Host "NOT SET" -ForegroundColor Red }

Write-Host "   REVERB_APP_KEY: " -NoNewline
if ($reverbAppKey) { Write-Host ($reverbAppKey.Substring(0, [Math]::Min(10, $reverbAppKey.Length)) + "...") -ForegroundColor Green } else { Write-Host "NOT SET" -ForegroundColor Red }

Write-Host "   REVERB_APP_SECRET: " -NoNewline
if ($reverbSecret) { Write-Host ($reverbSecret.Substring(0, [Math]::Min(10, $reverbSecret.Length)) + "...") -ForegroundColor Green } else { Write-Host "NOT SET" -ForegroundColor Red }

Write-Host "   REVERB_HOST: " -NoNewline
if ($reverbHost) { Write-Host $reverbHost -ForegroundColor Green } else { Write-Host "NOT SET" -ForegroundColor Red }

Write-Host "   REVERB_PORT: " -NoNewline
if ($reverbPort) { Write-Host $reverbPort -ForegroundColor Green } else { Write-Host "NOT SET" -ForegroundColor Red }

Write-Host "   VITE_REVERB_APP_KEY: " -NoNewline
if ($viteReverbKey) { 
    if ($viteReverbKey -eq '${REVERB_APP_KEY}') {
        Write-Host "✅ Using REVERB_APP_KEY reference" -ForegroundColor Green
    } else {
        Write-Host ($viteReverbKey.Substring(0, [Math]::Min(10, $viteReverbKey.Length)) + "...") -ForegroundColor Green
    }
} else { 
    Write-Host "NOT SET" -ForegroundColor Red 
}

Write-Host ""
Write-Host "2. Checking if port $reverbPort is in use..." -ForegroundColor Yellow
$portCheck = netstat -ano | findstr ":$reverbPort"
if ($portCheck) {
    Write-Host "   ✅ Port $reverbPort is in use (Reverb server might be running)" -ForegroundColor Green
    Write-Host "   Processes using port $reverbPort :" -ForegroundColor Cyan
    $portCheck | ForEach-Object { Write-Host "      $_" -ForegroundColor Gray }
} else {
    Write-Host "   ❌ Port $reverbPort is NOT in use" -ForegroundColor Red
    Write-Host "   ⚠️  Reverb server is probably NOT running!" -ForegroundColor Yellow
    Write-Host "   Run: php artisan reverb:start" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "3. Checking BROADCAST_CONNECTION..." -ForegroundColor Yellow
$broadcastConn = (Get-Content .env | Select-String -Pattern "^BROADCAST_CONNECTION=" | Select-Object -First 1) -replace "BROADCAST_CONNECTION=", ""
if ($broadcastConn -eq "reverb") {
    Write-Host "   ✅ BROADCAST_CONNECTION=reverb" -ForegroundColor Green
} else {
    Write-Host "   ❌ BROADCAST_CONNECTION=$broadcastConn (should be 'reverb')" -ForegroundColor Red
}

Write-Host ""
Write-Host "=== Summary ===" -ForegroundColor Cyan
Write-Host ""
if ($reverbAppKey -and $reverbSecret -and $reverbAppId -and ($broadcastConn -eq "reverb")) {
    Write-Host "✅ Configuration looks good!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Next steps:" -ForegroundColor Yellow
    Write-Host "1. Make sure Reverb server is running: php artisan reverb:start" -ForegroundColor White
    Write-Host "2. Clear config cache: php artisan config:clear" -ForegroundColor White
    Write-Host "3. Rebuild assets: npm run build" -ForegroundColor White
    Write-Host "4. Refresh browser and check console" -ForegroundColor White
} else {
    Write-Host "❌ Configuration incomplete!" -ForegroundColor Red
    Write-Host "Please check your .env file and ensure all REVERB_* variables are set." -ForegroundColor Yellow
}

