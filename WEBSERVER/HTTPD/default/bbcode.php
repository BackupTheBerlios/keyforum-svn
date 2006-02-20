// from PHPBB code
function emot(text) {
	var txtarea = document.forms[2].elements['body'];
	text = ' ' + text + ' ';
	if (txtarea.createTextRange && txtarea.caretPos) {
		var caretPos = txtarea.caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? caretPos.text + text + ' ' : caretPos.text + text;
		txtarea.focus();
	} else {
		txtarea.value  += text;
		txtarea.focus();
	}
}


// Insert at Claret position. Code from
// http://www.faqts.com/knowledge_base/view.phtml/aid/1052/fid/130
function storeCaret(textEl) {
	if (textEl.createTextRange) textEl.caretPos = document.selection.createRange().duplicate();
}


// Startup variables
var imageTag = false;
var theSelection = false;

// Check for Browser & Platform for PC & IE specific bits
// More details from: http://www.mozilla.org/docs/web-developer/sniffer/browser_type.html
var clientPC = navigator.userAgent.toLowerCase(); // Get client info
var clientVer = parseInt(navigator.appVersion); // Get browser version

var is_ie = ((clientPC.indexOf("msie") != -1) && (clientPC.indexOf("opera") == -1));
var is_nav = ((clientPC.indexOf('mozilla')!=-1) && (clientPC.indexOf('spoofer')==-1)
                && (clientPC.indexOf('compatible') == -1) && (clientPC.indexOf('opera')==-1)
                && (clientPC.indexOf('webtv')==-1) && (clientPC.indexOf('hotjava')==-1));
var is_moz = 0;

var is_win = ((clientPC.indexOf("win")!=-1) || (clientPC.indexOf("16bit") != -1));
var is_mac = (clientPC.indexOf("mac")!=-1);

// Helpline messages
x_help = "";
b_help = "Grassetto: [b]testo[/b]  (alt+b)";
i_help = "Corsivo: [i]testo[/i]  (alt+i)";
u_help = "Sottolineato: [u]testo[/u]  (alt+u)";
q_help = "Citazione: [quote]testo[/quote]  (alt+q)";
n_help = "Citazione con nome: [quote=nome @ data]testo[/quote]  (alt+n)";
c_help = "Codice: [code]codice[/code]  (alt+c)";
p_help = "Inserisci immagine: [img]http://image_url[/img]  (alt+p)";
w_help = "Inserisci URL: [url]http://url[/url] o [url=http://url]testo URL[/url]  (alt+w)";
a_help = "Chiudi tutti i bbCode tags aperti";
e_help = "Indirizzo e-mail: [email]nome@indirizzo.com[/email] (alt+e)";
t_help = "Miniatura immagine: [tmb]http://image_url[/tmb]  (alt+t)";
f_help = "Dimensione carattere: [size=x]testo[/size]";
s_help = "Colore carattere: [color=red]testo[/color]  Aiuto: puoi anche usare color=#FF0000";
o_help = "Tipo carattere: [font=Arial]testo[/font]";


// Define the bbCode tags

<?
if ($nquote)
{
echo "var nquote='$nquote';";
} else {
$quotedate=strftime("%d/%m/%y  - %H:%M:%S",time());
echo "var nquote='[quote=nome @ $quotedate]';";
}
?>

bbcode = new Array();
bbtags = new Array('[b]','[/b]','[i]','[/i]','[u]','[/u]','[quote]','[/quote]','[code]','[/code]',nquote,'[/quote]');
imageTag = false;

// Shows the help messages in the helpline window
function helpline(help) {
	document.forms[2].helpbox.value = eval(help + "_help");
}


// Replacement for arrayname.length property
function getarraysize(thearray) {
	for (i = 0; i < thearray.length; i++) {
		if ((thearray[i] == "undefined") || (thearray[i] == "") || (thearray[i] == null))
			return i;
		}
	return thearray.length;
}

// Replacement for arrayname.push(value) not implemented in IE until version 5.5
// Appends element to the array
function arraypush(thearray,value) {
	thearray[ getarraysize(thearray) ] = value;
}

// Replacement for arrayname.pop() not implemented in IE until version 5.5
// Removes and returns the last element of an array
function arraypop(thearray) {
	thearraysize = getarraysize(thearray);
	retval = thearray[thearraysize - 1];
	delete thearray[thearraysize - 1];
	return retval;
}


