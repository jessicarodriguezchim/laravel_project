@echo off
title Laravel scheduler - cada 60 segundos (cierra esta ventana para detener)
cd /d "%~dp0"
call "%~dp0schedule-resolve-php.bat"
if errorlevel 1 pause & exit /b 1

if not exist "%~dp0storage\logs" mkdir "%~dp0storage\logs"

:loop
"%PHP_EXE%" artisan schedule:run >> "%~dp0storage\logs\scheduler.log" 2>&1
timeout /t 60 /nobreak >nul
goto loop
