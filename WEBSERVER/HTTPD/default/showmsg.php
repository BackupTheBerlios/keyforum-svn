<?php
include ("testa.php");
include ("mostra_messaggio.php");
$lang += $std->load_lang('lang_showmsg', $blanguage );
$SNAME=$_ENV['sesname'];

// Parser
include_once("lib/bbcode_parser.php");

// leggo i titoli utente
$member_titles=$std->CollectMemberTitles();

echo "<a href=\"searcher.php?MODO=2&amp;REPOF=" . $_REQUEST['THR_ID'] . "\">" . $lang['shmsg_findnewmsg'] . "</a><br>";

function PageSelect($pos) {
global $std;
?>
<table border="0" cellpadding="5px" cellspacing="0" width="100%">
  <tbody>
  <tr>
    <td align="left" nowrap="nowrap" width="20%">
<?php
  global $SNAME;
  global $NumPag;
  global $CurrPag;
  global $Section;
  global $blanguage;
  global $lang;
  global $closed;
  $link = "showmsg.php?SEZID=".$_REQUEST["SEZID"]."&amp;THR_ID=".$_REQUEST["THR_ID"]."&amp;pag=";
  if ($NumPag > 0) {
    echo "<span class='pagelink'>".($NumPag+1)."&nbsp;".$lang['shmsg_pages']."</span>&nbsp;";
    if ($CurrPag>0) { # Pagina precedente
      echo "<span class='pagelinklast'><a href=\"{$link}0\">&laquo;</a></span>&nbsp;";
      echo "<span class='pagelink'><a href=\"{$link}".($CurrPag-1)."\">&lt;</a></span>&nbsp;";
    }

    # Visualizzo i link solamente per un certo numero di pagine
    if ($CurrPag > $Section) {print "<span class='pagelink'>..</span>&nbsp;";}
      $StartPag = $CurrPag-$Section;
    if ($StartPag < 0) {$StartPag = 0;}
      $EndPag = $CurrPag+$Section;
    if ($EndPag > $NumPag) {$EndPag = $NumPag;}

    for ($i = $StartPag+1; $i <= $EndPag+1; $i++) {
      if ($i-1 == $CurrPag)
        echo "<span class='pagecurrent'>$i</span>&nbsp;";
      else
        echo "<span class='pagelink'><a href=\"{$link}".($i-1)."\">$i</a></span>&nbsp;";
    }
    if ($CurrPag < $NumPag - $Section) {print "<span class='pagelink'>..</span>&nbsp;";}

    if ($CurrPag<$NumPag) { # Pagina successiva
      echo "<span class='pagelink'><a href=\"{$link}".($CurrPag+1)."\">&gt;</a></span>";
      echo "&nbsp;<span class='pagelinklast'><a href=\"{$link}{$NumPag}\">&raquo; ".($NumPag+1)."</a></span>";
    }
  }

  if ($pos==1) { # Opzioni di inizio pagina
    ?>
    </td>
    <td align="right" width="80%">
      
      <?
	if ($closed)

	{
	$dclose=$std->PostDate($closed);
	echo "<img src='img/buttons/$blanguage/t_closed.gif' border='0' alt='Closed  $dclose' />";
	} else {
	echo "<a href=\"reply.php?SEZID={$_REQUEST['SEZID']}&THR_ID={$_REQUEST['THR_ID']}\"><img src='img/buttons/$blanguage/t_reply.gif' border='0' alt='Rispondi' /></a>";    
       }
     ?>

    </td>
    <td align="right" width="80%">
      <a href="writenewmsg.php?SEZID=<?=$_REQUEST[SEZID]?>" class="normalname"><img src='img/buttons/<?=$blanguage?>/t_new.gif' border='0' alt='Apri Nuovo Topic' /></a>
    </td>
    </tr>
    <?php
  } else {
    echo "
	<script type=\"text/javascript\">
	<!--
		var dom = (document.getElementById && !document.all)? 1: 0;
		function show_hide(the_id)
		{
			var obj = (dom)? document.getElementById(the_id): document.all[the_id];
			if(obj.style.visibility == \"hidden\"){
				obj.style.visibility = \"visible\";
				obj.style.display = \"block\";
			}
			else {
				obj.style.visibility = \"hidden\";
				obj.style.display = \"none\";
			}
		}
		
	-->
	</script>
	";
	if($_SESSION[$SNAME]['sess_auth']){
	  $logged="javascript:show_hide('FastReply')";
	}else{
	  $logged="login.php?SEZID=".$_REQUEST["SEZID"]."&amp;THR_ID=".$_REQUEST["THR_ID"]."&amp;pag=".$_REQUEST["pag"];
	}
  	echo "</td><td align='right' width='80%' class='normalname'><a href=\"$logged\">";
  	
  	if (!$closed) {echo "<img src='img/buttons/".$blanguage."/t_qr.gif' border='0' alt='FastReply' /></a>";}
  	
  	echo "</td><td align='right' width='80%'>";
  	
  	if ($closed) {
	$dclose=$std->PostDate($closed);
	echo "<img src='img/buttons/$blanguage/t_closed.gif' border='0' alt='Closed  $dclose' />";
  	} else {
  	echo "<a href=\"reply.php?SEZID={$_REQUEST['SEZID']}&amp;THR_ID={$_REQUEST['THR_ID']}\"><img src=\"img/buttons/".$blanguage."/t_reply.gif\" border=\"0\" alt=\"Rispondi\" /></a>";
  	}
  	
  	echo "</td><td align='right' width='80%'><a href=\"writenewmsg.php?SEZID={$_REQUEST['SEZID']}\" class=\"normalname\"><img src=\"img/buttons/".$blanguage."/t_new.gif\" border=\"0\" alt=\"Apri Nuovo Topic\" /></a></td></tr>\n";

  }
  echo "</tbody>\n</table>\n";
}

