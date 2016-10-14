<?php
class Df_Page_Observer {
	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function page_block_html_topmenu_gethtml_before(Varien_Event_Observer $o) {
		try {
			Df_Page_Model_Menu_Product_Inserter::p(Df_Page_Model_Menu_Product_New::i($o['menu']));
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function rm_menu_top_add_submenu(Varien_Event_Observer $o) {
		try {
			Df_Page_Model_Menu_Product_Inserter::p(Df_Page_Model_Menu_Product_Old::i($o['menu']));
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}
}