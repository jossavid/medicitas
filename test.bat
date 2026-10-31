@echo off
cd /d %~dp0
set PHP_CMD=php
if exist C:\xampp\php\php.exe set PHP_CMD=C:\xampp\php\php.exe
if not exist storage\medicitas.sqlite (
  echo Inicializando base de datos...
  "%PHP_CMD%" database\init.php
)
echo.
echo MediCitas se abrira en http://localhost:8000
start "" http://localhost:8000
"%PHP_CMD%" -S localhost:8000 -t public public\router.php
pause
