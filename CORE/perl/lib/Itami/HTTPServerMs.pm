package HTTPServerMs;
# Classe per la gestione di più sessioni HTTP.
# Ver. 0.01a
use strict;
use HTTP::Status;
use HTTP::Date qw(time2str);
use MIME::Types;
use Itami::upipe;
my $CRLF = "\015\012";
# SendSub=>\&SendSub; La funzione che deve essere richiamata (socket, Data) per inviare i dati
# Destroy=>\&Desotry; La funzione chiamata quando l'oggetto viene distrutto.
# httpd=>"C:\https"; La directory delle pagine
# CGI => {php=>"C:\php\php.exe", pl=>"C:\perl\bin\perl.exe"}  CGI vari con i relativi eseguibili
# 
sub new($%) {
	my ($packname, %sconf)=@_;
	my $this=bless({},$packname);
	$this->{Session}={};
	$this->{Conf}=\%sconf;
	$this->{MimeTypes}=MIME::Types->new;
	$this->{Conf}->{Env}->{DOCUMENT_ROOT}=$this->{Conf}->{httpd};
	return $this;
}

sub NewSession {
	my ($this,$name,%client_data)=@_;
	delete $this->{Session}->{"$name"} if exists $this->{Session}->{"$name"};
	my ($key, $value);
	if (exists $this->{Conf}->{Env}) {
		while (($key, $value)=each(%{$this->{Conf}->{Env}})) {
			$client_data{"$key"}=$value unless exists $client_data{"$key"};
		}
	}
	$this->{Session}->{"$name"}={};
	$this->{Session}->{"$name"}->{Item}=$name;
	$this->{Session}->{"$name"}->{RecData}="";
	$this->{Session}->{"$name"}->{OverData}=0;
	$this->{Session}->{"$name"}->{Env}=\%client_data;
	#print "nuova sezione $name\n";
	return 1;
}
sub RecData {
	my ($this, $name, $data, $sock)=@_;
	#print "entr $name\n";
	return -1 unless exists $this->{Session}->{"$name"};
	$this->{Session}->{$name}->{RecData}.=$data;
	unless (exists $this->{Session}->{$name}->{Env}->{REQUEST_URL}) {
		$this->GotHeader($name) || return undef;
		$this->HeaderReq($name) || return undef;
	}
	unless ($this->{Session}->{"$name"}->{OverData}) {
		$this->OverData($name) || return undef;
		$this->{Session}->{$name}->{OverData}=1;
	}
	$this->SendReply($name,$sock);
}
sub SendReply {
	my ($this,$name,$sock)=@_;
	my $url=$this->{Session}->{$name}->{Env}->{REQUEST_URL};
	my ($param,$ext,$buf)=('','','');
	$param=$1 if $url=~ s/\?(.+)$//;
	return $this->send_error($name,400) if $url=~ /\.\./;
	
	if (-d $this->{Conf}->{httpd}.$url) {
		foreach $buf (sort(keys(%{$this->{Conf}->{INDEX}}))) {
			if (-e $this->{Conf}->{httpd}.$url.$this->{Conf}->{INDEX}->{$buf}) {
				$url.=$this->{Conf}->{INDEX}->{$buf};
				last;
			}
		}
	}
	#print "HTTP SERVER: ".$sock->peerhost." sulla ".$sock->sockport." richiede $url\n";
	return $this->send_error($name,404) unless -e $this->{Conf}->{httpd}.$url;
	#print "dir ".$this->{Conf}->{httpd}.$url."\n";
	
	$ext=$1 if $url=~ /\.(\w+?)$/;
	#print "ext $ext\n";
	$ext=lc $ext;
	if (exists $this->{Conf}->{CGI}->{$ext}) {
		$this->CGI($name,$this->{Conf}->{CGI}->{$ext},$this->{Conf}->{httpd},$url,$param,$sock);
	} else {
		$this->SendFile($name,$this->{Conf}->{httpd}.$url);
	}
	
	return;
}
sub CGI {
	my ($this, $name, $exe,$httpd,$url,$param,$sock)=@_;
	my $postd=$this->{Session}->{$name}->{PostData};
	$this->{Session}->{$name}->{Env}->{"REDIRECT_STATUS"}=200;
	$this->{Session}->{$name}->{Env}->{SCRIPT_FILENAME}=$httpd.$url;
	$this->{Session}->{$name}->{Env}->{ORIG_PATH_TRANSLATED}=$httpd.$url;
	$this->{Session}->{$name}->{Env}->{REDIRECT_QUERY_STRING}=$param if $param;
	$this->{Session}->{$name}->{Env}->{QUERY_STRING}=$param if $param;
	$this->{Session}->{$name}->{Env}->{ORIG_SCRIPT_FILENAME}=$exe;
	$this->{Session}->{$name}->{Env}->{GATEWAY_INTERFACE}="CGI/1.1";
	$this->{Session}->{$name}->{Env}->{SYSTEMROOT}=$ENV{SYSTEMROOT} if exists $ENV{SYSTEMROOT};
	$this->{Session}->{$name}->{Env}->{COMSPEC}=$ENV{COMSPEC} if exists $ENV{COMSPEC};
	if (defined $postd) {
		$this->{Session}->{$name}->{Env}->{CONTENT_LENGTH}=length $postd;
		$this->{Session}->{$name}->{Env}->{CONTENT_TYPE}=$this->GetHeader($name,'http_Content-Type');
	}
	if ($exe eq "cpt") {
		if (ref ($this->{Conf}->{PerlScript}) eq "CODE") {
			#print "perlscript\n";
			my ($page,$header)=$this->{Conf}->{PerlScript}->($this->{Conf}->{httpd},$url,$sock,$param,
									$postd,
									$this->{Session}->{$name}->{Env});
			$this->{Conf}->{SendSub}->($name,$this->send_basic_header(200)
									   ."Content-Length: ".length($page).$CRLF
									   #."Connection: close".$CRLF
									   .$header
									   .$CRLF.$page);
			#return $this->Remove($name,1);
			return 1;
		} else {
				$this->send_error($name,404);
				return undef;
		}
	}
	
	my %oldenv=%ENV;
	%ENV=%{$this->{Session}->{$name}->{Env}};
	unless ($this->{Conf}->{CGI_MODE}) {
		#print "standard open\n";
		#print "Eseguo $exe del file $httpd.$url\n";
		my $pagina=OpProc::xopen($exe,$httpd.$url,$this->{Session}->{$name}->{PostData});
		%ENV=%oldenv;
		$this->{Conf}->{SendSub}->($name,$this->send_basic_header(200).($pagina || ''));
		return 1;
	}
	my $type=ref($sock);
	#print "new direct open $type\n";
	send($sock,$this->send_basic_header(200)."Content-Length: 1$CRLF",0);
	#."Content-Length: 5$CRLF"."Connection: close$CRLF"
	#."Content-Length: 1$CRLF"
	#send($sock,"HTTP/1.1 200 OK$CRLF"."Date: Tue, 01 Mar 2005 18:18:24 GMT$CRLF"."Server: Apache/2.0.49 (Win32)$CRLF"."Connection: close$CRLF",0);
	OpProc::open_rep($exe,$httpd.$url,$this->{Session}->{$name}->{PostData},$sock,$this->send_basic_header(200));
	%ENV=%oldenv;
	#return $this->Remove($name,0);
	return 1;
}

