@echo off
ECHO STOPPING MYSQL...
..\mysql\bin\mysqladmin --defaults-file=..\mysql\bin\my.cnf shutdown --user=root --password=
ECHO;
pause