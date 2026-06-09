@echo off
cd /d "%~dp0"
php bin/console app:seed-dungeons
pause
