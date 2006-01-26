@echo off
ECHO DIRECTORY CHECK
IF EXIST "E:\keyforum\newcore\KeyForum\WEBSERVER\Apache\conf\fakefile.null" GOTO fine
ECHO KEYFORUM NEEDS CONFIGURATION
ECHO;
install_keyforum.bat
:fine
ECHO OK
ECHO;
