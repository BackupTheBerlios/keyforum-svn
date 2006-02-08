<?

include "testa.php";

$forumlist=$std->ForumList();

echo <<<EOF


<form action='adminsez.php' >
				 
<input type='hidden' name='fid' value='7'>

<div class='tableborder'>
<div class='maintitle' align=center>
Basic Settings for {forum_name}
</div>
<div class='tableborder'>
<table width='100%' cellspacing='0' cellpadding='5' align='center' border='0'><tr>
</tr>

<tr>
<td class='row1'  width='40%'  valign='middle'><b>Forum Name</b></td>
<td class='row2'  width='60%'  valign='middle'><input type='text' name='name' value='{forum_name}' size='30' class='textinput'></td>
</tr>

<tr>
<td class='row1'  width='40%'  valign='middle'><b>Forum Description</b></td>
<td class='row2'  width='60%'  valign='middle'><textarea name='description' cols='60' rows='5' wrap='soft'   class='multitext'></textarea></td>
</tr>

<tr>
<td class='row1'  width='40%'  valign='middle'><b>Add to which parent?</b><br></td>
<td class='row2'  width='60%'  valign='middle'>
<select name='figlio'  class='dropdown'>
<option value='0'>Make Root (Category)</option>
$forumlist
</select>
</td>
</tr>

<tr>
<td class='row1'  width='40%'  valign='middle'><b>Forum State</b></td>
<td class='row2'  width='60%'  valign='middle'><select name='status'  class='dropdown'>
<option value='1' selected>Active</option>
<option value='0'>Hidden</option>
</select>
</td>
</tr>

<tr>
<td class='row1'  width='40%'  valign='middle'><b>Aliases (comma separated)</b></td>
<td class='row2'  width='60%'  valign='middle'><input type='text' name='aliases' value='10,20,21' size='30' class='textinput'></td>
</tr>

<tr>
<td class='row1'  width='40%'  valign='middle'>
<b>Act as a normal forum not as a category?</b></td>
<td class='row2'  width='60%'  valign='middle'>
Yes &nbsp; <input type='radio' name='sub_can_post' value='1'  checked id='green'>&nbsp;&nbsp;&nbsp;<input type='radio' name='sub_can_post' value='0'  id='red'> &nbsp; No</td>
</tr>
<tr><td colspan=2><div class='tableborder'><div align='center' class='pformstrip'><input type='submit' value='Edit this forum' class='button' id='button' accesskey='s'></div></div>
</form><br /></td></tr>
</table></div>
<br />








EOF;







include "end.php";

?>