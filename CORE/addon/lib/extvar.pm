package ExtVar;
use Itami::BinDump;
use strict;

sub new {
    my ($packname,$fname,$id)=@_;
    my $this=bless({},$packname);
    $this->{fname}=$fname;
    my $conf=$GLOBAL::Fconf->{$id};
    $this->{id}=$id;
    $this->{UpDateMemTit}=$GLOBAL::SQL->prepare("UPDATE ".$fname."_membri SET title=?,edit_adminset=? WHERE HASH=? AND edit_adminset<?");
    $this->{UpDateMemAvat}=$GLOBAL::SQL->prepare("UPDATE ".$fname."_membri SET firma=?,avatar=?,edit_avatar=? WHERE HASH=? AND edit_avatar<?");
    $this->{UpDateMemDati}=$GLOBAL::SQL->prepare("UPDATE ".$fname."_membri SET email=?,nascita=?,provenienza=?,EXTRA=?,edit_dati=? WHERE HASH=? AND edit_dati<?");
    $this->{MSGHEVAR}=$GLOBAL::SQL->prepare("UPDATE ".$fname."_msghe SET pinned=?,home=?,special=?,`fixed`=?,block_date=?,last_admin_update=? WHERE HASH=? AND last_admin_update<?");
    #$this->{MaxSignDim}=abs(int($conf->{USER}->{SIGN}->{MAXDIM}) || '500');
    #$this->{ExtVarDim}=abs(int($conf->{USER}->{EXTVAR}->{MAXDIM}) || '4000');
    return $this;
}
# Esegue i comandi contenuti nel campo extvar
sub Execute {
    my ($this,$autore,$extvar,$date)=@_;
    return undef if ref($extvar) ne "HASH";
    return undef if length($autore) != 16;
    my $permessi=$GLOBAL::Permessi->{$this->{id}};
    $this->UpdateMyDati($autore,$extvar->{UpdateMyDati},$date) if exists($extvar->{UpdateMyDati}) && !$permessi->CanDo($autore,$date,'CANT','UPDATE_PERS_DATA');
    $this->UpdateMyAvatar($autore,$extvar->{UpdateMyAvatar},$date) if exists($extvar->{UpdateMyAvatar}) && !$permessi->CanDo($autore,$date,'CANT','UPDATE_PERS_AVAT');
    # Cambia i dati alle altre persone
    $this->ChangeOther($autore,$extvar->{ChangeOther},$date) if exists($extvar->{ChangeOther}) && $permessi->CanDo($autore,$date,'CAN','EDIT_ALL_AVAT');
}
# Cambia i dati degli altri utenti.
sub ChangeOther {
    my ($this,$autore,$extvar,$date)=@_;
    return undef if ref($extvar) ne "HASH";
    my $dest_autore=$extvar->{autore};
    return undef if length($dest_autore) != 16;
    $this->UpdateMyDati($dest_autore,$extvar->{UpdateDati},$date) if exists $extvar->{UpdateDati};
    $this->UpdateMyAvatar($dest_autore,$extvar->{UpdateAvatar},$date) if exists $extvar->{UpdateAvatar};
    $this->UpdateMySvar($dest_autore,$extvar->{UpdateSvar},$date) if exists $extvar->{UpdateSvar};
}
sub UpdateMySvar {
    my ($this,$autore,$extvar,$date)=@_;
    return undef if ref($extvar) ne "HASH";
    $this->{UpDateMemTit}->execute($extvar->{title} || '',$date,$autore,$date);
}
# Fa l'update dei dati di un utente
sub UpdateMyDati {
    my ($this,$autore,$extvar,$date)=@_;
    return undef if ref($extvar) ne "HASH";
    my $conf=$GLOBAL::Fconf->{$this->{id}};
    my $maxdimextvar=abs(int($conf->{USER}->{EXTVAR}->{MAXDIM}) || '4000');
    
    $extvar->{extra}=BinDump::MainDump($extvar->{extra}) || '';
    $extvar->{extra}=substr($extvar->{extra},0,$maxdimextvar) if length($extvar->{extra})>$maxdimextvar;
    $this->{UpDateMemDati}->execute($extvar->{email} || '',$extvar->{nascita} || '',$extvar->{provenienza} || '',$extvar->{extra} || '',$date,$autore,$date);
}
# aggiorna l'avatar e la firma con quelli più recenti
sub UpdateMyAvatar {
    my ($this,$autore,$extvar,$date)=@_;
    return undef if ref($extvar) ne "HASH";
     my $conf=$GLOBAL::Fconf->{$this->{id}};
    my $maxdimsign=abs(int($conf->{USER}->{SIGN}->{MAXDIM}) || '500');
    
    $extvar->{avatar}=substr($extvar->{avatar},0,300) if length($extvar->{avatar})>300;
    $extvar->{firma}=substr($extvar->{firma},0,$maxdimsign) if length($extvar->{firma})> $maxdimsign;
    $this->{UpDateMemAvat}->execute($extvar->{firma},$extvar->{avatar},$date,$autore,$date);
}
# Modifica i dati dei thread
sub ExecuteThread {
    my ($this,$autore,$thread,$sez,$extvar,$date)=@_;
    return undef if ref($extvar) ne "HASH";
    return undef if length($autore) != 16;
    return undef if length($thread) != 16;
    my $permessi=$GLOBAL::Permessi->{$this->{id}};
    $this->Execute($autore,$extvar,$date);
    $extvar->{'block_date'}=$date if $extvar->{'block'};
    return undef unless $permessi->CanDo($autore,$date,$sez,'IS_MOD');
    # if ( $extvar->{update_thread} ) 
    $this->{MSGHEVAR}->execute(int($extvar->{pinned}), int($extvar->{home}), int($extvar->{special}), int($extvar->{'fixed'}),
                               int($extvar->{'block_date'}),$date,$thread,$date) if exists $extvar->{update_thread};
    
}
sub ExecuteReply {
    my ($this,$autore,$thread,$repof,$extvar,$date)=@_;
    return undef if ref($extvar) ne "HASH";
    $this->Execute($autore,$extvar,$date);
}



1;