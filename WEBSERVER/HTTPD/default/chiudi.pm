package chiuso;
use CGI;
my $CRLF="\015\012";

sub start {
my ($var,$get, $post, $env,$header)=@_;  # Prende i parametri che il webserver passa allo script+
exit(0);
return 1;
}

sub Error {
print shift;
return shift; 
}
return \&start;