function bbfontstyle(bbopen, bbclose) {
	var txtarea = document.forms[2].elements['body'];

	if ((clientVer >= 4) && is_ie && is_win) {
		theSelection = document.selection.createRange().text;
		if (!theSelection) {
			txtarea.value += bbopen + bbclose;
			txtarea.focus();
			return;
		}
		document.selection.createRange().text = bbopen + theSelection + bbclose;
		txtarea.focus();
		return;
	}
	else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0))
	{
		mozWrap(txtarea, bbopen, bbclose);
		return;
	}
	else
	{
		txtarea.value += bbopen + bbclose;
		txtarea.focus();
	}
	storeCaret(txtarea);
}


function bbstyle(bbnumber) {
	var txtarea = document.forms[2].elements['body'];

	txtarea.focus();
	donotinsert = false;
	theSelection = false;
	bblast = 0;

	if (bbnumber == -1) { // Close all open tags & default button names
		while (bbcode[0]) {
			butnumber = arraypop(bbcode) - 1;
			txtarea.value += bbtags[butnumber + 1];
			buttext = eval('document.forms[2].addbbcode' + butnumber + '.value');
			eval('document.forms[2].addbbcode' + butnumber + '.value ="' + buttext.substr(0,(buttext.length - 1)) + '"');
		}
		imageTag = false; // All tags are closed including image tags :D
		txtarea.focus();
		return;
	}

	if ((clientVer >= 4) && is_ie && is_win)
	{
		theSelection = document.selection.createRange().text; // Get text selection
		if (theSelection) {
			// Add tags around selection
			document.selection.createRange().text = bbtags[bbnumber] + theSelection + bbtags[bbnumber+1];
			txtarea.focus();
			theSelection = '';
			return;
		}
	}
	else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0))
	{
		mozWrap(txtarea, bbtags[bbnumber], bbtags[bbnumber+1]);
		return;
	}

	// Find last occurance of an open tag the same as the one just clicked
	for (i = 0; i < bbcode.length; i++) {
		if (bbcode[i] == bbnumber+1) {
			bblast = i;
			donotinsert = true;
		}
	}

	if (donotinsert) {		// Close all open tags up to the one just clicked & default button names
		while (bbcode[bblast]) {
				butnumber = arraypop(bbcode) - 1;
				txtarea.value += bbtags[butnumber + 1];
				buttext = eval('document.forms[2].addbbcode' + butnumber + '.value');
				eval('document.forms[2].addbbcode' + butnumber + '.value ="' + buttext.substr(0,(buttext.length - 1)) + '"');
				imageTag = false;
			}
			txtarea.focus();
			return;
	} else { // Open tags

		if (imageTag && (bbnumber != 14)) {		// Close image tag before adding another
			txtarea.value += bbtags[15];
			lastValue = arraypop(bbcode) - 1;	// Remove the close image tag from the list
			document.forms[2].addbbcode14.value = "Img";	// Return button back to normal state
			imageTag = false;
		}

		// Open tag
		txtarea.value += bbtags[bbnumber];
		if ((bbnumber == 14) && (imageTag == false)) imageTag = 1; // Check to stop additional tags after an unclosed image tag
		arraypush(bbcode,bbnumber+1);
		eval('document.forms[2].addbbcode'+bbnumber+'.value += "*"');
		txtarea.focus();
		return;
	}
	storeCaret(txtarea);
}

// From http://www.massless.org/mozedit/
function mozWrap(txtarea, open, close)
{
	var selLength = txtarea.textLength;
	var selStart = txtarea.selectionStart;
	var selEnd = txtarea.selectionEnd;
	if (selEnd == 1 || selEnd == 2)
		selEnd = selLength;

	var s1 = (txtarea.value).substring(0,selStart);
	var s2 = (txtarea.value).substring(selStart, selEnd)
	var s3 = (txtarea.value).substring(selEnd, selLength);
	txtarea.value = s1 + open + s2 + close + s3;
	return;
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


// based on FUDforum code

/* indentify the browser */
var IE4 = (document.all) ? 1 : 0;

function rs_txt_box(name, col_inc, row_inc,savename)
{
        if (IE4) {  
                var obj = document.all[name];
                var objs = document.all[savename];
        } else {
                var obj = document.getElementById(name);
                var objs = document.getElementById(savename);
        }                                   

        obj.rows += row_inc;           
        obj.cols += col_inc;           
        
        objs.value = obj.rows+":"+obj.cols;
        
        
}