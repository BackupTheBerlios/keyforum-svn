<?php

// classe di supporto per reply_dest.php

class ADDER {

      private $fname;
      private $magic_q;
      
      public function ADDER($sesname) 
      {
          $fname=$sesname;
          if ( get_magic_quotes_gp() ) $magic_q = 1;
          else $magic_q = 0;
      }
      
      // fa l'escape dell'array dato come input
      public function escape($input)
      {
          foreach ($input as $var) {
              if ($magic_q) $var = stripslashes($var);
              $var = mysql_real_escape_string($var);
         }
      }
      
      public function Congi($msg_hash,$msg_type,$write_date,$instime,$autore)
      {
          escape(array(&$msg_hash,&$msg_type,&$write_date,&$instime,&$autore));
          $query = "INSERT INTO " . $fname . "_congi (HASH,TYPE,WRITE_DATE,INSTIME,AUTORE) "
                 . "VALUES($msg_hash,$msg_type,$write_date,$instime,$autore);";
          if ( !(mysql_query($query)) ) die("Errore nella Query!\n");
          return true;
      }
      
      public function InsertReply($msg_hash,$rep_of,$autore,$edit_of,$date,$firma,$avatar,$title,$body,$sign,$visible)
      {
          escape(array(&$msg_hash,&$rep_of,&$autore,&$edit_of,&$date,&$firma,&$avatar,&$title,&$body,&$sign,&$visible));
          $query = "INSERT INTO $fname" . "_reply (HASH,REP_OF,AUTORE,EDIT_OF,DATE,FIRMA,AVATAR,TITLE,BODY,SIGN,Visibile) "
                   . "VALUES($msg_hash,$rep_of,$autore,$edit_of,$date,$firma,$avatar,$title,$body,$sign,$visible);";
          if ( !(mysql_query($query)) ) die("Errore nella Query!\n");
          return true;
      }
      
      public function IncrementMsgNum($autore)
      {
          escape(array(&$autore));
          $query = "UPDATE $fname" . "_membri SET msg_num=msg_num+1 WHERE HASH='$autore';";
          if ( !(mysql_query($query)) ) die("Errore nella Query!\n");
          return true;
      }
      
      public function UpDateAvatar($firma,$avatar,$date,$msg_hash)
      {
          escape(array(&$firma,&$avatar,&$edit_firma,&$msg_hash));
          $sql = "UPDATE $fname" . "_membri SET firma='$firma',avatar='$avatar',edit_firma='$date' WHERE HASH='$msg_hash' AND $date>edit_firma;";
          if ( !(mysql_query($query)) ) die("Errore nella Query!\n");
          return true;
      }
      
      public function IncMsghe($rep_of)
      {
          escape(array(&$rep_of));
          $sql = "UPDATE $fname" . "_msghe SET reply_num=reply_num+1 WHERE HASH='$rep_of';";
          if ( !(mysql_query($query)) ) die("Errore nella Query!\n");
          return true;
      }
      
      public function IncRepSez($rep_of)
      {
          escape(array(&$rep_of));
          $sql = "UPDATE $fname" . "_sez,$fname" . "_newmsg SET REPLY_NUM=REPLY_NUM+1 WHERE $fname" . "_sez.ID=$fname" . "_newmsg.SEZ "
                 . "AND $fname" . "_newmsg.visibile='1' AND $fname" . "_newmsg.EDIT_OF='$rep_of';";
          if ( !(mysql_query($query)) ) die("Errore nella Query!\n");
          return true;
      }
      
      public function UpDateLastTime($date,$autore,$rep_of,$date)
      {
          escape(array(&$date,&$autore,&$rep_of,&$date));
          $sql = "UPDATE $fname" . "_msghe SET last_reply_time='$date',last_reply_author='$autore' WHERE HASH='$rep_of' AND last_reply_time<$date;";
          if ( !(mysql_query($query)) ) die("Errore nella Query!\n");
          return true;      
      }

}
?>
