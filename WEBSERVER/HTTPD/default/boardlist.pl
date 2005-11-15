use strict;
use Itami::forumlib;
use CGI qw/:standard/;
use Itami::ConvData;
my $MySQL=ForumLib::SQL();
my $sth=$MySQL->prepare("SELECT `VALUE`, `SUBKEY` FROM config WHERE `MAIN_GROUP`='SHARE' AND `FKEY`='PKEY'");
my $ref;$sth->execute;
$MySQL->do("INSERT INTO temp (`CHIAVE`,`VALORE`,`TTL`) VALUES(?,?,?)",undef,$ref->[1].'_PKEY_DEC',ConvData::Base642Dec($ref->[0]),time()+3600) while $ref=$sth->fetchrow_arrayref;
print "<script language=\"javascript\">setTimeout('delayer()', 0000);\nfunction delayer(){ window.location='boardlist.php';}</script>";