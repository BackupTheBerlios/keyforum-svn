@echo off
echo STARTING MYSQL...
start ..\mysql\bin\mysqld --defaults-file=..\mysql\bin\my.cnf --standalone
echo;
pause
