<!-- ricerca_form.php v0.01 -->
<form method="post">
<input type="hidden" name="find" value="1">

<div class="tableborder">
 <table cellpadding='4' cellspacing='0' border='0' width='100%'>
  <tr>
   <td colspan='2' class="maintitle"  align='center'><? echo $lang['word_search']; ?></td>
  </tr>
  <tr>
   <td class='pformstrip' width='50%'><? echo $lang['search_word']; ?></td>
   <td class='pformstrip' width='50%'><? echo $lang['search_user']; ?></td>
  </tr>
  <tr>
   <td class='row1' valign='top'>
    <input type='text' maxlength='100' size='40' name='keywords' id="keywords" class='forminput' />
    <br /><br />
    <label for="keywords"><? echo $lang['search_string']; ?></label>
   </td>
   <td class='row1' valign='top'>
    <table width='100%' cellpadding='4' cellspacing='0' border='0' align='center'>
     <tr>
      <td><input type='text' maxlength='100' size='50' name='namesearch' class='forminput' /></td>
     </tr>
     <tr>
      <td width='40%'><input type='checkbox' name='exactname' id='matchexact' value='1' class="checkbox" /><label for="matchexact"><? echo $lang['exact_user']; ?></label></td>
     </tr>
    </table>
   </td>
  </tr>
 </table>
</div>
<br />

<div class="tableborder">
 <table cellpadding='4' cellspacing='0' border='0' width='100%'>         
  <tr>
   <td colspan='2' class="maintitle"  align='center'><? echo $lang['search_option']; ?></td>
  </tr>
  <tr>
   <td class='pformstrip' width='40%' valign='middle'><? echo $lang['search_forum']; ?></td>
   <td class='pformstrip' width='60%' valign='middle'><? echo $lang['search_refines']; ?></td>
  </tr>
  <tr>
   <td class='row1' valign='middle'>
    <select name='forums[]' class='forminput' size='10' multiple='multiple'>
<?
  if ($_REQUEST["SEZID"])
    echo "<option value='all'>&raquo; ".$lang['search_all']."</option>";
  else
    echo "<option value='all' SELECTED>&raquo; ".$lang['search_all']."</option>";
  $query = "select id,sez_name from {$SNAME}_sez";
  $risultato=mysql_query($query) or Muori ("Query non valida: " . mysql_error());
  while ($riga = mysql_fetch_assoc($risultato)) {
    if ($riga["id"]==$_REQUEST["SEZID"])
      echo "<option selected value=\"".$riga["id"]."\">".$riga["sez_name"]."</option>";
    else
      echo "<option value=\"".$riga["id"]."\">".$riga["sez_name"]."</option>";
  }  
?>
    </select>
    <br />
<!--    <input type='checkbox' name='searchsubs' value='1' id="searchsubs" checked="checked" />&nbsp;<label for="searchsubs">Search in child forums if sub category is chosen?</label> -->
   </td>
   <td class='row1' valign='top'>
    <table cellspacing='4' cellpadding='0' width='100%' align='center' border='0'>
     <tr>
      <td valign='top'>
       <fieldset class="search">
       <legend><strong><? echo $lang['search_from']; ?></strong></legend>
       <select name='prune' class='forminput'>
	 <option value='1'><? echo $lang['search_today']; ?></option>
	 <option value='7'>7 <? echo $lang['search_days']; ?></option>
	 <option value='30'>30 <? echo $lang['search_days']; ?></option>
	 <option value='60'>60 <? echo $lang['search_days']; ?></option>
	 <option value='90'>90 <? echo $lang['search_days']; ?></option>
	 <option value='180'>180 <? echo $lang['search_days']; ?></option>
	 <option value='365'>365 <? echo $lang['search_days']; ?></option>
	 <option value='0' selected="selected"><? echo $lang['search_always']; ?></option>
       </select>
       <br />
	<input type='radio' name='prune_type' id="prune_older" value='<' class='radiobutton' />&nbsp;<label for="prune_older"><? echo $lang['search_old']; ?></label>
	<br />
	<input type='radio' name='prune_type' id="prune_newer" value='>' class='radiobutton' checked="checked" />&nbsp;<label for="prune_newer"><? echo $lang['search_new']; ?></label>
       </fieldset>
      </td>
      <td valign='top'>
		  <fieldset class="search">
		     <legend><strong><? echo $lang['search_sort']; ?></strong></legend>
			 <select name='sort_key' class='forminput'>
			 <option value='DATE'><? echo $lang['search_s_date']; ?></option>
			 <option value='reply_num'><? echo $lang['search_s_reply']; ?></option>
			 <option value='NICK'><? echo $lang['search_s_auth']; ?></option>
			 <option value='SEZID'><? echo $lang['search_s_forum']; ?></option>
			 </select>
			 <br /><input type='radio' name='sort_order' id="sort_desc" class="radiobutton" value='desc' checked="checked" /><label for="sort_desc"><? echo $lang['search_desc']; ?></label>
			 <br /><input type='radio' name='sort_order' id="sort_asc" class="radiobutton" value='asc' /><label for="sort_asc"><? echo $lang['search_asc']; ?></label>
		  </fieldset>
		</td>
		</tr>
		<tr>
		 <td nowrap="nowrap">
		   <fieldset class="search">
		     <legend><strong><? echo $lang['search_in']; ?></strong></legend>
			 <input type='radio' name='search_in' class="radiobutton" id="search_in_posts" value=1 checked="checked" /><label for="search_in_posts"><? echo $lang['search_full']; ?></label>
			 <br />
			 <input type='radio' name='search_in' class="radiobutton" id="search_in_titles" value=2 /><label for="search_in_titles"><? echo $lang['search_title']; ?></label>
		   </fieldset>
		 </td>
		 <td>
		    <fieldset class="search">
		     <legend><strong><? echo $lang['search_result']; ?></strong></legend>
		     <input type='radio' name='result_type' class="radiobutton" value='topics' id="result_topics" checked="checked" /><label for="result_topics"><? echo $lang['search_topic']; ?></label>
		     <br />
		     <input type='radio' name='result_type' class="radiobutton" value='posts' id="result_posts" /><label for="result_posts"><? echo $lang['search_post']; ?></label>
		   </fieldset>
		 </td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td class='pformstrip' colspan='2' align='center'><input type='submit' value='<? echo $lang['search']; ?>' class='forminput' /></td>
</tr>
</table>
</div>
</form></div>