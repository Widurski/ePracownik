$ErrorActionPreference = "Stop"

$phpUrl = "https://windows.php.net/downloads/releases/php-8.4.16-Win32-vs17-x64.zip"
$composerUrl = "https://getcomposer.org/installer"
$toolsDir = Join-Path $PSScriptRoot "tools"
$phpDir = Join-Path $toolsDir "php"

# 1. Create tools directory
if (-not (Test-Path $toolsDir)) { New-Item -ItemType Directory -Path $toolsDir | Out-Null }

# 2. Download PHP
Write-Host "Downloading PHP from $phpUrl..."
$phpZip = Join-Path $toolsDir "php.zip"
Invoke-WebRequest -Uri $phpUrl -OutFile $phpZip

# 3. Extract PHP
Write-Host "Extracting PHP..."
if (Test-Path $phpDir) { Remove-Item -Recurse -Force $phpDir }
Expand-Archive -Path $phpZip -DestinationPath $phpDir -Force

# 4. Configure php.ini
Write-Host "Configuring php.ini..."
$phpIniDev = Join-Path $phpDir "php.ini-development"
$phpIni = Join-Path $phpDir "php.ini"
Copy-Item $phpIniDev $phpIni

$content = Get-Content $phpIni
$newContent = $content -replace ';extension_dir = "ext"', 'extension_dir = "ext"' `
                       -replace ';extension=curl', 'extension=curl' `
                       -replace ';extension=fileinfo', 'extension=fileinfo' `
                       -replace ';extension=mbstring', 'extension=mbstring' `
                       -replace ';extension=openssl', 'extension=openssl' `
                       -replace ';extension=pdo_mysql', 'extension=pdo_mysql' `
                       -replace ';extension=zip', 'extension=zip'

Set-Content -Path $phpIni -Value $newContent

# 5. Download Composer
Write-Host "Downloading Composer..."
$composerSetup = Join-Path $phpDir "composer-setup.php"
Invoke-WebRequest -Uri $composerUrl -OutFile $composerSetup

# Install Composer
$phpExe = Join-Path $phpDir "php.exe"
& $phpExe $composerSetup --install-dir=$phpDir --filename=composer.phar

# Create composer.bat
$composerBat = Join-Path $phpDir "composer.bat"
"@""%~dp0php.exe"" ""%~dp0composer.phar"" %*" | Set-Content $composerBat

# Clean up
Remove-Item $phpZip
Remove-Item $composerSetup

Write-Host "--------------------------------------------------------"
Write-Host "PHP and Composer installed successfully in:"
Write-Host "  $phpDir"
Write-Host "To use them in this session, run:"
Write-Host '  $env:PATH = "'$phpDir';$env:PATH"'
Write-Host "--------------------------------------------------------"
