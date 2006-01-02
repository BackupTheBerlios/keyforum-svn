<?PHP
$whereiam='showmember';

include ("testa.php");
include_once("lib/bbcode_parser.php");
$lang = $std->load_lang('lang_showmember', $blanguage );


//ACQUISIZIONE DATI
$SNAME=$_ENV['sesname'];
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
$pdata = $db->get_row($query);
if(!pdata){	$std->Error("ShowMember: user does NOT exist"); } 

//statistiche messaggi
//1 - Messaggi totali board se non già calcolati
if(!$totmsg)
{
	$query = "
	SELECT    sum(THR_NUM) + sum(REPLY_NUM) as totmsg
	FROM {$SNAME}_sez WHERE 1;";
	$totmsg = $db->get_var($query);
}
//2 - max dei messaggi totali personali gruppati per sezione 
$query = "
SELECT count( sez ) AS num, {$SNAME}_sez.sez_name, {$SNAME}_newmsg.sez
FROM {$SNAME}_reply
JOIN {$SNAME}_newmsg ON {$SNAME}_newmsg.hash = {$SNAME}_reply.rep_of
JOIN {$SNAME}_sez ON {$SNAME}_sez.id = {$SNAME}_newmsg.sez
WHERE {$SNAME}_reply.autore = '$hash'
AND {$SNAME}_newmsg.visibile = '1'
AND {$SNAME}_reply.visibile = '1'
GROUP BY sez
UNION SELECT count( sez ) AS num, {$SNAME}_sez.sez_name, {$SNAME}_newmsg.sez
FROM {$SNAME}_newmsg
JOIN {$SNAME}_sez ON {$SNAME}_sez.id = {$SNAME}_newmsg.sez
WHERE {$SNAME}_newmsg.autore = '$hash'
AND {$SNAME}_newmsg.visibile = '1'
GROUP BY sez";
$result = mysql_query($query) or die(mysql_error().$query);
while($row=mysql_fetch_array($result))
{
	list($tmp,$sez_name,$sez_id) = $row;
	$sez_data[$sez_id]['num_reply'] += $tmp;
	$sez_data[$sez_id]['SEZ_NAME'] = $sez_name;
	$sez_data[$sez_id]['sez_id'] = $sez_id;
}
@rsort($sez_data);
$sez_data = $sez_data[0];


//last action 
$query = "
(SELECT {$SNAME}_reply.date+".GMT_TIME." AS date, {$SNAME}_newmsg.sez, {$SNAME}_newmsg.hash,{$SNAME}_newmsg.title
FROM {$SNAME}_reply
JOIN {$SNAME}_newmsg ON {$SNAME}_newmsg.hash = {$SNAME}_reply.rep_of
WHERE {$SNAME}_reply.autore = '$hash')
UNION 
(SELECT {$SNAME}_newmsg.date+".GMT_TIME." AS date, {$SNAME}_newmsg.sez, {$SNAME}_newmsg.hash, {$SNAME}_newmsg.title
FROM {$SNAME}_newmsg
WHERE {$SNAME}_newmsg.autore = '$hash')
ORDER BY date DESC 
Limit 1";
$last_data=$db->get_row($query);

/*
Struct User:
	NULL 	may be filled a day.............
	''   	is fileld now. Do not remove pls
*/
$user = Array(
	  'id' 		=>$_GET['MEM_ID']
	, 'nick'	=>$pdata->AUTORE
	, 'surnick'	=>''
	, 'reg_date'	=>$pdata->reg_date
	, 'group'	=>array('text' => $pdata->title,'image' =>NULL)
	, 'msg_num'	=>array('tot' => $pdata->msg_num,'daily' =>'','perc'=> '' )
	, 'msg_sez'	=>array('tot' => $sez_data['num_reply'],'perc'=> '','sez_id' => $sez_data['id'],'sez_name' => $sez_data['SEZ_NAME'])
	, 'home'	=>''
	, 'avatar'	=>$pdata->avatar
	, 'sign'	=>$pdata->firma
	, 'icq'		=>NULL
	, 'msn'		=>NULL
	, 'location'	=>''
	, 'compleanno'	=>''
	, 'online'	=>array('text' => '','image' =>'') //IMPOSSIBLE TO DO
	, 'last_action'	=>array('title' => $last_data->title,'data' => $last_data->date,'sez' => $last_data->sez, 'reply_id' =>$last_data->hash)
	);
unset($pdata);
unset($sez_data);
unset($last_data);

//PREPROCESSING DATA

//Securing data
$user['nick'] = secure_v($user['nick']);
$user['avatar'] = secure_v($user['avatar']);
$user['sign'] = secure_v($user['sign']);
$user['home'] = secure_v($user['home']);
$user['location'] = secure_v($user['location']);
$user['msg_sez']['sez_name'] = secure_v($user['msg_sez']['sez_name']);
$user['last_action']['title'] = secure_v($user['last_action']['title']);

