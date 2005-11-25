
var boldStatus = 0;
var italicStatus = 0;
var underlineStatus = 0;
var quoteStatus = 0;
var codeStatus = 0;

	function emot(st) {
		obj_body = document.forms[2].elements['body'];
		lastchar = obj_body.value.charAt(obj_body.value.length-1);
		if (lastchar == ' ' || lastchar == '\n' || obj_body.value.length==0)
			obj_body.value = obj_body.value + st + ' ';
		else
			obj_body.value = obj_body.value + ' ' + st + ' ';
		obj_body.focus();
	}

	function toggleTag(tag) {
		obj_body = document.forms[2].elements['body'];
		tstat = eval(tag + 'Status');
		if (tag.toLowerCase() == 'bold') tagchar = 'B';
		if (tag.toLowerCase() == 'italic') tagchar = 'I';
		if (tag.toLowerCase() == 'underline') tagchar = 'U';
		if (tag.toLowerCase() == 'quote') tagchar = 'QUOTE';
		if (tag.toLowerCase() == 'code') tagchar = 'CODE';

		if (tstat == 0) {
			obj_body.value = obj_body.value + '[' + tagchar + ']';
			eval(tag + 'Status = 1');
		}
		else {
			obj_body.value = obj_body.value + '[/' + tagchar + ']';
			eval(tag + 'Status = 0');
		}

		obj_body.focus();

	}

	function btnInsertLink() {
		lnk = prompt("Inserisci l'indirizzo a cui si riferisce il link:", "http://");
		if (lnk != null && lnk != "") {
			if (lnk.substring(0,lnk.substring(7, lnk.length).indexOf("/")+7).toLowerCase() == location.href.substring(0,location.href.substring(7, location.href.length).indexOf("/")+7).toLowerCase()) linktag = "FORUM_URL"; else linktag = "URL"
			desc = prompt("Inserisci il titolo del link", lnk);
			if (desc != null && desc != "") {
				if (lnk.substring(0,4).toLowerCase() == "www.") lnk = "http://" + lnk;
				obj_body = document.forms[2].elements['body'];
				obj_body.value = obj_body.value + '[' + linktag + '=' + lnk + ']' + desc + '[/' + linktag + ']';
				obj_body.focus();
			}
		}
	}


	function btnInsertImage() {
		lnk = prompt("Inserisci l'indirizzo web dell'immagine:", "http://");
		if (lnk != null && lnk != "") {
			obj_body = document.forms[2].elements['body'];
			obj_body.value = obj_body.value + '[IMG]' + lnk + '[/IMG]';
			obj_body.focus();
		}
	}

	function btnInsertThumbImage() {
		lnk = prompt("Inserisci l'indirizzo web dell'immagine:", "http://");
		if (lnk != null && lnk != "") {
			obj_body = document.forms[2].elements['body'];
			obj_body.value = obj_body.value + '[TMB]' + lnk + '[/TMB]';
			obj_body.focus();
		}
	}

	function btnInsertEmail() {
		lnk = prompt("Inserisci l'indirizzo email:", "");
		if (lnk != null && lnk != "") {
			obj_body = document.forms[2].elements['body'];
			obj_body.value = obj_body.value + '[EMAIL]' + lnk + '[/EMAIL]';
			obj_body.focus();
		}
	}

	function btnHover(str) {
		document.forms[2].elements['txtAbout'].value = str;
	}
