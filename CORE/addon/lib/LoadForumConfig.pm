package LoadForumConfig;
use strict;

sub Load {
    my ($fname,$id)=@_;
    my $sth=$GLOBAL::SQL->prepare("SELECT `GROUP`, `FKEY`, `SUBKEY`,`VALUE` FROM ".$fname."_conf WHERE present='1' ORDER BY `GROUP`, `FKEY`,`SUBKEY`");
    $sth->execute or return Error($GLOBAL::SQL->errstr."\n");
    my (@tmp);
    $GLOBAL::Fconf->{$id}={};
    addhashref($GLOBAL::Fconf->{$id},@tmp) while @tmp=$sth->fetchrow_array;
    $sth->finish;
}


sub addhashref {
    my ($hash,@vet)=@_;
    return undef if $#vet<1;
    if ($#vet==1) {
	return if $vet[0] eq "";
	$hash->{$vet[0]}=$vet[1];
	return 1;
    }
    my $key=shift @vet;
    if ($key ne "") {
	$hash->{$key}={} if ref($hash->{$key}) ne "HASH";
	return addhashref($hash->{$key},@vet);
    }
    return addhashref($hash,@vet);
}

sub Error {
    print STDERR shift;
    return shift || undef;	
}



1;