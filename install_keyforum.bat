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
echo Non trovo il PHP
echo Impossibile continuare...
pause
GOTO END

:Normal

echo AVVIO DI MYSQL
start COMMON\mysql\bin\mysqld --defaults-file=COMMON\mysql\bin\my.ini --standalone

set PHP_BIN=WEBSERVER\apache\bin\php-cgi.exe

rem prima creo un php.ini valido
%PHP_BIN%  -q -n  COMMON\script\install\install-phpini.php

rem poi tutto il resto
%PHP_BIN%  -q COMMON\script\install\install.php

echo ARRESTO DI MYSQL
COMMON\mysql\bin\mysqladmin shutdown --user=root --password=

echo;
echo TERMINATO...
pause 

:END
