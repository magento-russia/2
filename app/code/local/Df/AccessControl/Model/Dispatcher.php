<?php
class Df_AccessControl_Model_Dispatcher {
	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function catalog_product_collection_load_before(
		Varien_Event_Observer $observer
	) {
		try {
			if (df_is_admin()) {
				df_handle_event(
					Df_AccessControl_Model_Handler_Catalog_Product_Collection_ExcludeForbiddenProducts::_CLASS
					,Df_Catalog_Model_Event_Product_Collection_Load_Before::_CLASS
					,$observer
				);
			}
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function catalog_category_collection_load_before(
		Varien_Event_Observer $observer
	) {
		try {
			if (df_is_admin()) {
				df_handle_event(
					Df_AccessControl_Model_Handler_Catalog_Category_Collection_ExcludeForbiddenCategories::_CLASS
					,Df_Catalog_Model_Event_Category_Collection_Load_Before::_CLASS
					,$observer
				);
			}
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function admin_roles_save_after(
		Varien_Event_Observer $observer
	) {
		try {
			df_handle_event(
				Df_AccessControl_Model_Handler_RemindLastSavedRoleId::_CLASS
				,Df_Admin_Model_Event_Roles_Save_After::_CLASS
				,$observer
			);
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function controller_action_postdispatch_adminhtml_permissions_role_saverole(
		Varien_Event_Observer $observer
	) {
		try {
			df_handle_event(
				Df_AccessControl_Model_Handler_Permissions_Role_Saverole_UpdateCatalogAccessRights::_CLASS
				,Df_AccessControl_Model_Event_Permissions_Role_Saverole::_CLASS
				,$observer
			);
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}
}