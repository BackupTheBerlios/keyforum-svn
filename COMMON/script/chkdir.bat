@echo off
ECHO DIRECTORY CHECK
IF EXIST "D:\bidone\bakaforum\newtest\KeyForum\WEBSERVER\Apache\conf\keyforum.conf" GOTO fine
ECHO KEYFORUM NEEDS CONFIGURATION
ECHO;
install_keyforum.bat
:fine
ECHO OK
ECHO;
