<?PHP
require_once('lib/lib.php');
include_once("lib/bbcode_parser.php");
$SNAME=$_ENV['sesname'];

//ACQUISIZIONE DATI
$id = $_GET['MEM_ID'];
$hash = addslashes(pack('H*',$id));

//dati personali
$query = "
Select    AUTORE
	, (DATE+".GMT_TIME.") AS 'reg_date'
	, firma
	, avatar
	, title
	, msg_num
from {$SNAME}_membri
WHERE HASH = '$hash' LIMIT 1";//not used AUTH, TYPE, SIGN ,is_auth ,ban, present, edit_firma, edit_adminset
$result = mysql_query($query) or die(mysql_error().$query);
$pdata = mysql_fetch_array($result);

//statistiche messaggi
//1 - Messaggi totali board se non gi� calcolati
if(!$totmsg)
{
	$query = "
	SELECT    sum(THR_NUM) + sum(REPLY_NUM) as totmsg
	FROM {$SNAME}_sez WHERE 1;";
	list($totmsg) = mysql_fetch_row(mysql_query($query));
}
//2 - max dei messaggi totali personali gruppati per sezione //TO DO _ Da correggere :) ad alcuni va e per altri no, per� la query � stata completamente inventata :) non ho la minima idea di quello che faccia :) 
$query = "
SELECT    count(ID) as num_reply
	, {$SNAME}_sez.id
	, SEZ_NAME
FROM {$SNAME}_sez 
join {$SNAME}_newmsg on {$SNAME}_newmsg.sez 	= {$SNAME}_sez.id
left outer join {$SNAME}_reply on {$SNAME}_reply.rep_of 	= {$SNAME}_newmsg.hash 
where {$SNAME}_newmsg.autore = '$hash' or {$SNAME}_reply.autore = '$hash'
group by sez_name
order by num_reply desc
LIMIT 1";
$result = mysql_query($query) or die(mysql_error().$query);
$sez_data=mysql_fetch_array($result);

//last action //TO DO _ Da correggere :) solo ultimo reply e non anche ultimo 3d aperto
$query = "
SELECT {$SNAME}_newmsg.title, {$SNAME}_reply.date, {$SNAME}_newmsg.sez, {$SNAME}_newmsg.hash FROM {$SNAME}_reply
join {$SNAME}_newmsg on {$SNAME}_reply.rep_of = {$SNAME}_newmsg. hash
where {$SNAME}_reply.autore='$hash' or {$SNAME}_newmsg.autore='$hash'
ORDER BY {$SNAME}_reply.date desc
Limit 1";
$result = mysql_query($query) or die(mysql_error().$query);
$last_data=mysql_fetch_array($result);
/*
Struct User:
	NULL 	may be filled a day.............
	''   	is fileld now. Do not remove pls
*/
$user = Array(
	  'id' 		=>$_GET['MEM_ID']
	, 'nick'	=>$pdata['AUTORE']
	, 'surnick'	=>NULL
	, 'reg_date'	=>$pdata['reg_date']
	, 'group'	=>array('text' => $pdata['title'],'image' =>NULL)
	, 'msg_num'	=>array('tot' => $pdata['msg_num'],'daily' =>'','perc'=> '' )
	, 'msg_sez'	=>array('tot' => $sez_data['num_reply'],'perc'=> '','sez_id' => $sez_data['id'],'sez_name' => $sez_data['SEZ_NAME'])
	, 'home'	=>''
	, 'avatar'	=>$pdata['avatar']
	, 'sign'	=>$pdata['firma']
	, 'icq'		=>NULL
	, 'msn'		=>NULL
	, 'location'	=>NULL
	, 'compleanno'	=>NULL
	, 'online'	=>array('text' => '','image' =>'') //IMPOSSIBLE TO DO
	, 'last_action'	=>array('title' => $last_data['title'],'data' => $last_data['date'],'sez' => $last_data['sez'], 'reply_id' =>$last_data['hash'])
	);
unset($pdata);
unset($sez_data);
unset($last_data);

//PREPROCESSING DATA
//Default data
$user['group']['text'] = ($user['group']['text'] ? $user['group']['text'] : 'membri');
$user['avatar'] = ($user['avatar'] ? "<div><img src='{$user['avatar']}' border='0' alt='avatar' /></div>" : ''); //Default avatar?::NULL

