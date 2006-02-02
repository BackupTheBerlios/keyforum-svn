<?PHP

class Admin {
    var $comandi;
    var $privata;
    function Admin($privatepwd='') {
        $this->comandi=array();
        $this->privata=$privatepwd;
    }
    function EditSez($SEZID,$SEZ_NAME,$SEZ_DESC,$ORDINE,$FIGLIO,$ONLY_AUTH=1,$NEED_PERM=0) {
        $sez=array(SEZID=>$SEZID, SEZ_NAME=>$SEZ_NAME,SEZ_DESC=>$SEZ_DESC,ORDINE=>$ORDINE,FIGLIO=>$FIGLIO,ONLY_AUTH=>$ONLY_AUTH,NEED_PERM=>$NEED_PERM);
        $this->comandi['EditSez'][$SEZID]=$sez;
    }
    function EditCat($SEZID,$SEZ_NAME,$SEZ_DESC,$ORDINE,$FIGLIO,$ONLY_AUTH=1,$NEED_PERM=0) {
        $ORDINE+=9000;
        $sez=array(SEZID=>$SEZID, SEZ_NAME=>$SEZ_NAME,SEZ_DESC=>$SEZ_DESC,ORDINE=>$ORDINE,FIGLIO=>$FIGLIO,ONLY_AUTH=>$ONLY_AUTH,NEED_PERM=>$NEED_PERM);
        $this->comandi['EditSez'][$SEZID]=$sez;
    }
    function ConfTable($CHIAVE1,$CHIAVE2,$CHIAVE3,$VALORE,$delete=0) {
        $this->comandi['ConfTable'][]=array(a=>$CHIAVE1,b=>$CHIAVE2,c=>$CHIAVE3,d=>$VALORE,'delete'=>$delete);
    }
    function EditPerm($autore,$chiave1,$chiave2,$valore,$date=0) {
        $this->comandi['EditPerm'][]=array(autore=>$autore,chiave1=>$chiave1,chiave2=>$chiave2,valore=>$valore,data=>$date);
    }
    function AuthMem($autore) {
        global $core;
        $this->comandi[AuthMem][]=array(HASH=>$autore,AUTH=>$core->GetSign($md5,$this->privata));
    }
    function ReturnVar() {
        return $this->comandi;
    }
}

?>