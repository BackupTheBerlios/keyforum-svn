package ticket;
use strict;
use Time::Local;
use Digest::SHA1;

my %ipreq;

sub new {
    my ($packname,$fname,$fid)=@_;
    my $this=bless({},$packname);
    return $this if $GLOBAL::Fconf->{$fid}->{DISABLE_TICKET};
    $this->{active}=1;
    $this->{fid}=$fid;
    print "Forum id: $fid\n";
    $this->{coutvalid}=$GLOBAL::SQL->prepare("SELECT count(*) FROM ".$fname."_ticket WHERE START_DATE<? AND END_DATE>?");
    $this->{TakeTicket}=$GLOBAL::SQL->prepare("SELECT * FROM ".$fname."_ticket WHERE START_DATE<? AND END_DATE>? ORDER BY END_DATE LIMIT 1");
    $this->{DeleteTicket}=$GLOBAL::SQL->prepare("DELETE FROM ".$fname."_ticket WHERE HASH=?");
    $this->{InsertTicket}=$GLOBAL::SQL->prepare("INSERT INTO ".$fname."_ticket (HASH,ID,KEY_ID,START_DATE,END_DATE,AUTH) VALUES(?,?,?,?,?,?)");
    $this->{DeleteOldTicket}=$GLOBAL::SQL->prepare("DELETE FROM ".$fname."_ticket WHERE END_DATE<?");
    $this->CountValidTicket;
    $this->{DeleteOldTicket}->execute(GMTIME());
    return $this;
}

sub NewNode { # Quando un nodo si connette a noi
    my ($this,$ogg)=@_;
    return undef unless $this->{active};
    return undef if $this->CountValidTicket > 0;
    keyforum::Sender($this->{fid},'TicketReq',$ogg,1);
    print "Mi mancano dei ticket. Li richiedo a $ogg\n";
}
sub TicketReq { # Risponde alle richieste dei ticket degli altri nodi
    my ($this,$data,$ogg,$ip)=@_;
    print "$ogg mi richiede dei ticket\n";
    return undef unless $this->{active};
    return undef unless $data;
    return undef if $this->{validi}<=1;
    my $num=0;
    ($this->{validi}>2) ? ($num=0) : ($num=1);
    if (exists $ipreq{$ip}) { # Se questo ip ci ha gia fatto una richiesta in questa sessione
        (time-$ipreq{$ip}>7200) ? ($ipreq{$ip}=time) : (return undef); # Massimo su distribuisce un ticket ad IP ogni 2 ore
    } else {
        $ipreq{$ip}=time();
    }
    $this->{TakeTicket}->execute(GMTIME(),GMTIME()); # Prende prima i ticket che scadono prima (quelli che scadono dopo ce li teniamo noi)
    my @lista;
    while (my $ticket = $this->{TakeTicket}->fetchrow_hashref) {
        print "Trovato un ticket, spedito\n";
        $this->{DeleteTicket}->excute($ticket->{HASH});
        push(@lista,$ticket);
        last if ++$num>=2;
    }
    keyforum::Sender($this->{fid},'TicketResp',$ogg,\@lista) if $num;
}
sub TicketResp {  # Analizza i ticket ricevuti da un altro nodo
    my ($this,$data,$ogg)=@_;
    print "$ogg mi sta regalando dei ticket ($data):)\n";
    return undef unless $this->{active};
    return undef if ref($data) ne "ARRAY";
    foreach my $buf (@$data) {
        $this->AddTicket($buf);
    }
}
sub AddTicket { # Convalida il ticket e lo aggiunge se è valido :)
    my ($this,$buf)=@_;
    print "Ricevo un ticket\n";
    return undef if ref($buf) ne "HASH";
    return -5 if $buf->{'END_DATE'}<GMTIME();
    return -6 if $buf->{START_DATE} =~ /\D/;
    return -7 if length($buf->{START_DATE})<10;
    return -8 unless $this->ValidateTicket($buf);
    print "Ticket aggiunto\n";
    $this->{InsertTicket}->execute($buf->{HASH},int($buf->{'ID'}),int($buf->{'KEY_ID'}),int($buf->{'START_DATE'}),int($buf->{'END_DATE'}),$buf->{AUTH});
    print "Aggiunto un ticket valido :) \n";
    return 1;
}
sub ValidateTicket {
    my ($this,$ticket)=@_;
    return undef if ref($ticket) ne "HASH";
    my $permessi=$GLOBAL::Permessi->{$this->{fid}};
    $ticket->{HASH}=TicketSha1($ticket);
    my $pkey=$permessi->KeyRing($ticket->{'KEY_ID'},$ticket->{'START_DATE'},'TICKET','');
    return undef if length($pkey)<150;
    my $futils=$GLOBAL::ForUtility->{$this->{fid}};
    return $futils->CheckSignPkey($ticket->{HASH},$ticket->{AUTH},$pkey);
}
sub MakeTicket {
    my ($this,$info)=@_;
    return -10 unless $this->{active};
    my $ticket={};
    $ticket->{'END_DATE'}=int($info->{'END_DATE'});
    $ticket->{'START_DATE'}=int($info->{'START_DATE'});
    $ticket->{'KEY_ID'}=int($info->{'KEY_ID'});
    $ticket->{'ID'}=int($info->{'ID'});
    my $sha1=TicketSha1($ticket);
    my ($password,$item);
    return -25 unless $item=$GLOBAL::ForUtility->{$this->{fid}};
    return -1 unless $info->{'_PRIVATE'};  # -1 manca la chiave privata
    return -2 unless $password=$item->GetPrivateKey($info->{'_PRIVATE'},$info->{'_PWD'}); # -2 impossibile aprire la chiave privata
    return -3 unless $ticket->{'AUTH'}=$item->Firma($sha1,$password);  # -3 non riesco a firmare il ticket
    return $this->AddTicket($ticket);
}
sub TicketSha1 {
    my $ticket=shift;
    return undef if ref($ticket) ne "HASH";
    return Digest::SHA1::sha1($ticket->{'ID'}.'a'.$ticket->{'KEY_ID'}.'a'.$ticket->{'START_DATE'}.'a'.$ticket->{'END_DATE'});
}
sub CountValidTicket {
    my $this=shift;
    my @conta;
    $this->{coutvalid}->execute(GMTIME(),GMTIME());
    return $this->{validi}=$conta[0] if @conta = $this->{coutvalid}->fetchrow_array;
    return undef;
}

sub GMTIME {
    return Time::Local::timelocal(gmtime(time()+$GLOBAL::ntpoffset));
}




1;