package searcher;
use CGI;
my $CRLF="\015\012";

sub start {
	my ($var,$get, $post, $env,$header)=@_;  # Prende i parametri che il webserver passa allo script
	$$header.= "Expires: Mon, 26 Jul 1997 05:00:00 GMT".$CRLF;
	$$header.= "Cache-Control: no-store, no-cache, must-revalidate".$CRLF;
	$$header.= "Cache-Control: post-check=0, pre-check=0".$CRLF;
	$$header.= "Pragma: no-cache".$CRLF;
	$$header.= "Content-Type: text/plain".$CRLF;
	$get_query = new CGI($get);
	$post_query = new CGI($post);
	my $bbid=pack("H*",$env->{idboard});
	return Error("Id Board Non valida per effettuare la ricerca ".$env->{idboard}."<bR>") if length($bbid)!=20;
	return Error("Board Non trovata tra quelle iscritte.<br>") unless exists $var->{"ShSession::Gate"}->{$bbid};
	my $params = $get_query->Vars();
	delete $params->{'board'};
	my $sentnum=$var->{"ShSession::Gate"}->{$bbid}->GenericRequest('HASH_REQ',$params);
	($sentnum) ?
		(print "La ricerca è stata inoltrata con successo a $sentnum nodi connessi!\n") :
		(print "Nessun utente è connesso momentaneamente per questa board\n");
	
	return 1;
}

sub Error {
	print shift;
	return shift;	
}
return \&start;



	#while (my ($key, $value)=each %{$var->{"ShSession::Gate"}}) {
	#	print "$key => $value\n<br>";
	#}