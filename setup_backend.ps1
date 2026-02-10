# Check for local PHP
$localPhpDir = Join-Path $PSScriptRoot "tools\php"
if (Test-Path (Join-Path $localPhpDir "php.exe")) {
    Write-Host "Found local PHP installation. Using it..."
    $env:PATH = "$localPhpDir;$env:PATH"
}

# Check for PHP and Composer
if (-not (Get-Command "php" -ErrorAction SilentlyContinue)) {
    Write-Error "PHP is not installed or not in PATH. Please run install_local_php.ps1 first."
    exit 1
}
if (-not (Get-Command "composer" -ErrorAction SilentlyContinue)) {
    Write-Error "Composer is not installed or not in PATH. Please run install_local_php.ps1 first."
    exit 1
}

# Backup existing backend
if (Test-Path "backend") {
    Write-Host "Backing up existing backend to backend_backup..."
    if (Test-Path "backend_backup") {
        Remove-Item -Recurse -Force "backend_backup"
    }
    Rename-Item "backend" "backend_backup"
} else {
    Write-Error "No 'backend' folder found."
    exit 1
}

# Create new Laravel project
Write-Host "Creating new Laravel project..."
composer create-project laravel/laravel backend

cd backend

# Install API and Sanctum
Write-Host "Installing API and Sanctum..."
php artisan install:api

# Copy user files back
Write-Host "Restoring user files..."
$backup = "../backend_backup"

# Copy Controllers (excluding Controller.php if it conflicts, but usually we want user's)
Copy-Item -Recurse -Force "$backup/app/Http/Controllers/*" "app/Http/Controllers/"

# Copy Models
Copy-Item -Recurse -Force "$backup/app/Models" "app/"

# Copy Requests
if (Test-Path "$backup/app/Http/Requests") {
    Copy-Item -Recurse -Force "$backup/app/Http/Requests" "app/Http/"
}

# Copy Middleware
if (Test-Path "$backup/app/Http/Middleware") {
    Copy-Item -Recurse -Force "$backup/app/Http/Middleware" "app/Http/"
}

# Copy Migrations
Copy-Item -Recurse -Force "$backup/database/migrations/*" "database/migrations/"

# Copy Seeders
Copy-Item -Recurse -Force "$backup/database/seeders/*" "database/seeders/"

# Copy Routes (api.php)
Copy-Item -Force "$backup/routes/api.php" "routes/"

# Register middleware in bootstrap/app.php (Laravel 11 style)
Write-Host "Configuring middleware..."
$bootstrapFile = "bootstrap/app.php"
$bootstrapContent = Get-Content $bootstrapFile
$newContent = $bootstrapContent -replace '//', '' # Remove potential comments if needed
# Finding the place to inject alias
# We look for ->withMiddleware(function (Middleware $middleware) {
# and append the alias registration.

# Simple string replacement for the standard fresh file
$middlewareCode = '->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            "role" => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })'

# Replace the default empty middleware block
$newContentString = $bootstrapContent -join "`n"
$newContentString = $newContentString -replace '->withMiddleware\(function \(Middleware \$middleware\) \{\s*\}\)', $middlewareCode

Set-Content -Path $bootstrapFile -Value $newContentString

# Configure .env
Write-Host "Configuring .env..."
if (Test-Path ".env") {
    (Get-Content ".env") `
    -replace "DB_CONNECTION=sqlite", "DB_CONNECTION=mysql" `
    -replace "# DB_HOST=127.0.0.1", "DB_HOST=127.0.0.1" `
    -replace "# DB_PORT=3306", "DB_PORT=3306" `
    -replace "# DB_DATABASE=laravel", "DB_DATABASE=epracownik" `
    -replace "# DB_USERNAME=root", "DB_USERNAME=root" `
    -replace "# DB_PASSWORD=", "DB_PASSWORD=" `
    | Set-Content ".env"
}

# Run migrations and seeders (optional, user might want to do it manually)
Write-Host "Running migrations..."
php artisan migrate --force

Write-Host "Seeding database..."
php artisan db:seed

Write-Host "Backend setup complete! You can run 'php artisan serve' to start the server."
