@echo off
REM Si falla la deteccion, descomenta y pon la ruta que te da: where php
REM set "PHP_EXE=C:\Users\kekev\PHP\php.exe"

if not "%PHP_EXE%"=="" (
  if exist "%PHP_EXE%" exit /b 0
  echo [ERROR] PHP_EXE no existe: %PHP_EXE%
  exit /b 1
)

where php >nul 2>&1
if not errorlevel 1 (
  set "PHP_EXE=php"
  exit /b 0
)

if exist "%USERPROFILE%\PHP\php.exe" (
  set "PHP_EXE=%USERPROFILE%\PHP\php.exe"
  exit /b 0
)

if exist "C:\php\php.exe" (
  set "PHP_EXE=C:\php\php.exe"
  exit /b 0
)

if exist "C:\xampp\php\php.exe" (
  set "PHP_EXE=C:\xampp\php\php.exe"
  exit /b 0
)

for /d %%D in ("C:\laragon\bin\php\php-*") do (
  if exist "%%~D\php.exe" (
    set "PHP_EXE=%%~D\php.exe"
    goto :ok
  )
)

echo.
echo [ERROR] No se encontro php.exe
echo En PowerShell ejecuta:  where php
echo Luego edita schedule-resolve-php.bat linea 3 con esa ruta.
echo.
exit /b 1

:ok
exit /b 0
