@echo off
..\..\WEBSERVER\apache\bin\pv -f -k apache.exe -q
if not exist ..\..\WEBSERVER\apache\logs\httpd.pid GOTO exit
del ..\..\WEBSERVER\apache\logs\httpd.pid

:exit
