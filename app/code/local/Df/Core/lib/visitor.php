<?php
/**
 * @used-by Df_Core_Model_Action::processRedirect()
 * @return string
 */
function df_referer() {return Df_Core_Controller_Mock::getRefererUrl();}

/** @return string */
function df_visitor_ip() {
	return
		df_is_it_my_local_pc()
		? '91.229.242.51'//'92.243.166.8'
		: df_mage()->core()->httpHelper()->getRemoteAddr()
	;
}

/** @return Df_Core_Model_Geo_Locator_Multi */
function df_visitor_location() {return Df_Core_Model_Geo_Locator_Multi::s(df_visitor_ip());}


