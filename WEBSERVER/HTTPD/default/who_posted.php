<?php
$whereiam = 'who_posted.php';
include("lib/lib.php");

$lang = $std->load_lang('lang_who_posted', $blanguage );

$TID = $_GET['TID'];
$SID = $_GET['SID'];

$THASH = mysql_real_escape_string(pack('H*',$TID));
//Query;
$thr_name = $db->get_var("SELECT TITLE FROM {$SNAME}_newmsg where EDIT_OF = '$THASH' AND visibile = '1'");
$query = "
	SELECT count({$SNAME}_membri.AUTORE) as NUM_REP,{$SNAME}_membri.AUTORE,{$SNAME}_membri.HASH
	FROM {$SNAME}_reply
		JOIN {$SNAME}_membri on {$SNAME}_membri.hash = {$SNAME}_reply.autore
	WHERE {$SNAME}_reply.rep_of='$THASH' 
		AND {$SNAME}_reply.visibile='1'
	GROUP BY {$SNAME}_membri.AUTORE
	ORDER BY NUM_REP DESC, {$SNAME}_reply.date DESC
		";
$result = $db->get_results($query);
if($result)foreach($result as $utente)
{
	list($asd,$userid) = unpack('H*',$utente->HASH);
	$users[] = Array(
		 'nick' => secure_v($utente->AUTORE)
		,'id' 	=> $userid
		,'num_reply' => (int)$utente->NUM_REP
		);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
<HEAD>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-1">

<title>Keyfo Forum </title>
<link type="text/css" rel="stylesheet" href="style_page.css">
<link rel="shortcut icon" href="favicon.ico">
</HEAD>
<body>
	<script language="javascript" type="text/javascript">
<!--
	function ReimpostaDimensioni(num)
	{
		if(num == 0) num = num +1;
		height = (num*15)+175;
		window.resizeTo(350,height);
	}
	function bog_off(){
		var tid = "<?=$_GET['TID']?>";
		var sid = "<?=$_GET['SID']?>";
		opener.location= "showmsg.php?SEZID=" + sid + "&THR_ID=" + tid;
		self.close();
	}
	window.setTimeout("ReimpostaDimensioni('<?=count($users)?>')", 20);
-->
</script>
<div class="borderwrap">
	<div class="maintitle" align="center"><?=$lang['who_reply']?>: <?=secure_v($thr_name)?></div>
	<table cellspacing="1" width="100%">
		<tr>
			<th width="70%" valign="middle"><?=$lang['who_user']?></th>
			<th width="30%" align="center" valign="middle"><?=$lang['who_posts']?></th>
		</tr>
<?php if($users)foreach($users as $user){ 				//INIZIO CICLO 	?> 
		<tr>
			<td class="row1" valign="middle">
				<a href="showmember.php?MEM_ID=<?=$user[id]?>" target="_blank"><?=$user['nick']?></a>
			</td>
			<td align="center" class="row1" valign="middle">
				<?=$user[num_reply]?>
			</td>
		</tr>
<? }											//Fine Ciclo	
else echo "<tr><td class='row1' valign='middle' colspan='2' align='center'>{$lang[who_nousers]}
			</td></tr>"; ?> 
		<tr>
			<td class="formbuttonrow" colspan="2">
				<a href="javascript:bog_off();"><?=$lang['who_close']?></a>
			</td>
		</tr>
		<tr>
			<td class="catend" colspan="2"><!-- no content --></td>
		</tr>
	</table>
	</div>
</body>
</html>