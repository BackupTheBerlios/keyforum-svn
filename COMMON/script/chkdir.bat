@echo off
ECHO DIRECTORY CHECK
IF EXIST "c:\keyforum\fake.file" GOTO fine
ECHO KEYFORUM NEEDS CONFIGURATION
ECHO;
pause
install_keyforum.bat
:fine
ECHO OK
ECHO;
