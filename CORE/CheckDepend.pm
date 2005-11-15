package CheckDepend;
# COntrolla se il messaggio che si vuole inserire ha tutti i riferimenti in regola.
# Ad esempio un risposta ad un messaggio non ancora presente nel database non ha i requisiti per essere aggiunto al DB.
# In poche parola si fa un controllo di integrità referenziale.
# La classe ritorna l'HASH che manca per poter aggiungere il msg.
use strict;


sub new {
	my ($packname, $db, $fname)=@_;	
	my $this=bless({}, $packname);
	$this->{AutoreExists}=$db->prepare("SELECT count(*) FROM ".$fname."_membri WHERE HASH=?");
	
	return $this;
}
sub Check {
	my ($this, $msg)=@_;	
	return undef if $msg->{TYPE}>2;
	return $msg->{AUTORE} unless $this->AutoreExists($msg->{AUTORE});
	if ($msg->{TYPE}
	
}


sub AutoreExists {
	my ($this, $autore)=@_;	
	$this->{AutoreExists}->execute($autore);
	my $num=$this->{AutoreExists}->fetchrow_hashref->[0];
	$this->{AutoreExists}->finish;
	return $num;
}



1;