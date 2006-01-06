//==========================================
// Toggle category
//==========================================

function togglecategory( fid, add )
{
	saved = new Array();
	clean = new Array();

	//-----------------------------------
	// Get any saved info
	//-----------------------------------
	
	if ( tmp = getc('collapseprefs') )
	{
		saved = tmp.split(",");
	}


	//-----------------------------------
	// Remove bit if exists
	//-----------------------------------
	
	for( i = 0 ; i < saved.length; i++ )
	{
		if ( saved[i] != fid && saved[i] != "" )
		{
			clean[clean.length] = saved[i];
		}
	}
	
	//-----------------------------------
	// Add?
	//-----------------------------------
	
	if ( add )
	{
		clean[ clean.length ] = fid;
		my_show_div( my_getbyid( 'fc_'+fid  ) );
		my_hide_div( my_getbyid( 'fo_'+fid  ) );
	}
	else
	{
		my_show_div( my_getbyid( 'fo_'+fid  ) );
		my_hide_div( my_getbyid( 'fc_'+fid  ) );
	}
	
		setc( 'collapseprefs', clean.join(','), 1 );

	
}


//==========================================
// Set DIV ID to hide
//==========================================

function my_hide_div(itm)
{
	if ( ! itm ) return;
	
	itm.style.display = "none";
}

//==========================================
// Set DIV ID to show
//==========================================

function my_show_div(itm)
{
	if ( ! itm ) return;
	
	itm.style.display = "";
}


//==========================================
// Get element by id
//==========================================

function my_getbyid(id)
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
// cookies
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


