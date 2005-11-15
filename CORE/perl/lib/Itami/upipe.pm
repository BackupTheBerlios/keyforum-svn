package OpProc;
use strict;
use IO::Handle;
use IO::Pipe;


#	$exe path di un eseguibile
#	$getp parametri passati all'eseguibile tramite ARGV
#	$postd Dati passati al programma tramite STDIN
#	Il valore di ritorno è il testo stampato su STDOUT dal programma

# La funzione usa metodi di basso livello.
# open3 e open2 hanno alcuni problemi con la chiusura delle pipe.
# Forse sono io che non so leggere le istruzioni :(
sub xopen {
	my ($exe,$getp,$postd)=@_;
	$getp="" unless defined $getp;
	my ($writer, $reader,$pagina,$vecchioin);	# Dichiaro le variabili
	pipe($reader,$writer);	# Creo una coppia di pipe per inviare i dati al processo tramite STDIN
	$writer->autoflush(1);	# Svuoto il canale
	open $vecchioin, "<&", \*STDIN or return undef;	# Mi salvo il ref del vecchio STDIN
	open (STDIN, "<&",$reader) or return undef;		# Sovrascrivo il ref di STDIN con la pipe
	open (PROG, "$exe $getp |") or return undef;	# Avvio il processo aprendo una pipe in lettura
	close $reader;		# Chiudo la pipe destinata all'altro processo.
	print $writer $postd if defined $postd; # Invio i dati tramite la pipe all'altro processo
	$pagina.=$_ while <PROG>;	# Salvo in uno scalare il testo che l'altro processo produce su STDOUT
	close PROG;	# Il processo dovrebbe essere già chiuso a questo punto ma di prassi...
	close $writer;	# Chiudo la pipe, anche se funzionava quando la lasciavo aperto
	open STDIN, "<&",$vecchioin or die "impossibile aprire STDIN $!\n" if $vecchioin; # Reimposto il vecchio STDIN :)
	# Tutto è nuovamente come prima e torno quello che il processo ha prodotto :)
	return $pagina;
}

sub open_rep {
	my ($exe, $getp, $postd, $sock,$header)=@_;
	#print "param ".join("-",@_)."\n";
	$getp="" unless defined $getp;
	my ($old_stderr, $old_stdout);
	open $old_stdout, "<&", \*STDOUT or return undef;
	open $old_stderr, "<&", \*STDERR or return undef;
	open STDOUT, ">&", $sock or return undef;
	open STDERR, ">&", $sock or return undef;
	open (PROG, "|$exe $getp") or return undef;
	print PROG $postd if defined $postd;
	PROG->autoflush(1);
	open STDERR, ">&",$old_stderr or die "impossibile aprire STDERR $!\n"; # Reimposto il vecchio STDIN :)
	open STDOUT, ">&",$old_stdout or die "impossibile aprire STOUT $!\n"; # Reimposto il vecchio STDIN :)
	print "fine apertura\n";
	return 1;
}


1;