@echo off
title ParkApp - Tunel de Prueba Cloudflare
echo ==============================================================================
echo                      PARKAPP - TUNEL DE PRUEBA (CLOUDFLARE)
echo ==============================================================================
echo.
echo 1. Verifica que el modulo Apache en XAMPP este INICIADO (Verde).
echo 2. Se esta generando tu enlace temporal seguro (HTTPS)...
echo.
echo En unos segundos veras una linea con tu enlace temporal:
echo      https://xxxx-xxxx-xxxx.trycloudflare.com
echo.
echo IMPORTANTE:
echo    Para que tu cliente entre a ParkApp, agrega "/park-app/" al final:
echo    Ejemplo: https://xxxx-xxxx-xxxx.trycloudflare.com/park-app/
echo.
echo    Para DETENER el acceso externo en cualquier momento, presiona:
echo    Ctrl + C (o simplemente cierra esta ventana).
echo ==============================================================================
echo.
"C:\Program Files (x86)\cloudflared\cloudflared.exe" tunnel --url http://localhost:80
pause
