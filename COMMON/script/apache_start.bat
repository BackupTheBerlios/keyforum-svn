@echo off
echo Please close this command only for Shutdown
echo Apache 2 is starting ...

..\..\WEBSERVER\apache\bin\apache.exe

if errorlevel 255 goto finish
if errorlevel 1 goto error
goto finish

:error
echo.
echo Impossibile avviare Apache
echo Apache could not be started
pause

:finish
