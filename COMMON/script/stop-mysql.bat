@echo off
ECHO STOPPING MYSQL...
..\mysql\bin\mysqladmin shutdown --user=root --password=
ECHO;
pause