function FastReply() {
	global $lang,$userdata?>
	<script language='JavaScript' type='text/javascript'>
        <? include "bbcode.php";?>
	</script>

	<script type='text/javascript' language="javascript">
	<!--
	function altezze(){
		co1 = document.getElementById('colo-sx').offsetHeight;
		co2 = document.getElementById('colo-dx').offsetHeight;
		co3 = document.getElementById('centrale').offsetHeight;
		altok = co1;
		if (co2 > altok) altok = co2;
		if (co3 > altok) altok = co3;
		altokok = co1;
		if (co2 < altokok) altokok = co2;
		if (co3 < altokok) altokok = co3;
		document.getElementById('colo-sx').style.height = altok + 'px';
		document.getElementById('colo-dx').style.height = altok + 'px';
		document.getElementById('centrale').style.height = altok + 'px';
		document.getElementById('FastReply').style.height = altokok + 'px';
	}
	//-->
	</script>

	<div id='FastReply' style='visibility:hidden;display:none;' class='post2'>
		<form method=post action='reply_dest.php'>
			<input type='hidden' name='sezid' value='<?=$_REQUEST[SEZID]?>' />
			<input type='hidden' name='repof' value='<?=$_REQUEST[THR_ID]?>' />
		<div id='head01' align=left class='maintitle'>
			FastReply:
		</div>
		<div id='colo-sx' class='post2' align='center'><br />
			<table border='0' cellspacing='0' cellpadding='3'>
				<div align='center'><p><strong>Smiles:</strong></p></div>
				<? include("emoticons.php");?>
			</table>
   		</div>
   		<div id='colo-dx'></div>
  		<div id='centrale' class='post2' align='center'><br />
			<table border='0' cellspacing='0' cellpadding='0'>
				<tr>
					<td align='center'>
						<? include "buttons.php"; ?>
					</td>
				</tr>
		 		<tr>
					<td>
						<textarea name='body' rows='8' cols='70' wrap='virtual' onselect='storeCaret(this);' onclick='storeCaret(this);' onkeyup='storeCaret(this);'></textarea><br /><br />
						<center>
						<input type='submit' name='submit' value='<?=$lang['shmsg_submit']?>' accesskey='s' />
						<input type='button' name='nfr' onclick="show_hide('FastReply');" value='<?=$lang['shmsg_hidefp']?>' accesskey='h' />
						</center><br />
					</td>
					<td>
					</td>
				</tr>
			</table>
		</div>
		</form>
		</div><? //end FastReply();
}

