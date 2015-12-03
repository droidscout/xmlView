
function toggleLink( className ) {
	
	if( document.getElementById(className).style.textDecoration == "none" )
		document.getElementById(className).style.textDecoration = "underline";
	else
		document.getElementById(className).style.textDecoration = "none";
}
