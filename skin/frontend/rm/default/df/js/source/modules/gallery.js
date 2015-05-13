function show_hide_checkbox_fields(objName,condition)
{
	document.getElementById(objName).disabled = condition==1?"disabled":"";
}
