@echo off
cd /d "%~dp0"
start "" /B php artisan serve --host=0.0.0.0 --port=8000
start "" /B yarn dev
pause
