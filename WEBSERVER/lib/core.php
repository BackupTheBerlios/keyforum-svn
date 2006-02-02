<?PHP

class CoreSock {
	var $connesso;
	var $coresock;
	var $std;
	var $errnum;
	var $errmsg;
	var $buf;
	var $header;
	var $lungh;
	var $prov;
	var $gmt_time;
	function CoreSock() {
		$this->connesso=0;
		$this->std = new FUNC;
		$this->errmsg='';
		$this->errnum=0;
		$this->header=0;
		$this->buf='';
		$this->prov=0;
		
	}
	function Connect($ip='127.0.0.1',$porta=40565) {
		if ($this->prov) return false;
		$this->prov=1;
		$this->coresock=@socket_create ( AF_INET, SOCK_STREAM,getprotobyname('tcp'));
		if (!@socket_connect($this->coresock, $ip, $porta)) 
			return($this->save_error('Impossibile connettersi al core:'.socket_strerror(socket_last_error()),1));
		$this->connesso=1;
		return 1;
	}
	function Send($var) {
	 global $corecalls;
	 
	 $corecalls++;
		if (!$this->connesso) {
			$this->Connect();
			if (!$this->connesso) return false;
		}
		if (!($binarydata=$this->std->var2binary($var))) return false;
		if(!socket_write($this->coresock,"\x15".pack("I",strlen($binarydata)).$binarydata)) 
			return($this->save_error('Impossibile inviare dati al core:'.socket_strerror(socket_last_error()),2));
			return true;
	}
	function Read($timeout=4) {
		if (!$this->connesso) return false;
		if ($this->_bufferizza($var)) return $var;
		$start=time();
		while(time()-$start < $timeout){
			if (!$this->connesso) return false;
			$this->_ricevi();
			if ($this->_bufferizza($var)) {
				$this->gmt_time=$var['CORE']['INFO']['GMT_TIME'];
				return($var);}
		}
		return false;
	}
	function _ricevi() {
		if (!$this->connesso) return false;
		if (!socket_select($read=array($this->coresock), $write = NULL, $except = NULL, 1)) 
			return false;
		$buf=socket_read ($this->coresock, 200000);
		if(!strlen($buf)) {
			$this->save_error('Il core ha chiuso la connessione in modo inaspettato.',3);
			return($this->chiudi());
		}
		$this->buf.=$buf;
		return true;
	}
	function _bufferizza(&$var) {
		if (!$this->header) {
		    if (strlen($this->buf)<5) return false;
			if ($this->_substr($this->buf,0,1,'') != "\x15") return $this->chiudi();
			list(,$this->lungh)=unpack("I",$this->_substr($this->buf,0,4,''));
			$this->header=1;
		}
		if (strlen($this->buf)<$this->lungh) return(false);
		$pacchetto=$this->_substr($this->buf,0,$this->lungh,'');
		$this->header=0;
		return $this->std->binary2var($pacchetto,$var);
	}
	function chiudi() {
		socket_close($this->coresock);
		$this->connesso=0;
		return false;
	}
	function save_error($msg,$num) {
		$this->errmsg=$msg;
		$this->errnum=$num;
		return false;
	}

	function _substr(&$stringa,$start,$length,$replace) {
		$ritorno=substr($stringa,$start,$length);
		$stringa=substr_replace($stringa,$replace,$start,$length);
		return $ritorno;
	}
	function Var2BinDump($array) {
		$tosend['FUNC']['var2BinDump']=$array;
		$this->Send($tosend);
		$risp=$this->Read();
		return $risp['FUNC']['var2BinDump'];
	}
	function NewUser($nick,$publickey,$privatekey,$passwd='') {
		$utente[PKEYDEC]=$publickey;
		$utente[AUTORE]=$nick;
		$utente[TYPE]=2; # Utente
		$utente[_PRIVATE]=$privatekey;
		$utente[_PWD]=$passwd;
		return $this->AddMsg($utente);
	}
	function AddMsg($array) {
		global $forum_id;
		$array['FDEST']=$forum_id;
		if (strlen($this->gmt_time)>10) {
			$array['DATE']=$this->gmt_time;
		} else {
			$array['DATE']=time();
		}
		$tosend['FORUM']['ADDMSG']=$array;
		$this->Send($tosend);
		$risp=$this->Read();
		return $risp['FORUM']['ADDMSG'];
	}
	function GetSign($md5,$privkey,$pwd='') {
		$req[RSA][FIRMA][0][md5]=$md5;
		$req[RSA][FIRMA][0][priv_pwd]=$privkey;
		$req[RSA][FIRMA][0][priv_key]=$pwd;
		$this->Send($req);
		$risp=$this->Read();
		return $risp[RSA][FIRMA][$md5];
	}
	function GenRsaKey($pwd='',$output=0) {
		$req['RSA']['GENKEY']['CONSOLE_OUTPUT'] = $output;
		$req['RSA']['GENKEY']['PWD'] = $pwd;
		$this->Send($req);
		$coreresp = $this->Read(120);
		$ret[pub] = $coreresp['RSA']['GENKEY']['pub'];		// in decimale
		$ret[priv]= $coreresp['RSA']['GENKEY']['priv'];		// in base64
		return $ret;
	}
}
?>