sub SendFile {
	my ($this,$name,$filename)=@_;
	open (PAGE, $filename) or return $this->send_error($name,404);
	binmode PAGE if -B $filename;
	my ($buff,$file);
	$file.=$buff while read(PAGE,$buff,3000,0);
	close PAGE;
	my $mime=$this->{MimeTypes}->mimeTypeOf($filename);
	$mime="Content-Type: ".$mime.$CRLF if $mime;
	$this->{Conf}->{SendSub}->($name,
							   $this->send_basic_header(200)
							   .$mime
							   ."Content-Length: ".length($file)
							   .$CRLF.$CRLF.$file);
}
sub CanSend {
	my ($this, $name)=@_;
	#print "potrei spedire altri ma muoio\n";
	return $this->Remove($name);
}
sub Remove {
	my ($this, $name,$close)=@_;
	delete $this->{Session}->{$name};
	$this->{Conf}->{Destroy}->($name,$close) if exists $this->{Conf}->{Destroy};
}
sub send_error {
	my ($this, $name, $error)=@_;
	    my $mess = status_message($error);

    $mess = "<html><head><title>$error $mess</title></head><body>\n<h1>$error $mess</h1></body></html>";
	my $msg.=$this->send_basic_header($error);
	$msg .= "Content-Type: text/html$CRLF";
	$msg .= "Content-Length: " . length($mess) . $CRLF;
    $msg .= $CRLF;
	$msg .= $mess;
	$this->{Conf}->{SendSub}->($name, $msg);
	$this->Remove($name);
}
sub send_status_line
{
    my($this, $status, $message, $proto) = @_;
    $status  ||= RC_OK;
    $message ||= status_message($status) || "";
    $proto   ||= "HTTP/1.1";
    return "$proto $status $message$CRLF";
}
sub send_basic_header
{
    my $this = shift;
    my $msg.=$this->send_status_line(@_);
    $msg .= "Date: ".time2str(time).$CRLF;
    $msg .= "Server: https$CRLF";
	#$msg .= "Connection: close$CRLF";
	return $msg;
}

###########################################################################
#  Molto del codice seguente è preso dalla libreria HTTP::Daemon		  #
#  Seguono le SUB di questa libreria									  #
###########################################################################


