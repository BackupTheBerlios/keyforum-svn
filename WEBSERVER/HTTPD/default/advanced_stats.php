<?php
require_once('lib/lib.php');

switch($advanced_stats_mode)
{
	case 'logged_since':
		if(!$_SESSION[$SNAME]['logged_since']) break;
		echo "<br />Loggato da ".$std->postdate($_SESSION[$SNAME]['logged_since']);
	break;
	case 'most_active_day':
		$timestart = time()-3600*24-GMT_TIME;
		$timeend = time();
		$query = "
		SELECT {$SNAME}_membri.hash , {$SNAME}_membri.autore as nick, count(1) as rep_num
		from {$SNAME}_membri
		join {$SNAME}_reply on {$SNAME}_membri.hash = {$SNAME}_reply.autore 
		where {$SNAME}_reply.date between $timestart and $timeend 
			AND {$SNAME}_reply.visibile='1'
		group by {$SNAME}_membri.hash
		order by rep_num desc
		limit 7";
		$result = $db->get_results($query);
		if($result)foreach($result as $user)
		{
			list($asd,$id) = unpack('H*',$user->hash);
			$nick = secure_v($user->nick);
			$users[$id] = $nick;
		}
		if($users)
		{
			$result ='<br />Utenti più attivi nelle ultime 24 ore: ';
			foreach($users as  $id=>$nick )
			{
				$result .= "<a href='showmember.php?MEM_ID=$id'>$nick</a>, ";
			}
		$result = substr($result,0,-2);
		echo $result;
		}
	break;
	
	case 'today_birthday':
		$timestart = mktime(0,0,0,date('M'),date('d'),date('Y'));
		$timeend = mktime(23,59,59,date('M'),date('d'),date('Y'));
		$query = "
		SELECT {$SNAME}_membri.hash , {$SNAME}_membri.autore as nick
		from {$SNAME}_membri
		where {$SNAME}_membri.nascita between $timestart and $timeend 
		order by nascita, nick asc
		limit 10";
		$result = $db->get_results($query);
		if($result)foreach($result as $user)
		{
			list($asd,$id) = unpack('H*',$user->hash);
			$nick = secure_v($user->nick);
			$users[$id] = $nick;
		}
		if($users)
		{
			$result ='<br />Auguri a: ';
			foreach($users as  $id=>$nick )
			{
				$result .= "<a href='showmember.php?MEM_ID=$id'>$nick</a>, ";
			}
		$result = substr($result,0,-2);
		echo $result;
		}
	break;
	
	case 'last_user':
		$query = "
		SELECT {$SNAME}_membri.hash , {$SNAME}_membri.autore as nick
		from {$SNAME}_membri
		where {$SNAME}_membri.is_auth = '1'
		order by date desc
		limit 1";
		$result = $db->get_results($query);
		if($result)foreach($result as $user)
		{
			list($asd,$id) = unpack('H*',$user->hash);
			$nick = secure_v($user->nick);
			$users[$id] = $nick;
		}
		if($users)
		{
			$result ='<br />L\'ultimo arrivato: ';
			foreach($users as  $id=>$nick )
			{
				$result .= "<a href='showmember.php?MEM_ID=$id'>$nick</a>, ";
			}
		$result = substr($result,0,-2);
		echo $result;
		}
	default:
	break;
}
unset($users,$result);
?>