?>
<tr>
<td>
<?php


$MSGID=pack("H*",$_REQUEST['THR_ID']);
$query="SELECT newmsg.HASH as hash,newmsg.title as title, membri.AUTORE as autore, newmsg.SUBTITLE as subtitle,"
." newmsg.BODY as body, (msghe.DATE+".GMT_TIME.") as 'date', membri.avatar AS 'avatar', membri.firma AS 'firma',"
." (membri.DATE+".GMT_TIME.") AS 'regdate', membri.msg_num AS 'msg_num',membri.title as 'gruppo',"
." membri.is_auth AS 'is_auth', membri.HASH AS 'memhash', newmsg.AUTORE AS 'real_autore', (newmsg.DATE+".GMT_TIME.") AS 'real_date',"
." newmsg.SEZ AS 'SEZ', newmsg.EDIT_OF as edit_of, msghe.reply_num as reply_num, msghe.block_date as closed"
." FROM {$SNAME}_msghe AS msghe,{$SNAME}_newmsg AS newmsg,{$SNAME}_membri AS membri"
." WHERE newmsg.EDIT_OF=msghe.HASH"
." AND newmsg.EDIT_OF='".mysql_escape_string($MSGID)."'"
." AND newmsg.visibile='1'"
." AND membri.HASH=msghe.AUTORE"
.";"
;
$riga=$db->get_row($query);

$closed=$riga->closed;


if (!$riga) {
  echo "".$lang['shmsg_msgnotfound']."\n\t</td>\n</tR>\n";
  include ("end.php");
  exit(0);
}


/*
  Preparo le variabili per la selezione delle pagine...
*/
$Num3d = $riga->reply_num;
$NumPag = intval(($Num3d-1) / $PostXPage);
$CurrPag = $_REQUEST["pag"];
if ($CurrPag=="last")
  $CurrPag = $NumPag;
if (! is_numeric($CurrPag))
  $CurrPag = 0;
if ($CurrPag < 0) $CurrPag = 0;

$db->query("replace temp(chiave,valore,TTL) values ('".$_REQUEST['THR_ID']."',$Num3d,".(time()+2592000).");");

$db->query("update {$SNAME}_msghe set read_num=read_num+1 WHERE HASH='".mysql_escape_string($MSGID)."';");

// *** convertire searcher.pm prima di attivare questa funzione ***
//echo "<a href=\"searcher.pm?MODO=2&amp;REP_OF=".urlencode($MSGID)."\">";
//echo $lang['shmsg_findnewmsg']."</a><br />";


  PageSelect(1);

  if ($CurrPag<1)
    printmsg($riga);

$query="SELECT edit.TITLE AS title, edit.BODY AS body, membri.AUTORE as autore,"
." (origi.DATE+".GMT_TIME.") as 'date', (membri.DATE+".GMT_TIME.") AS regdate, membri.avatar AS avatar, membri.firma AS firma,"
." membri.is_auth AS 'is_auth', membri.msg_num AS 'msg_num',"
." membri.title as 'gruppo', membri.HASH AS 'memhash',origi.HASH AS 'hash', edit.REP_OF AS 'repof',"
." edit.EDIT_OF AS 'edit_of', edit.HASH AS 'real_hash', edit.AUTORE AS 'real_autore', (edit.DATE+".GMT_TIME.") AS 'real_date'"
." FROM `{$SNAME}_reply` AS origi, `{$SNAME}_reply` AS edit, `{$SNAME}_membri` AS membri"
." WHERE edit.EDIT_OF=origi.HASH"
." AND membri.HASH=origi.AUTORE"
." AND edit.REP_OF='".mysql_escape_string($MSGID)."'"
." AND edit.visibile='1'"
." ORDER BY origi.DATE"
." LIMIT ".($CurrPag*$PostXPage).",$PostXPage;";
$risultato=$db->get_results($query);

  if($risultato)foreach($risultato as $riga) printmsg($riga);

  PageSelect(2);
  FastReply();
?>
</td>
</tr>
<?php
include ("end.php");
?>
