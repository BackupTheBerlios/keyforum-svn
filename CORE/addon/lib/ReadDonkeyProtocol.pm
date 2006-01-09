package ReadDonkeyProtocol;
use strict;

# Variabili internet alla libreria
my @tags;

@tags[1..5,8..23,32..33,48,211..213,251]=qw(name size type format copied Colletion GAP_start GAP_end desc ping fail prefer port ip version tempfile priority status availab qtime parts compress udp_port availab_compl durata bitrate codec nonso);


sub new {
    my ($pacchetto)=@_;
    my $this=bless({},$pacchetto);
    return $this;
}

sub Estrai {
    my ($this,$msg)=@_;
    my $tipo=substr($msg,0,1,''); # Tolgo il primo byte che specifica la destinazione del pacchetto
    return $this->ID_change(\$msg) if $tipo eq "\x40";
    return $this->Server_message(\$msg) if $tipo eq "\x38";
    return $this->Server_info_data(\$msg) if $tipo eq "\x41";
    return $this->Server_status(\$msg) if $tipo eq "\x34";
    return $this->Search_file_results(\$msg) if $tipo eq "\x33";
    print "Non conosco il tipo ".unpack("C",$tipo)."\n";
}
sub ID_change { # ritorna ID_change->id numerico
    my ($this,$msg)=@_;
    my $tosend={};
    $tosend->{ID_change}=unpack("I",substr($$msg,0,4,''));
    return $tosend;
}
sub Server_message { # ritorna ID_change->id numerico
    my ($this,$msg)=@_;
    my $tosend={};
    $tosend->{Server_message}=$this->String($msg);
    return $tosend;
}
sub Server_info_data { # ritorna ID_change->id numerico
    my ($this,$msg)=@_;
    my $tosend={};
    $tosend->{Server_info_data}={};
    $tosend->{Server_info_data}->{Server_hash}=$this->Hash($msg);
    $tosend->{Server_info_data}->{Server_address}=$this->Address($msg);
    $tosend->{Server_info_data}->{Meta_tag_list}=$this->Meta_tag_list($msg);
    return $tosend;
}
sub Server_status {
    my ($this,$msg)=@_;
    my $tosend={};
    $tosend->{Server_status}={};
    $tosend->{Server_status}->{Nusers}=$this->DWORD($msg);
    $tosend->{Server_status}->{Nfiles}=$this->DWORD($msg);
    return $tosend;
}
sub Search_file_results {
    my ($this,$msg)=@_;
    my $tosend={};
    $tosend->{Search_file_results}={};
    $tosend->{Search_file_results}->{File_info_list}=$this->File_info_list($msg);
    $tosend->{Search_file_results}->{More}=$this->BYTE($msg);
    return $tosend;
}
# High level data
sub File_info_list {
    my ($this,$msg)=@_;
    my $numero=$this->DWORD($msg);
    sleep 1;
    my $tosend=[];
    for(;$numero>0;$numero--) {
        push(@$tosend,$this->File_info($msg));
    }
    return $tosend;
}
sub File_info {
    my ($this,$msg)=@_;
    my $tosend={};
    $tosend->{File_hash}=$this->Hash($msg);
    $tosend->{CLient_IP}=$this->IP($msg);
    $tosend->{CLient_PORT}=$this->PORT($msg);
    $tosend->{Meta_tag_list}=$this->Meta_tag_list($msg);
    return $tosend;
}
sub Meta_tag_list {
    my ($this,$msg,$disable_string)=@_;
    my $numero=$this->DWORD($msg);
    my ($tipo,$name,$value);
    my $tosend={};
    for(;$numero>0;$numero--) {
        $tipo=$this->BYTE($msg);
        $name=$this->Meta_tag_name($msg,$tipo>=128); #Se il tipo è maggiore di 112 allora il nome della variabile è solo numerico
        $tipo-=128 if $tipo>=128;
        $value=$this->Meta_tag_value($msg,$tipo);
        $tosend->{$name}=$value;
    }
    return $tosend;
}
sub Meta_tag_value {
    my($this,$msg,$tipo)=@_;
    return substr($$msg,0,16,'') if $tipo==1;
    return $this->String($msg) if $tipo==2;
    return $this->DWORD($msg) if $tipo==3;
    return $this->WORD($msg) if $tipo==8;
    return $this->BYTE($msg) if $tipo==9;
    return substr($$msg,0,$tipo-16,'') if $tipo>16;
    print "$tipo tipo non definito\n";
    return undef;
}
sub Meta_tag_name {
    my ($this,$msg,$disable_string)=@_;
    return $this->Special_tag($msg) if $disable_string;
    my $sptag=$this->WORD($msg);
    return $this->Special_tag($msg) if $sptag==1;
    return substr $$msg,0,$sptag,'';
}
sub Special_tag {
    my ($this,$msg)=@_;
    my $id=$this->BYTE($msg);
    ($tags[$id]) ? (return $tags[$id]) : (return "Tag$id");
}


# Basic Data
sub String {
    my ($this,$msg)=@_;
    return substr($$msg,0,$this->WORD($msg),'');
}
sub Hash {
    my ($this,$msg)=@_;
    return substr($$msg,0,16,'');
}
sub Address {  # IP=>ip N:N:N:N format  && PORT=>port number
    my ($this,$msg)=@_;
    return {IP=>$this->IP($msg),PORT=>$this->PORT($msg)};
}
sub PORT {
    my ($this,$msg)=@_;
    return unpack "S",substr $$msg,0,2,'';
}
sub IP {
    my ($this,$msg)=@_;
    return join ":", unpack "CCCC", substr $$msg,0,4,'';
}
sub DWORD {
    my ($this,$msg)=@_;
    return unpack "I", substr $$msg,0,4,'';
}
sub WORD {
    my ($this,$msg)=@_;
    return unpack "S", substr $$msg,0,2,'';
}
sub BYTE {
    my ($this,$msg)=@_;
    return unpack "C", substr $$msg,0,1,'';
}
sub QDATA {
    my ($this,$msg)=@_;
    my @dati=unpack "CCCC",substr $$msg,0,4,'';
    return {A=>$dati[0],B=>$dati[1],C=>$dati[2],D=>$dati[3]};
}

1;