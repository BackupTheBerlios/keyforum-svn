package WriteDonkeyProtocol;
use Digest::MD5;
use strict;
use socket;
#hash=16byte hash
#id=4byte id
#client_port=porta decimale del client
#name=nome client
my @tags;
@tags[1..5,8..23,32..33,48,211..213,251]=qw(name size type format copied Colletion GAP_start GAP_end desc ping fail prefer port ip version tempfile priority status availab qtime parts compress udp_port availab_compl durata bitrate codec nonso);
my %tags;
chiavizza();
sub chiavizza {
    my $num=0;
    foreach my $chiave (@tags) {
        $tags{$chiave}=pack("S",1).pack("C",$num) if $chiave;
        $num++;
    }
}
sub new {
    my ($pacchetto)=@_;
    my $this=bless({},$pacchetto);
    $this->{var}={};
    $this->{var}->{hash}=$this->RandomHash;
    return $this;
}
sub edit_var{
    my ($this,$var,$value)=@_;
    $this->{var}->{$var}=$value;
}
sub RandomHash {
    return Digest::MD5::md5(time * rand);
}
sub header {
    my ($this,$tipo,$msg)=@_;
    $msg=$tipo.$msg;
    return $msg  if $this->{var}->{DisableHeader};
    return "\xe3".pack("I",length($msg)).$msg;
    
}

sub hello_server {
    my ($this)=@_;
    my $var=$this->{var};
    my $msg=$var->{hash} . $var->{id} . pack("S",$var->{client_port});
    my %tosend;
    $tosend{name}=$var->{name};
    $tosend{port}=$var->{client_port};
    $tosend{version}=60;
    $tosend{compress}=25;
    $tosend{nonso}=47360;
    $msg.=$this->pack_meta_tag_list(\%tosend);
    return $this->header("\x01",$msg);
}
sub offer_files {
    my ($this)=@_;
    my $msg=pack("I",0);
    return $this->header("\x15",$msg);
}
sub Search_file {
    my ($this,$keyword,$altro)=@_;
    my $msg="\x01".$this->String($keyword);
    $msg.=$this->pack_meta_tag_list($altro) if ref($altro) eq "HASH";
    return $this->header("\x16",$msg);
}


#varie
sub pack_meta_tag_list {
    my ($this,$hash)=@_;
    return undef if ref($hash) ne "HASH";
    my ($msg,$num,$tmp,$punta)=('',0,'',{});
    while(my ($key,$value)=each %$hash) {
        if ($tmp=$this->pack_meta_tag($key,$value)) {
            $punta->{$tags{$key}}=$tmp;
            $num++;
        }
    }
    foreach my $buf ( sort(keys(%$punta))) {
        $msg.=$punta->{$buf};
    }
    return pack("I",$num) . $msg;  # DWORD <meta tag>
}
sub pack_meta_tag {
    my ($this,$key,$value)=@_;
    my ($tipo);
    if(exists $tags{$key}) {
        ($tipo,$value)=$this->pack_meta_tag_value($value);
        return $tipo . $tags{$key} . $value; # Tipo valore, nome della variabile, il valore
    }
    return undef;
}
sub pack_meta_tag_value {
    my ($this,$value)=@_;
    return ("\x03",pack("I",$value)) if $value!~ m/\D/ && $value<4294967296;
    return ("\x01",$value) if length($value)==16;
    return ("\x02",pack("S",length($value)).$value);
}
sub ip2id {
    my $this=shift;
    return pack "CCCC", split /\./ ,shift;
}
sub String {
    my ($this,$stringa)=@_;
    return pack("S",length($stringa)).$stringa;    
}
1;