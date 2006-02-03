package Permessi;
use strict;
use Digest::MD5;

sub new {
    my ($packname,$fname,$id)=@_;
    my $this=bless({},$packname);
    $this->{fname}=$fname;
    $this->{SelVal}="SELECT VALORE FROM ".$this->{fname}."_permessi WHERE AUTORE=? AND CHIAVE_A=? AND CHIAVE_B=? AND `DATE`<? ORDER BY `DATE` DESC LIMIT 1";
    $this->{'InsVal'}=$GLOBAL::SQL->prepare("INSERT INTO ".$this->{fname}."_permessi (AUTORE,`DATE`,CHIAVE_A,CHIAVE_B,VALORE) VALUES(?,?,?,?,?)");
    return $this;
}

sub CanDo {
    my ($this,$autore,$data,$chiave1,$chiave2)=@_;
    return undef if length($autore) != 16;
    if (my $val=$GLOBAL::SQL->selectrow_array($this->{SelVal},undef,($autore,$chiave1 || '', $chiave2 || '',$data))) {
        return $val;
    }
    return undef;
}
# I permessi delle sezione
sub SezPerm {
    my ($this,$sezione,$data,$chiave1,$chiave2)=@_;
    $sezione=Digest::MD5::md5(int($sezione));
    return undef if length($sezione) != 16;
    if (my $val=$GLOBAL::SQL->selectrow_array($this->{SelVal},undef,($sezione,$chiave1 || '', $chiave2 || '',$data))) {
        return $val;
    }
    return undef; 
}
sub KeyRing {
    my ($this,$key_id,$data,$chiave1,$chiave2)=@_;
    $key_id=Digest::MD5::md5("KeyRing:".int($key_id));
    #print unpack "H*",$key_id;print "\n";
    return undef if length($key_id) != 16;
    if (my $val=$GLOBAL::SQL->selectrow_array($this->{SelVal},undef,($key_id,$chiave1 || '', $chiave2 || '',$data))) {
        return $val;
    }
    return undef; 
}
sub EditPermessi {
    my ($this,$autore,$data,$chiave1,$chiave2,$valore)=@_;
    return undef if length($autore) != 16;
    $this->{'InsVal'}->execute($autore,$data,$chiave1 || '', $chiave2 || '',$valore || '');
    return 1;
}

1;


#            "SELECT VALIDATE,EDIT_REPLY,EDIT_THREAD,EDIT_MSGHE,EDIT_USER_DATA,ADD_SEZ,EDIT_CONF,SEZ_GROUP "
#            ."FROM ".$fname."_autper, ".$fname."_permission AS permission "
#            ."WHERE AUTORE=? AND `DATE`<=? AND PERMISSION_ID=permission.ID ORDER BY `DATE` DESC LIMIT 1");