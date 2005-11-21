@echo off
ECHO DIRECTORY CHECK
IF EXIST "D:\bidone\bakaforum\keyforum 42\WEBSERVER\Apache\conf\keyforum.conf" GOTO fine
ECHO KEYFORUM NEEDS CONFIGURATION
ECHO;
pause
install_keyforum.bat
:fine
ECHO OK
ECHO;
