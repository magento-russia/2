<?php
class Df_Page_Model_Dispatcher {
	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function page_block_html_topmenu_gethtml_before(Varien_Event_Observer $observer) {
		try {
			Df_Page_Model_Menu_Product_Inserter::i(
				Df_Page_Model_Menu_Product_New::i($observer->getData('menu'))
			)->process();
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function rm_menu_top_add_submenu(Varien_Event_Observer $observer) {
		try {
			Df_Page_Model_Menu_Product_Inserter::i(
				Df_Page_Model_Menu_Product_Old::i($observer->getData('menu'))
			)->process();
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}
}