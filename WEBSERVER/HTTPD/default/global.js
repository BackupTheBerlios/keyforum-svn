//==========================================
// Toggle category
//==========================================

function togglecategory( fid, add )
{
	saved = new Array();
	clean = new Array();


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
