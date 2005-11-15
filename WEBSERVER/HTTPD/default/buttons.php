<input name="btnBold" type="button" id="btnBold" value=" G " onMouseOver="btnHover('Grassetto');" onMouseOut="btnHover('');" onClick="toggleTag('bold');">
          <input name="btnItalic" type="button" id="btnItalic" value=" C " onMouseOver="btnHover('Corsivo');" onMouseOut="btnHover('');" onClick="toggleTag('italic');">
          <input name="btnUnderline" type="button" id="btnUnderline" value=" S " onMouseOver="btnHover('Sottolineato');" onMouseOut="btnHover('');" onClick="toggleTag('underline');">
          <input name="btnQuote" type="button" id="btnQuote" value="Quote" onMouseOver="btnHover('Citazione');" onMouseOut="btnHover('');" onClick="toggleTag('quote');">
          <input name="btnCode" type="button" id="btnCode" value="Code" onMouseOver="btnHover('Testo fixed font');" onMouseOut="btnHover('');" onClick="toggleTag('code');">
          <input name="btnLink" type="button" id="btnLink" value="http://" onMouseOver="btnHover('Inserisci un link');" onMouseOut="btnHover('');" onClick="btnInsertLink();">
          <input name="btnEmail" type="button" id="btnEmail" value=" @ " onMouseOver="btnHover('Inserisci un indirizzo email');" onMouseOut="btnHover('');" onClick="btnInsertEmail();">
          <input name="btnImage" type="button" id="btnImage" value="IMG" onMouseOver="btnHover('Inserisci un\'immagine');" onMouseOut="btnHover('');" onClick="btnInsertImage();">
          <input name="btnImage" type="button" id="btnThumbImage" value="TMB" onMouseOver="btnHover('Inserisci un ImgThumbnail');" onMouseOut="btnHover('');" onClick="btnInsertThumbImage();">
	  <br>
          <input name="txtAbout" type="text" id="txtAbout" size="30" readonly="1" style='font-size:10px;font-family:verdana,arial;border:0px;'>