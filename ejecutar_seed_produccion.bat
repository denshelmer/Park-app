@echo off
title Seeder Modo Produccion - ParkApp
cd /d "%~dp0"
echo ===================================================
echo   RESTAURANDO BASE DE DATOS A MODO PRODUCCION
echo ===================================================
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0seed_produccion.ps1"
echo.
pause
