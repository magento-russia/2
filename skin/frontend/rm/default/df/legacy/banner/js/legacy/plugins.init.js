// no conflicts with other frameworks
var $j = jQuery.noConflict();

//
// window.onload function to setup various javascripts
//
$j(function() {
	//fix png transparency in IE<6
	//$j('img[@src$=.png]').ifixpng();
	//lightbox gallery
  $j('.lightbox_image').lightBox();
  //This is for Zooming Images
	//$j.imgzoom();
});


