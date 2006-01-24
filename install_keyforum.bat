@ECHO OFF

echo ****************************
echo *                          *
echo * KEYFORUM INSTALL         *
echo *                          *
echo ****************************
echo;

if exist WEBSERVER\apache\bin\php-cgi.exe GOTO Normal
if not exist WEBSERVER\apache\bin\php-cgi.exe GOTO Abort

:Abort
echo PHP NOT FOUND
echo can't continue...
pause
GOTO END

:Normal

echo STARTING MYSQL
start COMMON\mysql\bin\mysqld --defaults-file=COMMON\mysql\bin\my.cnf --standalone

set PHP_BIN=WEBSERVER\apache\bin\php-cgi.exe

rem creating valid php.ini
%PHP_BIN%  -q -n  COMMON\script\install\install-phpini.php

rem do install
%PHP_BIN%  -q COMMON\script\install\install.php

echo STOPPING MYSQL
COMMON\mysql\bin\mysqladmin --defaults-file=COMMON\mysql\bin\my.cnf shutdown --user=root --password=

echo;
echo ALL DONE...


:END
