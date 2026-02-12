@echo off
cd /d "%~dp0"
"%~dp0tools\php\php.exe" "%~dp0backend\artisan" serve
pause
