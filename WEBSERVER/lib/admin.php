<?PHP

class Admin {
    var $comandi;
    var $privata;
    function Admin($privatepwd='') {
        $this->comandi=array();
        $this->privata=$privatepwd;
    }
    
      
    function EditSez($SEZID,$SEZ_NAME,$SEZ_DESC,$ORDINE,$FIGLIO,$ONLY_AUTH=1,$NEED_PERM=0,$HIDE=0,$ALIAS='',$REDIRECT='') {
        global $db,$SNAME;
        
        // se non ho fornito un id, creo una nuova sezione
        if(!$SEZID)
         {
         $db->query("insert into {$SNAME}_sez (ID,PENDING) VALUES (null,'2')");
         $SEZID=$db->insert_id;
         }
        $sez=array(SEZID=>$SEZID, SEZ_NAME=>$SEZ_NAME,SEZ_DESC=>$SEZ_DESC,ORDINE=>$ORDINE,FIGLIO=>$FIGLIO,ONLY_AUTH=>$ONLY_AUTH,NEED_PERM=>$NEED_PERM,HIDE=>$HIDE,ALIAS=>$ALIAS,REDIRECT=>$REDIRECT);
        $this->comandi['EditSez'][$SEZID]=$sez;
    }
    function EditCat($SEZID,$SEZ_NAME,$SEZ_DESC,$ORDINE,$FIGLIO,$ONLY_AUTH=1,$NEED_PERM=0) {
        $ORDINE+=9000;
 	$this->EditSez($SEZID,$SEZ_NAME,$SEZ_DESC,$ORDINE,$FIGLIO,$ONLY_AUTH,$NEED_PERM);
    }
    function ConfTable($CHIAVE1,$CHIAVE2,$CHIAVE3,$VALORE,$delete=0) {
        $this->comandi['ConfTable'][]=array(a=>$CHIAVE1,b=>$CHIAVE2,c=>$CHIAVE3,d=>$VALORE,'delete'=>$delete);
    }
    function EditPerm($autore,$chiave1,$chiave2,$valore,$date=0) {
        $this->comandi['EditPerm'][]=array(autore=>$autore,chiave1=>$chiave1,chiave2=>$chiave2,valore=>$valore,data=>$date);
    }
    function KeyRing($keyid,$chiave1,$chiave2,$valore,$date=0) {
        $this->comandi['EditPerm'][]=array(autore=>md5("KeyRing:".$keyid,true),chiave1=>$chiave1,chiave2=>$chiave2,valore=>$valore,data=>$date);
    }
    function AuthMem($autore) {
        global $core;
        $this->comandi[AuthMem][]=array(HASH=>$autore,AUTH=>$core->GetSign($md5,$this->privata));
    }
    function Send2Core($title) {
            global $core;
            $messaggio['BODY']=$core->Var2BinDump($this->ReturnVar());
	    $messaggio['TITLE']=$title;
	    $messaggio['TYPE']=1; 
	    $messaggio['_PRIVATE']=$this->privata;
            return $core->AddMsg($messaggio); 
    }
    
    
    function ReturnVar() {
        return $this->comandi;
    }
}

?>