@echo off
title Seeder Modo Pruebas - ParkApp
cd /d "%~dp0"
echo ===================================================
echo   INYECTANDO DATOS DE PRUEBA / TESTING (PARKAPP)
echo ===================================================
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0seed_pruebas.ps1"
echo.
pause
