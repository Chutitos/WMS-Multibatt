@echo off
REM ============================================================
REM  Demo WMS Multibatt - servidor + tunel publico gratuito
REM  Doble clic y listo. La URL para enviar por WhatsApp aparece
REM  en la ventana "WMS - Tunel Cloudflare".
REM  Requiere: MySQL de XAMPP corriendo y cloudflared.exe en
REM  C:\Users\Chutitos\cloudflared\ (ver docs/DEMO.md).
REM ============================================================

cd /d "%~dp0.."

start "WMS - Servidor" cmd /k "php artisan serve --host=0.0.0.0 --port=8000"

timeout /t 3 >nul

start "WMS - Tunel Cloudflare" cmd /k "C:\Users\Chutitos\cloudflared\cloudflared.exe tunnel --url http://localhost:8000"

echo.
echo  Busca en la ventana "WMS - Tunel Cloudflare" la linea con
echo  https://xxxxx.trycloudflare.com  y enviala por WhatsApp.
echo.
echo  Para terminar la demo: cierra las dos ventanas que se abrieron.
echo.
pause
