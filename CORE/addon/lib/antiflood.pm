package AntiFlood;
use SDBM_File;
use Fcntl;
use strict;
my $lista={};
sub new {
    my ($pack,$fname,$id,$regole,$name,$tipo)=@_;
    if (exists $lista->{$fname.$name}) {  # Se l'oggetto antiflood esiste già con questo nome allora torno quello
        $lista->{$fname.$name}->LoadType($tipo,$fname);  # Aggiungo a questo oggetto antiflood tutti i msg di quel tipo
        return $lista->{$fname.$name};
    }
    my $this=bless({},'AntiFlood');
    $lista->{$fname.$name}=$this;
    return $this if ref($regole) ne "HASH";
    my @raf; # regole anti flood
    # Controllo che le regole antiflood siano valide e le aggiungo ad un vettore
    foreach my $buf (sort(keys(%{$regole}))) {
        next if ref($regole->{$buf}) ne "HASH";
        $regole->{$buf}->{RANGE_TIME}=int($regole->{$buf}->{RANGE_TIME});
        $regole->{$buf}->{MAX_MSG}=int($regole->{$buf}->{MAX_MSG});
        next if $regole->{$buf}->{MAX_MSG}==0 or $regole->{$buf}->{RANGE_TIME}<10;
        push(@raf,$regole->{$buf});
    }
    return $this if $#raf<0; #esco se non ci sono regole valide
    
    $this->{rule}=\@raf;
    my $filename=$GLOBAL::CONFIG->{TEMP_DIRECTORY}."/".$fname."AF".$name.".dbm";
    #Creo il file DBM dove salvare i dati
    tie(my %dbm, 'SDBM_File', $filename , O_RDWR | O_CREAT, 0666) or return error("Impossibile aprire in scrittura i file dbm $filename.",undef,1);
    $this->{DBM}=\%dbm;
    # Riempo il file DBM con gli hash presenti nel database
    $this->LoadType($tipo,$fname);

    $this->{Attivato}=1;
    return $this;
}
sub LoadType {
    my ($this,$tipo,$fname)=@_;
    print "ANTIFLOOD: Il forum prevede regole antiflood. Creazione lista...\n";
    my $sth=$GLOBAL::SQL->prepare("SELECT `HASH`,`WRITE_DATE`,`AUTORE` FROM ".$fname."_congi WHERE `TYPE`=?");
    $sth->execute();
    my $cont=0;
    while (my $tmp=$sth->fetchrow_arrayref) {
        $cont++;
        $this->dbminsert($tmp->[2],$tmp->[1]);
    }
    print "ANTIFLOOD: Inseriti $cont hash in lista di tipo $tipo.\n";
}
sub error {
    print STDERR "Errore nel modulo AntiFlood: ".$_[0]."\n";
    print STDOUT $_[0];
    die() if $_[2];
    return $_[1];
    
}
sub Check {
    my ($this,$autore,$date)=@_;
    return 1 unless $this->{Attivato};
    my $ff=0;
    foreach my $buf (@{$this->{rule}}) {
        return undef if $this->{DBM}->{$autore.pack("I",int($date/$buf->{RANGE_TIME})).chr($ff++)}>=$buf->{MAX_MSG};
    }
    return 1;
}
sub dbminsert {
    my ($this,$autore,$date)=@_;
    return 1 unless $this->{Attivato};
    my $ff=0;
    foreach my $buf (@{$this->{rule}}) {
        $this->{DBM}->{$autore.pack("I",int($date/$buf->{RANGE_TIME})).chr($ff++)}++;
    }    
}
1;