//statistiche messaggi
$tmp = (time() - $user['reg_date'])/(60*60*24); //86400 seconds in a day
$user['msg_num']['daily'] = number_format($user['msg_num']['tot']/$tmp,2);
$user['msg_num']['perc'] = number_format($user['msg_num']['tot']*100/$totmsg,1);
$user['msg_sez']['perc'] = number_format($user['msg_sez']['tot']*100/$user['msg_num']['tot'],1);


//Formatting data
$user['reg_date'] = strftime("%d/%m/%y",$user['reg_date']);
//$user['last_action']['data'] = strftime("%d/%m/%y",$user['last_action']['data']);
$user['last_action']['data'] = strftime("%d/%m/%y",$user['last_action']['data']);
//Converting data
$user['sign']  = convert($user['sign']);
list($tmp,$user['last_action']['reply_id']) = unpack("H*",$user['last_action']['reply_id']);



//Preparing output
$title = 'Visualizzazione Profilo Utente :: ' . $user['nick'];
//OUTPUT
// v. 0.6
include ("testa.php");
// carico la lingua per la gestip
$lang = $std->load_lang('lang_showmsg', $blanguage );
//$lang = $std->load_lang('lang_showmember', $blanguage );
?>

<div class="borderwrap">
	<div class="maintitle">Visualizzazione Profilo Utente: <?=$user['nick']?></div>
	<table cellspacing="1">
		<tr>
			<td width="1%" nowrap="nowrap" valign="top" class="row1">
				<div id="profilename"><?=$user['nick']?></div>
				<br />
				<?=$user['avatar']?>
				<div><?=$user['surnick']?></div>
				<div><img src="<?=$user['group']['image']?>" alt="<?=$user['group']['text']?>" /></div>
				<br />
				<div class="postdetails">
					<?=$lang['shmsg_group']?>:: <?=$user['group']['text']?><br />
  					<?=$lang['shmsg_joined']?>::<?=$user['reg_date']?>
				</div>
				<!--{WARN_LEVEL}-->
			</td>
			<td width="30%" align="center" nowrap="nowrap" valign="top" class="row1">
				<fieldset>
					<legend><b></b></legend>
					<table cellspacing="0">
						<tr>
							<td width="1%"><img src='style_images/1/profile_item.gif' border='0'  alt='Profile Item' /></td>
							<td width="99%"><a href="">Aggiungi alla rubrica</a></td>
						</tr>
						<tr>
							<td width="1%"><img src='style_images/1/profile_item.gif' border='0'  alt='Profile Item' /></td>
							<td width="99%"><a href="search.php?find=1&amp;namesearch=<?=$user['nick']?>&amp;exactname=1&amp;forums%5B%5D=all&amp;prune=0&amp;prune_type=%3E&amp;sort_key=DATE&amp;sort_order=desc&amp;result_type=posts">Trova tutti i messaggi di questo utente</a></td>
						</tr>
						<tr>
							<td width="1%"><img src='style_images/1/profile_item.gif' border='0'  alt='Profile Item' /></td>
							<td width="99%"><a href="">Trova le discussioni di questo utente</a></td>
						</tr><tr>
							<td width="1%"><img src='style_images/1/profile_item.gif' border='0'  alt='Profile Item' /></td>
							<td width="99%"><a href="">Ignora Utente</a></td>
						</tr>					</table>
				</fieldset>
			</td>
			<td width="1%" align="right" class="row1"></td>
		</tr>
	</table>
