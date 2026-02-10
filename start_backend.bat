@echo off
cd %~dp0
tools\php\php.exe backend\artisan serve
pause
