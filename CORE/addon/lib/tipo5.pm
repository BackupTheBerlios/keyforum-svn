package tipo5;
# Dati utente
use strict;
my $thistype=5;
$GLOBAL::TIPI->{$thistype}=\&new;
sub SelectQuery {
    my $this=shift;
    my $tosend=[];
    push(@$tosend,'SELECT `HASH`, `DATE`, \''.$thistype.'\' AS `TYPE`,`SIGN`, `TITLE`, `BODY` FROM ".$ForumName."_admin WHERE `HASH`=?;');
    return $tosend;
}
sub new {
    my ($fname,$id)=@_;
    my $this=bless({},"tipo".$thistype);
    print "Creato un tipo $thistype\n";
    @{$this}{'fname','id'}=($fname,$id);
    return $this;
}
sub priorita {
    return 3;
}
1;