</div>
<br />
<table cellspacing="1">
	<tr>
		<!-- STATS -->
		<td width="50%" valign="top" style="padding-left: 0;">
			<table cellspacing="1" class="borderwrap">
				<tr>
					<td align="center" colspan="2" class="maintitle">Statistiche Attivit&#224;</td>
				</tr>
				<tr>
					<td class="row2" valign="top"><b>Ora locale dell'utente</b></td>
					<td class="row1">Da togliere</td>
				</tr>
				<tr>
					<td class="row2" width="30%" valign="top"><b><?=$lang['shmsg_messages']?></b></td>
					<td width="70%" class="row1"><b><?=$user['msg_num']['tot']?></b>
					<br />( <?=$user['msg_num']['daily']?> Messaggi per giorno: / <?=$user['msg_num']['perc']?>% di tutti i messaggi del forum )
					</td>
				</tr>
				<tr>
					<td class="row2" valign="top"><b>Maggiormente attivo in:</b></td>
					<td class="row1">
	<a href="sezioni.php?SEZID=<?=$user['msg_sez']['sez_id']?>">
		<b><?=$user['msg_sez']['sez_name']?></b>
	</a><br />
	( <?=$user['msg_sez']['tot']?> messaggi in questo forum / <?=$user['msg_sez']['perc']?>% dei messaggi attivi di questo utente )</td>
				</tr>
				<tr>
					<td class="row2" valign="top"><b>Ultima Azione</b></td>
					<td class="row1"><?=$user['last_action']['data']?> in <a href='showmsg.php?SEZID=<?=$user['last_action']['sez']?>=&THR_ID=<?=$user['last_action']['reply_id']?>&pag=last#end_page'><?=$user['last_action']['title']?></a> </td>
				</tr>
				<tr>
					<td class="row2" valign="top"><b>Stato</b></td>
					<td class="row1"><img src='<?=$user['online']['image']?>' border='0'  alt='<?=$user['online']['text']?>' />(<?=$user['online']['text']?>)Da togliere</td>
				</tr>
			</table>
		</td>
		<!-- Communication -->
		<td width="50%" valign="top" style="padding-right: 0;">
			<table cellspacing="1" class="borderwrap">
				<tr>
					<td align="center" colspan="2" class="maintitle">Dettagli per Contatto</td>
				</tr>
				<tr>
					<td width="1%" class="row1"><img src='style_images/1/profile_aim.gif' border='0'  alt='AIM' /></td>
					<td width="99%" class="row2"><i>Nessuna informazione</i></td>
				</tr>
				<tr>
					<td width="1%" class="row1"><img src='style_images/1/profile_yahoo.gif' border='0'  alt='Yahoo' /></td>
					<td width="99%" class="row2"><i>Nessuna informazione</i></td>
				</tr>
				<tr>
					<td width="1%" class="row1"><img src='style_images/1/profile_icq.gif' border='0'  alt='ICQ' /></td>
					<td width="99%" class="row2"><i>Nessuna informazione</i></td>
				</tr>
				<tr>
					<td width="1%" class="row1"><img src='style_images/1/profile_msn.gif' border='0'  alt='MSN' /></td>
					<td width="99%" class="row2"><i>Nessuna informazione</i></td>
				</tr>
				<tr>
					<td width="1%" class="row1"><img src='style_images/1/icon_msg_nonew.gif' border='0'  alt='Contact' /></td>
					<td width="99%" class="row2"><a href="">Manda un Messaggio Privato</a></td>
				</tr>
				<tr>
					<td width="1%" class="row1"><img src='style_images/1/icon_msg_nonew.gif' border='0'  alt='Contact' /></td>
					<td width="99%" class="row2"><i>Privata</i></td>
				</tr>
			</table>
		</td>
		<!-- END CONTENT ROW 1 -->
		<!-- information -->
	</tr>
	<tr>
		<td width="50%" valign="top" style="padding-left: 0;">
			<table cellspacing="1" class="borderwrap" width='100%'>
				<tr>
					<td align="center" colspan="2" class="maintitle">Informazioni Varie</td>
				</tr>
				<tr>
					<td class="row2" width="30%" valign="top"><b>Homepage</b></td>
					<td width="70%" class="row1"><i>Nessuna informazione</i></td>
				</tr>
				<tr>
					<td class="row2" valign="top"><b>Compleanno</b></td>
					<td class="row1"><i>Nessuna informazione</i></td>
				</tr>
				<tr>
					<td class="row2" valign="top"><b>Provenienza:</b></td>
					<td class="row1"><i>Nessuna informazione</i></td>
				</tr>
				<tr>
					<td class="row2" valign="top"><b>Interessi</b></td>
					<td class="row1"><i>Nessuna informazione</i></td>
				</tr>
			</table>
		</td>
		<!-- Profile -->
		<td width="50%" valign="top" style="padding-right: 0;">
			<table cellspacing="1" class="borderwrap" width='100%'>
				<tr>
					<td align="center" colspan="2" class="maintitle">Informazioni aggiuntive</td>
				</tr>
				<tr>
	<td colspan="2" align="center" class="row2"><i>Nessuna informazione</i></td>
</tr>
			</table>
		</td>
	</tr>
</table><br />
<div class="borderwrap">
<table cellspacing="1" width='100%'>
	<tr>
		<td class="maintitle">Firma</td>
	</tr>
	<tr>
		<td class="row2">
			<div class="signature"><?=$user['sign']?></div>
		</td>
	</tr>
</table>
</div>
<?php include("end.php");?>