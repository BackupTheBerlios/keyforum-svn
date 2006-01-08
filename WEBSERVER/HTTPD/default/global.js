//==========================================
// Show/Hide section
//==========================================

function ShowHideSection( fid, add,sname )
{
	saved = new Array();
	clean = new Array();
	
	if ( tmp = GetKFcookie('collapseprefs',sname))
	{
		saved = tmp.split(",");
				
	}
	
	for( i = 0 ; i < saved.length; i++ )
	{
		if ( saved[i] != fid && saved[i] != "" )
		{
			clean[clean.length] = saved[i];
		}
	}

	if ( add )
	{
		clean[ clean.length ] = fid;
		show_div( GetElementById( 'divhide_'+fid  ) );
		hide_div( GetElementById( 'divshow_'+fid  ) );
	}
	else
	{
		show_div( GetElementById( 'divshow_'+fid  ) );
		hide_div( GetElementById( 'divhide_'+fid  ) );
	}
	
	SetKFcookie( 'collapseprefs', clean.join(','), 1,sname );
	
	
}

function ShowHideAll(sections,add,sname)
{
	

saved = sections.split(",");

	if ( add )
	{

	for( ii = 0 ; ii < saved.length; ii++ )
	{
			show_div( GetElementById( 'divshow_'+saved[ii] ) );

			hide_div( GetElementById( 'divhide_'+saved[ii] ) );
		}
	SetKFcookie( 'collapseprefs', '', 1,sname );
	}
	else
	{
	for( ii = 0 ; ii < saved.length; ii++ )
	{
			show_div( GetElementById( 'divhide_'+saved[ii] ) );
			hide_div( GetElementById( 'divshow_'+saved[ii] ) );
		}
	SetKFcookie( 'collapseprefs', sections, 1,sname );
	}

}


//==========================================
// Set cookie
//==========================================

function SetKFcookie( name, value, sticky,sname )
{
	expire = "";
	domain = "";
	path   = "/";
	
	if ( sticky )
	{
		expire = "; expires=Wed, 1 Jan 2020 00:00:00 GMT";
	}
	

	document.cookie = sname + name + "=" + value + "; path=" + path + expire + domain + ';';
}


//==========================================
// Get cookie
//==========================================

function GetKFcookie( name,sname )
{

    cname = sname + name + '=';

	cpos  = document.cookie.indexOf( cname );
	
	if ( cpos != -1 )
	{
		cstart = cpos + cname.length;
		cend   = document.cookie.indexOf(";", cstart);
		
		if (cend == -1)
		{
			cend = document.cookie.length;
		}
		
		return unescape( document.cookie.substring(cstart, cend) );
	}

	
	return null;
}




//==========================================
// Set DIV ID to hide
//==========================================

function hide_div(itm)
{
	if ( ! itm ) return;
	
	itm.style.display = "none";
}

//==========================================
// Set DIV ID to show
//==========================================

function show_div(itm)
{
	if ( ! itm ) return;
	
	itm.style.display = "";
}


//==========================================
// Get element by id
//==========================================

function GetElementById(id)
{
	itm = null;
	
	if (document.getElementById)
	{
		itm = document.getElementById(id);
	}
	else if (document.all)
	{
		itm = document.all[id];
	}
	else if (document.layers)
	{
		itm = document.layers[id];
	}
	
	return itm;
}


//==========================================
// Spoiler
//==========================================

function togglevis(id) {
  var obj = "";

  if(document.getElementById) obj = document.getElementById(id).style;
  else if(document.all) obj = document.all[id];
  else if(document.layers) obj = document.layers[id];
  else return 1;

  if(obj.visibility == "visible") obj.visibility = "hidden";
  else if(obj.visibility != "hidden") obj.visibility = "hidden";
  else obj.visibility = "visible";
}


//==========================================
// Reload
//==========================================

var reload_cname = "kf_" + pname + "reload";
function runit(reload_cname) {
  if(getc(reload_cname)) {
    if(getc(reload_cname)*1>0) setTimeout("document.location=document.location;",getc(reload_cname)*1000);
  }
}

window.onload=mklastselected;

function mklastselected() {
  if(getc(reload_cname)) {
    document.reloader.reload_value.value=getc(reload_cname);
  }
  runit(reload_cname);
}

//==========================================
// cookies (Oberon per reload)
//==========================================
function getc(name) {
  var rs = null;
  var mc = " " + document.cookie + ";";
  var sn = " " + name + "=";
  var sc = mc.indexOf(sn);
  var ec;
  if (sc != -1) {
    sc += sn.length;ec=mc.indexOf(";",sc);
    rs = unescape(mc.substring(sc,ec));
  }
  return rs;
}
function setc(name,value) {
  document.cookie=name+"="+escape(value);
}




