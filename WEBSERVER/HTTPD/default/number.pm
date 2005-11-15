package numeratore;
use CGI;
my $CRLF="\015\012";

sub start {
	my ($var,$get, $post, $env,$header)=@_;  # Prende i parametri che il webserver passa allo script+
	$$header.= "Expires: Mon, 26 Jul 1997 05:00:00 GMT".$CRLF;
	$$header.= "Cache-Control: no-store, no-cache, must-revalidate".$CRLF;
	$$header.= "Cache-Control: post-check=0, pre-check=0".$CRLF;
	$$header.= "Pragma: no-cache".$CRLF;
	$$header.= "Content-Type: application/x-javascript".$CRLF;
        $get_query = new CGI($get);
	$bbid=pack("H*",$env->{idboard});
        my $params = $get_query->Vars();
	print "<!---\n";
	return Error("document.write('".$params->{'err1'}.$env->{idboard}."<bR>');\n") if length($bbid)!=20;
	return Error("document.write('".$params->{'err2'}."<br>');\n") unless exists $var->{"ShSession::Gate"}->{$bbid};
	my $num=$var->{"ShSession::Gate"}->{$bbid}->Iscritti;
	($num) ? print("document.write('".$params->{'node1'}.$num.$params->{'node2'}."');") :
			print("document.write('".$params->{'err3'}."');");
	print "\n//  FINE OSCURAMENTO   --->\n\n";
	return 1;
}

sub Error {
	print shift;
	return shift;	
}
return \&start;

