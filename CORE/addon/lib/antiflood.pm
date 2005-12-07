package AntiFlood;
use SDBM_File;
use Fcntl;
use strict;

sub new {
    my ($pack,$fname,$id)=@_;
    return undef if ref($GLOBAL::Fconf->{$id}->{ANTIFLOOD_AUTH}) ne "HASH";
    my @raf; # regole anti flood
    my $ref=$GLOBAL::Fconf->{$id}->{ANTIFLOOD_AUTH};
    # Controllo che le regole antiflood siano valide e le aggiungo ad un vettore
    foreach my $buf (sort(keys(%{$ref}))) {
        next if ref($ref->{$buf}) ne "HASH";
        $ref->{$buf}->{RANGE_TIME}=int($ref->{$buf}->{RANGE_TIME});
        $ref->{$buf}->{MAX_MSG}=int($ref->{$buf}->{MAX_MSG});
        next if $ref->{$buf}->{MAX_MSG}==0 or $ref->{$buf}->{RANGE_TIME}<10;
        push(@raf,$ref->{$buf});
    }
    return undef if $#raf<0; #esco se non ci sono regole valide
    my $this=bless({},'AntiFlood');
    $this->{rule}=\@raf;
    my $filename=$GLOBAL::CONFIG->{TEMP_DIRECTORY}."/".$fname."AF.dbm";
    #Creo il file DBM dove salvare i dati
    tie(my %dbm, 'SDBM_File', $filename , O_RDWR | O_CREAT, 0666) or return error("Impossibile aprire in scrittura i file dbm $filename.",undef,1);
    $this->{DBM}=\%dbm;
    # Riempo il file DBM con gli hash presenti nel database
    
    print "ANTIFLOOD: Il forum prevede regole antiflood. Creazione lista...\n";
    my $sth=$GLOBAL::SQL->prepare("SELECT `HASH`,`WRITE_DATE`,`AUTORE` FROM ".$fname."_congi WHERE `TYPE`='1' OR `TYPE`='2' OR `TYPE`='5'");
    $sth->execute();
    my $cont=0;
    while (my $tmp=$sth->fetchrow_arrayref) {
        $cont++;
        $this->dbminsert($tmp->[2],$tmp->[1]);
    }
    print "ANTIFLOOD: Inseriti $cont hash in lista.\n";
    return $this;
}

sub error {
    print STDERR "Errore nel modulo AntiFlood: ".$_[0]."\n";
    print STDOUT $_[0];
    die() if $_[2];
    return $_[1];
    
}
sub Check {
    my ($this,$autore,$date)=@_;
    my $ff=0;
    foreach my $buf (@{$this->{rule}}) {
        return undef if $this->{DBM}->{$autore.pack("I",int($date/$buf->{RANGE_TIME})).chr($ff++)}>=$buf->{MAX_MSG};
    }
    return 1;
}
sub dbminsert {
    my ($this,$autore,$date)=@_;
    my $ff=0;
    foreach my $buf (@{$this->{rule}}) {
        $this->{DBM}->{$autore.pack("I",int($date/$buf->{RANGE_TIME})).chr($ff++)}++;
    }    
}
1;