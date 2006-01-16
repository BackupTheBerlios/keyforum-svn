<table>
<tr><td>
<input type="button" class="button" accesskey="b" name="addbbcode0" value=" B " style="font-weight:bold; width: 30px" onClick="bbstyle(0)" onMouseOver="helpline('b')" onMouseOut="helpline('x')" />
<input type="button" class="button" accesskey="i" name="addbbcode2" value=" i " style="font-style:italic; width: 30px" onClick="bbstyle(2)" onMouseOver="helpline('i')" onMouseOut="helpline('x')" />
<input type="button" class="button" accesskey="u" name="addbbcode4" value="&nbsp;u&nbsp;" style="text-decoration: underline; width: 30px" onClick="bbstyle(4)" onMouseOver="helpline('u')" onMouseOut="helpline('x')" />
<input type="button" class="button" accesskey="q" name="addbbcode6" value="Quote" style="width: 50px" onClick="bbstyle(6)" onMouseOver="helpline('q')" onMouseOut="helpline('x')" />
<input type="button" class="button" accesskey="n" name="addbbcode10" value="N Quote" style="width: 60px" onClick="bbstyle(10)" onMouseOver="helpline('n')" onMouseOut="helpline('x')" />
<input type="button" class="button" accesskey="c" name="addbbcode8" value="Code" style="width: 40px" onClick="bbstyle(8)" onMouseOver="helpline('c')" onMouseOut="helpline('x')" />
                   
<input name="btnLink" type="button" accesskey="w" class="button" id="btnLink" value="http://" onMouseOver="helpline('w')" onMouseOut="helpline('x')" onClick="btnInsertLink();">
<input name="btnEmail" type="button" class="button" accesskey="e" id="btnEmail" value=" @ " onMouseOver="helpline('e')" onMouseOut="helpline('x')"   onClick="btnInsertEmail();">
<input name="btnImage" type="button" accesskey="p" class="button"  id="btnImage" value="IMG" onMouseOver="helpline('p')" onMouseOut="helpline('x')"  onClick="btnInsertImage();">
<input name="btnThumbImage" type="button" accesskey="t" class="button"  id="btnThumbImage" value="TMB" onMouseOver="helpline('t')" onMouseOut="helpline('x')" onClick="btnInsertThumbImage();">
<br>
</td></tr>
<tr><td>
&nbsp;Colore:
<select name="addbbcode18" onChange="bbfontstyle('[color=' + this.form.addbbcode18.options[this.form.addbbcode18.selectedIndex].value + ']', '[/color]');this.selectedIndex=0;" onMouseOver="helpline('s')" onMouseOut="helpline('x')">
  <option style="color:black; background-color: #FAFAFA" value="#444444" >Default</option>
  <option style="color:darkred; background-color: #FAFAFA" value="darkred" >Dark Red</option>
  <option style="color:red; background-color: #FAFAFA" value="red" >Red</option>
  <option style="color:orange; background-color: #FAFAFA" value="orange" >Orange</option>
  <option style="color:brown; background-color: #FAFAFA" value="brown" >Brown</option>
  <option style="color:yellow; background-color: #FAFAFA" value="yellow" >Yellow</option>
  <option style="color:green; background-color: #FAFAFA" value="green" >Green</option>
  <option style="color:olive; background-color: #FAFAFA" value="olive" >Olive</option>
  <option style="color:cyan; background-color: #FAFAFA" value="cyan" >Cyan</option>
  <option style="color:blue; background-color: #FAFAFA" value="blue" >Blue</option>
  <option style="color:darkblue; background-color: #FAFAFA" value="darkblue" >Dark Blue</option>
  <option style="color:indigo; background-color: #FAFAFA" value="indigo" >Indigo</option>
  <option style="color:violet; background-color: #FAFAFA" value="violet" >Violet</option>
  <option style="color:white; background-color: #FAFAFA" value="white" >White</option>
  <option style="color:black; background-color: #FAFAFA" value="black" >Black</option>
</select>
&nbsp;&nbsp;&nbsp;
Dimensione:<select name="addbbcode20" onChange="bbfontstyle('[size=' + this.form.addbbcode20.options[this.form.addbbcode20.selectedIndex].value + ']', '[/size]')" onMouseOver="helpline('f')" onMouseOut="helpline('x')">
  <option value="1" >Piccolo</option>
  <option value="7" >Medio</option>
  <option value="14" >Grande</option>
  <option value="28" >Enorme</option>
</select>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input name="btnCloseAll" type="button" accesskey="a" class="button"  id="btnCloseAll" value="CHIUDI TAG" onMouseOver="helpline('a')" onMouseOut="helpline('x')" onClick="bbstyle(-1);">	
<br>
<input type="text" name="helpbox" size="40" maxlength="100" style="width:448px; font-size:10px" class="helpline" value="Info: selezionando il testo potrai applicare velocemente i BBcode" />
</td></tr>
</table>