sub OverData {
	my ($this, $name)=@_;
	    # Find out how much content to read
    my $te  = $this->GetHeader($name,'http_Transfer-Encoding');
    my $ct  = $this->GetHeader($name,'http_Content-Type');
    my $len = $this->GetHeader($name,'http_Content-Length');
	#print "provo a prendere $len di data\n";
    if ($te) {
		$this->send_error($name,501); 	# Unknown transfer encoding
		print "non conosco il trasfer encoding\n";
		return undef;

    } elsif ($ct && lc($ct) =~ m/^multipart\/\w+\s*;.*boundary\s*=\s*(\w+)/) {
		# Handle multipart content type
		my $boundary = "$CRLF--$1--$CRLF";
		my $index;
		while (1) {
			$index = index($this->{Session}->{$name}->{RecData}, $boundary);
			last if $index >= 0;
			# end marker not yet found
			return undef;
		}
		$index += length($boundary);
		$this->{Session}->{$name}->{PostData}=substr($this->{Session}->{$name}->{RecData}, 0, $index);
		substr($this->{Session}->{$name}->{RecData}, 0, $index) = '';

    } elsif ($len) {
		# Plain body specified by "Content-Length"
		my $missing = $len - length($this->{Session}->{$name}->{RecData});
		return undef if $missing>0;

		if (length($this->{Session}->{$name}->{RecData}) > $len) {
			$this->{Session}->{$name}->{PostData}=substr($this->{Session}->{$name}->{RecData},0,$len);
			substr($this->{Session}->{$name}->{RecData}, 0, $len) = '';
		} else {
			$this->{Session}->{$name}->{PostData}=$this->{Session}->{$name}->{RecData};
			$this->{Session}->{$name}->{RecData}='';
		}
    }
	#print "presi\n";
    return 1;
	
}
sub HeaderReq {
	my ($this, $name)=@_;
    if ($this->{Session}->{$name}->{RecData} !~ s/^(\S+)[ \t]+(\S+)(?:[ \t]+(HTTP\/\d+\.\d+))?[^\012]*\012//) {
		$this->send_error($name,400);  # BAD_REQUEST
		return undef;
    }
    my $method = $1;
    my $uri = $2;
    my $proto = $3 || "HTTP/0.9";
	if ($method ne "GET" && $method ne "POST") {
		$this->send_error($name,400);
		return undef;
	}
	$this->{Session}->{$name}->{Env}->{REQUEST_METHOD}=$method;
	$this->{Session}->{$name}->{Env}->{REQUEST_URL}=$uri;
	$this->{Session}->{$name}->{Env}->{SERVER_PROTOCOL}=$proto;
	#print "m:$method u:$uri p:$proto n:$name\n";
	my($key, $val);
	while ($this->{Session}->{$name}->{RecData} =~ s/^([^\012]*)\012//) {
		$_ = $1;
		s/\015$//;
		if (/^([^:\s]+)\s*:\s*(.*)/) {
			$this->push_header($name,"HTTP_".$key, $val) if $key;
			($key, $val) = ($1, $2);
		} elsif (/^\s+(.*)/) {
			$val .= " $1";
		} else {
			last;
		}
	}
	$this->push_header($name,"HTTP_".$key, $val) if $key && $val;
	
	return 1;
}
sub push_header {
	my ($this, $name, $key, $value)=@_;
	return undef unless exists $this->{Session}->{$name};
	$key=~ tr/[a-z]/[A-Z]/;
	$key=~ s/\-/_/;
	$this->{Session}->{$name}->{Env}->{$key}=$value unless exists $this->{Session}->{$name}->{Env}->{$key};
}
sub GetHeader {
	my ($this, $name, $key)=@_;
	$key=~ tr/[a-z]/[A-Z]/;
	$key=~ s/\-/_/;
	return $this->{Session}->{$name}->{Env}->{"$key"} if exists $this->{Session}->{$name}->{Env}->{"$key"};
	return undef;
}
sub GotHeader {
	my ($this, $name)=@_;
	# HTTP::Daemon code :)
	# loop until we have the whole header in $buf
	$this->{Session}->{$name}->{RecData} =~ s/^(?:\015?\012)+//;  # ignore leading blank lines \012=\n
	if ($this->{Session}->{$name}->{RecData} =~ /\012/) {  # potential, has at least one line
		if ($this->{Session}->{$name}->{RecData} =~ /^\w+[^\012]+HTTP\/\d+\.\d+\015?\012/) {
			if ($this->{Session}->{$name}->{RecData} =~ /\015?\012\015?\012/) {
				return 1;  # we have it
			} elsif (length($this->{Session}->{$name}->{RecData}) > 16*1024) {
				$this->send_error($name,413); # REQUEST_ENTITY_TOO_LARGE
				return undef;
			}
		} else {
			return 1;  # HTTP/0.9 client
		}
	} elsif (length($this->{Session}->{$name}->{RecData}) > 16*1024) {
		$this->send_error($name,414); # REQUEST_URI_TOO_LARGE
		return undef;
	}
	return undef;	
}



1;