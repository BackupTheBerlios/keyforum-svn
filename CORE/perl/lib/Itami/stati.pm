package stati;
my %query;
my $DB;
sub declare {
	$DB=shift;
	my ($table)=@_;
	$query{update}=$DB->prepare("UPDATE `$table` SET VALORE1=?, VALORE2=?,VALORE3=?, VALORE4=? WHERE CHIAVE1=? AND CHIAVE2=? AND CHIAVE3=?");
	$query{updateadd}=$DB->prepare("UPDATE `$table` SET VALORE1=VALORE1 + ?, VALORE2=VALORE2 + ?,VALORE3=VALORE3 + ?, VALORE4=VALORE4 + ? WHERE CHIAVE1=? AND CHIAVE2=? AND CHIAVE3=?");
	$query{insert}=$DB->prepare("INSERT INTO `$table` (VALORE1,VALORE2,VALORE3,VALORE4,CHIAVE1,CHIAVE2,CHIAVE3) VALUES (?,?,?,?,?,?,?)");
}

sub update {
	return undef unless defined $DB;
	my @vet=@_;
	$query{update}->execute(@vet);
	return 1 if $query{update}->rows;
	$query{insert}->execute(@vet);
}

sub updateadd {
	return undef unless defined $DB;
	my @vet=@_;
	$query{updateadd}->execute(@vet);
	return 1 if $query{updateadd}->rows;
	$query{insert}->execute(@vet);
}
1;