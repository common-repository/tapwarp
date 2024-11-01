/*
Plugin Name: Tap Warp
Description: Upload pictures from smartphone to wordpress directly.
Version:     0.1.0
Author:      Yuxiang Mao
Author URI:  http://qaon.net
License:     GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

function tapwarp_rebuild_sizehint_field()
{
	var width=document.getElementById('tapwarp-sizehint-width').value;
	var wunit=document.getElementById('tapwarp-sizehint-wunit').value;
	var height=document.getElementById('tapwarp-sizehint-height').value;
	var hunit=document.getElementById('tapwarp-sizehint-hunit').value;
	var sizemode=document.getElementById('tapwarp-sizehint-mode').value;
	var setframe=document.getElementById('tapwarp-sizehint-setframe').value;

	var sizeHint='';
	width=parseInt(width,10);
	height=parseInt(height,10);
	if (isNaN(width))
		width='';
	if (isNaN(height))
		height='';

	document.getElementById('tapwarp-sizehint-width').value=width;
	document.getElementById('tapwarp-sizehint-height').value=height;

	if (wunit=='%' || hunit=='%')
	{
		sizemode='';
		document.getElementById('tapwarp-sizehint-mode').selectedIndex=0;
	}

	if (width || height)
	{
		if (width)
			sizeHint+=width+wunit;
		if (height)
			sizeHint+='x'+height+hunit;
		sizeHint+=sizemode;
		sizeHint+=setframe;
	}
	document.getElementById('tapwarp_accept_size_hint').value=sizeHint;
}

function tapwarp_regenerate_authentication_key()
{
	var chars='0123456789abcdefghijklmnopqrstuvwxyz';
	var authkey='';
	for (var i=0;i<128;i++)
		authkey+=chars[Math.floor(Math.random()*36)];
	document.getElementsByName('tapwarp_accept_authKey')[0].value=authkey;
}

function tapwarp_initialize_sizehint_fields()
{
	document.getElementById('tapwarp-sizehint-width').addEventListener('change',tapwarp_rebuild_sizehint_field);
	document.getElementById('tapwarp-sizehint-wunit').addEventListener('change',tapwarp_rebuild_sizehint_field);
	document.getElementById('tapwarp-sizehint-height').addEventListener('change',tapwarp_rebuild_sizehint_field);
	document.getElementById('tapwarp-sizehint-hunit').addEventListener('change',tapwarp_rebuild_sizehint_field);
	document.getElementById('tapwarp-sizehint-mode').addEventListener('change',tapwarp_rebuild_sizehint_field);
	document.getElementById('tapwarp-sizehint-setframe').addEventListener('change',tapwarp_rebuild_sizehint_field);
	document.getElementById('tapwarp-regenerate-authentication-key').addEventListener('click',tapwarp_regenerate_authentication_key);

	var sizeHint=document.getElementById('tapwarp_accept_size_hint').value;
	var shps=sizeHint.match(/^(?:([0-9]+(?:\.[0-9]+)?)(%?))?(?:x([0-9]+(?:\.[0-9]+)?)(%?)([<>!|^]?)([*])?)?$/);
	if (shps==null)
		shps=[];
	var width=parseInt(shps[1],10);
	var wunit=shps[2];
	var height=parseInt(shps[3],10);
	var hunit=shps[4];
	var sizemode=shps[5];
	var setframe=shps[6];

	function selbyval(selobj,val)
	{
		if (val==undefined)
			val='';
		for (var i=0;i<selobj.options.length;i++)
			if (selobj.options[i].value==val)
			{
				selobj.selectedIndex=i;
				return;
			}
		selobj.selectedIndex=-1;
	}

	document.getElementById('tapwarp-sizehint-width').value=width;
	document.getElementById('tapwarp-sizehint-height').value=height;
	selbyval(document.getElementById('tapwarp-sizehint-wunit'),wunit);
	selbyval(document.getElementById('tapwarp-sizehint-hunit'),hunit);
	selbyval(document.getElementById('tapwarp-sizehint-mode'),sizemode);
	selbyval(document.getElementById('tapwarp-sizehint-setframe'),setframe);

	tapwarp_rebuild_sizehint_field();
}