//Default data
$user['group']['text'] = ($user['group']['text'] ? $user['group']['text'] : 'membri');
$user['avatar'] = ($user['avatar'] ? "<div><img src='{$user['avatar']}' border='0' alt='avatar' /></div>" : ''); //Default avatar?::NULL
if(!$user['last_action']['date'])
{
	$user['last_action']['date'] = 'Mai';
}

//Formatting data
$user['reg_date'] = strftime("%d/%m/%y",$user['reg_date']);
$user['last_action']['data'] = strftime("%d/%m/%y - %H:%M:%S",$user['last_action']['data']);
$user['compleanno'] = strftime("%d/%m/%y",$user['compleanno']);

//Converting data
$user['sign']  = convert($user['sign']);
list($tmp,$user['last_action']['reply_id']) = unpack("H*",$user['last_action']['reply_id']);

//statistiche messaggi
$tmp = (time() - $user['reg_date'])/(60*60*24); //86400 seconds in a day
$user['msg_num']['daily'] = number_format($user['msg_num']['tot']/$tmp,2);
if($totmsg)$user['msg_num']['perc'] = number_format($user['msg_num']['tot']*100/$totmsg,1); //to avoid division by 0
if($user['msg_num']['tot'])$user['msg_sez']['perc'] = number_format($user['msg_sez']['tot']*100/$user['msg_num']['tot'],1); //to avoid division by 0

//OUTPUT
?>
<tr><td><div class="borderwrap">
<div class='maintitle'><?=$lang['shmbr_maintitle']?> <?=$user['nick']?></div>
	<table cellspacing="1">
		<tr>
			<td width="1%" nowrap="nowrap" valign="top" class="row1">
				<div id="profilename"><?=$user['nick']?></div>
				<br />
				<?=$user['avatar']?>
				<div><?=$user['surnick']?></div>
				<br />
				<div class="postdetails">
					<?=$lang['shmbr_group']?>  <?=$user['group']['text']?><br />
  					<?=$lang['shmbr_joined']?>  <?=$user['reg_date']?>
				</div>
				<!--{WARN_LEVEL}-->
			</td>
			<td width="30%" align="center" nowrap="nowrap"  valign="top" class="row1">
				<fieldset>
					<table cellspacing="0">
						<tr>
							<td width="1%"><img src='img/profile_item.gif' border='0'  alt='Profile Item' /></td>
							<td width="99%"><a href=""> <? echo" ".$lang['shmbr_add']." "; ?> </a></td>
						</tr>
						<tr>
							<td width="1%"><img src='img/profile_item.gif' border='0'  alt='Profile Item' /></td>
							<td width="99%"><a href="search.php?find=1&amp;namesearch=<?=$user['nick']?>&amp;exactname=1&amp;forums%5B%5D=all&amp;prune=0&amp;prune_type=%3E&amp;sort_key=DATE&amp;sort_order=desc&amp;result_type=posts"><? echo "".$lang['shmbr_findmsg']."";?></a></td>
						</tr>
						<tr>
							<td width="1%"><img src='img/profile_item.gif' border='0'  alt='Profile Item' /></td>
							<td width="99%"><a href=""><? echo" ".$lang['shmbr_findthr']." "; ?></a></td>
						</tr><tr>
							<td width="1%"><img src='img/profile_item.gif' border='0'  alt='Profile Item' /></td>
							<td width="99%"><a href=""><? echo" ".$lang['shmbr_ignore']." "; ?></a></td>
						</tr>					</table>
				</fieldset>
			</td>
		</tr>
	</table>
</div>
<br />
<table cellspacing="1" width="100%">
	<tr>
		<!-- STATS -->
		<td width="50%" valign="top" style="padding-left: 0;">
			<table cellspacing="1" class="borderwrap">
				<tr>
					<td align="center" colspan="2" class="maintitle"><?=$lang['shmbr_stat']?></td>
				</tr>
				<tr>
					<td class="row2" valign="top"><b><? echo" ".$lang['shmbr_time']." "; ?></b></td>
					<td class="row1"><? echo" ".$lang['shmbr_toremove']." "; ?></td>
				</tr>
				<tr>
					<td class="row2" width="30%" valign="top"><b><?=$lang['shmbr_totalmsg']?></b></td>
					<td width="70%" class="row1">
						<b><?=$user['msg_num']['tot']?></b>
						<br />
						( <?=$user['msg_num']['daily']?> <? echo" ".$lang['shmbr_msgbyday']." "; ?> 
						/ <?=$user['msg_num']['perc']?>% <? echo" ".$lang['shmbr_ofall']." "; ?> )
					</td>
				</tr>
