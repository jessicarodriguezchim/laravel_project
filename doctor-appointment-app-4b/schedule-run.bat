@echo off
REM Ejecutar cada 1 minuto desde el Programador de tareas de Windows.
REM "Iniciar en" debe ser esta carpeta (doctor-appointment-app-4b).

cd /d "%~dp0"
call "%~dp0schedule-resolve-php.bat"
if errorlevel 1 exit /b 1

if not exist "%~dp0storage\logs" mkdir "%~dp0storage\logs"
"%PHP_EXE%" artisan schedule:run >> "%~dp0storage\logs\scheduler.log" 2>&1
