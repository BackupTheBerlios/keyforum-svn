@echo off

rem controllo che il batch sia uscito in maniera pulita
rem l'ultima volta che è stato eseguito
if not exist WEBSERVER\Apache\logs\httpd.pid GOTO clean

ECHO KeyForum e' stato interrotto in maniera anomala l'ultima volta
ECHO che e' stato eseguito. Ricorda che se interrompi il programma
ECHO premendo CTRL+C devi poi rispondere NO alla domanda
ECHO "Terminare il processo batch (S/N)?"
ECHO;
ECHO Il metodo corretto per uscire da KeyForum e' fare click sul
ECHO link "chiudi" in alto a sinistra della pagina web del forum
ECHO;
PAUSE

rem STOP APACHE
echo STOPPING APACHE...
WEBSERVER\Apache\bin\pv -f -k apache.exe -q
if not exist WEBSERVER\Apache\logs\httpd.pid GOTO exitapache
del WEBSERVER\Apache\logs\httpd.pid
:exitapache

rem STOP MYSQL
echo STOPPING MYSQL...
COMMON\mysql\bin\mysqladmin --defaults-file=COMMON\mysql\bin\my.cnf  shutdown --user=root --password= 

:clean

rem controllo la directory
CALL COMMON\script\chkdir.bat

rem controllo le porte
ECHO PORTS CHECK
IF EXIST kstop.txt DEL kstop.txt
set PHP_BIN=WEBSERVER\apache\bin\php-cgi.exe
%PHP_BIN%  -q COMMON\script\kport.php

IF EXIST kstop.txt GOTO exitall
ECHO OK: ALL NEEDED PORTS ARE FREE :-)
ECHO;

rem MYSQL
echo STARTING MySQL
start COMMON\mysql\bin\mysqld --defaults-file=COMMON\mysql\bin\my.cnf --standalone

rem APACHE
echo STARTING Apache
cd WEBSERVER\Apache\bin
StartApache.exe

echo STARTING KeyForum
rem CORE
cd ..\..\..\CORE
perl\bin\perl prog.pl
cd ..
cls

rem STOP APACHE
echo STOPPING APACHE...
WEBSERVER\Apache\bin\pv -f -k apache.exe -q
if not exist WEBSERVER\Apache\logs\httpd.pid GOTO exitapache
del WEBSERVER\Apache\logs\httpd.pid
:exitapache

rem STOP MYSQL
echo STOPPING MYSQL...
COMMON\mysql\bin\mysqladmin --defaults-file=COMMON\mysql\bin\my.cnf  shutdown --user=root --password= 
echo KEYFORUM STOPPED !
echo;
REM pause
GOTO end

:exitall
echo SOME PROBLEMS OCCURS
echo IMPOSSIBLE TO START KEYFORUM
type kstop.txt
echo;
pause

:end