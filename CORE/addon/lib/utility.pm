package utility;

sub Conta {
    my $this=shift;
    my $query=shift;
    my $sth=$GLOBAL::SQL->prepare($query);
    $sth->execute(@_);
    return $sth->fetchrow_arrayref->[0]
}


1;