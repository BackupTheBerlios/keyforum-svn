package msgpila;
use strict;
use Itami::Cycle;
my $timeout=25;
sub new {
    my ($packname)=@_;
    my $this=bless({},$packname);
    $this->{lista}={};
    $this->{mancanti}={};
    $this->{inpila}={};
    $this->{timeout}=Cycle->new(15); #Dopo quanto devono essere controllati i messaggi nel buffer
    $this->{msglevati}=0;
    return $this;
}

sub addcoda {
    my ($this,$hash_mancante,$md5,$msg)=@_;
    unless (exists $this->{lista}->{$hash_mancante}) {
        $this->{lista}->{$hash_mancante} = {};
        $this->{mancanti}->{$hash_mancante}=time();
    }
    return undef if exists $this->{lista}->{$hash_mancante}->{$md5};
    $this->{lista}->{$hash_mancante}->{$md5}=$msg;
    return 1;
}

sub aggiunto {
    my ($this,$hash)=@_;
    return undef unless exists $this->{lista}->{$hash};
    print "Aggiunto un messaggio nel buffer in attesa di quello dipendente\n";
    delete $this->{mancanti}->{$hash};
    while (my ($md5,$msg)=each(%{$this->{lista}->{$hash}})) {
        $this->{inpila}->{$md5}=$msg;
        $this->{msglevati}++;
    }
    delete $this->{lista}->{$hash};
    return 1;   
}
sub preleva {
    my $this=shift;
    return undef unless $this->{msglevati};
    $this->{msglevati}=0;
    my $datornare=$this->{inpila};
    $this->{inpila}={};
    return $datornare;
}
sub checktimeout {
    my $this=shift;
    return undef unless $this->{timeout}->check;
    while (my ($key,$value)=each(%{$this->{mancanti}})) {
        if (time()-$value>$timeout) {
            print "Cancellato un messaggio nel buffer\n";
            delete $this->{mancanti}->{$key};
            delete $this->{lista}->{$key}
        }
    }
    return 1;
}


1;