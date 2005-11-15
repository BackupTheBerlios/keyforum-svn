@echo off

rem MYSQL
echo STARTING MySQL
start COMMON\mysql\bin\mysqld --defaults-file=COMMON\mysql\bin\my.cnf --standalone

rem APACHE
echo STARTING Apache
cd WEBSERVER\Apache\bin
StartApache.exe

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