<?php if($user['last_action']['title']){?>
				<tr>
					<td class="row2" valign="top"><b><?=" ".$lang['shmbr_activity']." "; ?></b></td>
					<td class="row1">
						<a href="sezioni.php?SEZID=<?=$user['msg_sez']['sez_id']?>">
							<b><?=$user['msg_sez']['sez_name']?></b>
						</a><br />
						( <?=$user['msg_sez']['tot']?> <? echo" ".$lang['shmbr_boardmsg']." "; ?> 
						/ <?=$user['msg_sez']['perc']?>% <?=" ".$lang['shmbr_activemsg']." "; ?> )</td>
				</tr>
				<tr>
					<td class="row2" valign="top"><b><?=" ".$lang['shmbr_lastactive']." "; ?></b></td>
					<td class="row1">
						<?=$user['last_action']['data']?> in 
						<a href='showmsg.php?SEZID=<?=$user['last_action']['sez']?>=&THR_ID=<?=$user['last_action']['reply_id']?>&pag=last#end_page'><?=$user['last_action']['title']?></a> 
					</td>
				</tr>
<?php } //END SEZ DATA?>
				<tr>
					<td class="row2" valign="top"><b><? echo" ".$lang['shmbr_status']." "; ?></b></td>
					<td class="row1">
						<!--<img src='<?=$user['online']['image']?>' border='0'  alt='<?=$user['online']['text']?>' />-->		
						(<?=$user['online']['text']?>)<? echo" ".$lang['shmbr_toremove']." "; ?></td>
				</tr>
			</table>
		</td>
		<!-- Communication -->
		<td width="50%" valign="top" style="padding-right: 0;">
			<table cellspacing="1" class="borderwrap">
				<tr>
					<td align="center" colspan="2" class="maintitle"><?=$lang['shmbr_details']?></td>
				</tr>
				<tr>
					<td width="1%" class="row1">
						<img src='img/profile_aim.gif' border='0' alt='AIM' />
					</td>
					<td width="99%" class="row2"><i><? echo" ".$lang['shmbr_noinfo']." "; ?></i></td>
				</tr>
				<tr>
					<td width="1%" class="row1">
						<img src='img/profile_yahoo.gif' border='0'  alt='Yahoo' />
					</td>
					<td width="99%" class="row2"><i><? echo" ".$lang['shmbr_noinfo']." "; ?></i></td>
				</tr>
				<tr>
					<td width="1%" class="row1">
						<img src='img/profile_icq.gif' border='0'  alt='ICQ' />
					</td>
					<td width="99%" class="row2"><i><? echo" ".$lang['shmbr_noinfo']." "; ?></i></td>
				</tr>
				<tr>
					<td width="1%" class="row1">
						<img src='img/profile_msn.gif' border='0'  alt='MSN' />
					</td>
					<td width="99%" class="row2"><i><? echo" ".$lang['shmbr_noinfo']." "; ?></i></td>
				</tr>
				<tr>
					<td width="1%" class="row1">
						<img src='img/f_norm_no.gif' border='0'  alt='Contact' /></td>
					<td width="99%" class="row2">
						<a href=""><?=$lang['shmbr_sendpvt']?></a></td>
				</tr>
				<tr>
					<td width="1%" class="row1">
						<img src='img/f_norm_no.gif' border='0'  alt='Contact' />
					</td>
					<td width="99%" class="row2"><i><? echo" ".$lang['shmbr_pvt']." "; ?></i></td>
				</tr>
			</table>
		</td>
		<!-- END CONTENT ROW 1 -->
		<!-- information -->
	</tr>
	<tr>
		<!-- Varie -->
		<td width="50%" valign="top" style="padding-left: 0;">
			<table cellspacing="1" class="borderwrap" width='100%'>
				<tr>
					<td align="center" colspan="2" class="maintitle"><?=$lang['shmbr_miscinfo']?></td>
				</tr>
				<tr>
					<td class="row2" width="30%" valign="top"><b><?=$lang['shmbr_home']?></b></td>
					<td width="70%" class="row1"><i><? echo" ".$lang['shmbr_noinfo']." "; ?></i></td>
				</tr>
				<tr>
					<td class="row2" valign="top"><b><?=" ".$lang['shmbr_birthday']." "; ?></b></td>
					<td class="row1"><i><? echo" ".$lang['shmbr_noinfo']." "; ?></i></td>
				</tr>
				<tr>
					<td class="row2" valign="top"><b><?=" ".$lang['shmbr_location']." "; ?></b></td>
					<td class="row1"><i><? echo" ".$lang['shmbr_noinfo']." "; ?></i></td>
				</tr>
				<tr>
					<td class="row2" valign="top"><b><?=" ".$lang['shmbr_interests']." "; ?></b></td>
					<td class="row1"><i><? echo" ".$lang['shmbr_noinfo']." "; ?></i></td>
				</tr>
			</table>
		</td>
		<!-- Profile -->
		<td width="50%" valign="top" style="padding-right: 0;">
			<table cellspacing="1" class="borderwrap" width='100%'>
				<tr>
					<td align="center" colspan="2" class="maintitle">
				   		<?=$lang['shmbr_otherinfo']?>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center" class="row2">
						<i><? echo" ".$lang['shmbr_noinfo']." "; ?></i>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<br />
<div class="borderwrap">
<table cellspacing="1" width='100%'>
	<tr>
		<td class="maintitle"><? echo" ".$lang['shmbr_sign']." "; ?></td>
	</tr>
	<tr>
		<td class="row2">
			<div class="signature"><?=$user['sign']?></div>
		</td>
	</tr>
</table>
</div></td></tr>
<?php include("end.php");?>