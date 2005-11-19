@echo off

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
COMMON\mysql\bin\mysqladmin shutdown --user=root --password=
echo KEYFORUM STOPPED !
echo;
pause
GOTO end

:exitall
echo SI E' VERIFICATO UN PROBLEMA
echo IMPOSSIBILE AVVIARE KEYFORUM
type kstop.txt
echo;
pause

:end