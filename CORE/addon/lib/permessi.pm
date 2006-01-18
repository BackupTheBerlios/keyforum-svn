package Permessi;
use Itami::BinDump;
use strict;
sub new {
    my ($packname,$fname,$id)=@_;
    my $this=bless({},$packname);
           # AUTORE - DATE
    
}

sub LoadPermessi {
    my ($this,$autore,$data)=@_;
    my $newthis=bless({},'SingPerm');
    return $newthis;
}


package SingPerm;
sub CanDo {
    my ($this, $keyword)=@_;
    return undef;
    return undef;
}
sub AddPermit {
    my ($this,$keyword)=@_;
    $this->{permessi}->{$keyword}=1;
}

1;


#            "SELECT VALIDATE,EDIT_REPLY,EDIT_THREAD,EDIT_MSGHE,EDIT_USER_DATA,ADD_SEZ,EDIT_CONF,SEZ_GROUP "
#            ."FROM ".$fname."_autper, ".$fname."_permission AS permission "
#            ."WHERE AUTORE=? AND `DATE`<=? AND PERMISSION_ID=permission.ID ORDER BY `DATE` DESC LIMIT 1");