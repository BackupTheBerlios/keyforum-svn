@ECHO OFF

IF EXIST .svn\README.txt GOTO nosvn
IF EXIST CORE\perl\bin\perl.exe GOTO nodupe

ren COMMON\mysql\data\keyforum_base keyforum

cd CORE\Perl
ECHO Premi il pulsante EXTRACT quando ti viene richiesto...
start /w perl.exe

ECHO Terminato... ora esegui start_keyforum.bat
ECHO;
pause

goto fine


:nosvn

ECHO Non usare questo file direttamente nello snapshot SVN
ECHO fai prima un Export (da tortoise: tasto destro TortoiseSVN / Export )
ECHO in una nuova directory !
ECHO;
pause
goto fine

:nodupe
ECHO Questo script e' gia' stato eseguito, fai eventualmente un nuovo
ECHO Export in una nuova directory
ECHO;
pause
goto fine


:fine