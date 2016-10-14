<?php
class Df_AccessControl_Observer {
	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function catalog_product_collection_load_before(Varien_Event_Observer $o) {
		try {
			if (df_is_admin()) {
				df_handle_event(
					Df_AccessControl_Model_Handler_Catalog_Product_Collection_ExcludeForbiddenProducts::_C
					,Df_Catalog_Model_Event_Product_Collection_Load_Before::_C
					,$o
				);
			}
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
	public function catalog_category_collection_load_before(Varien_Event_Observer $o) {
		try {
			if (df_is_admin()) {
				df_handle_event(
					Df_AccessControl_Model_Handler_Catalog_Category_Collection_ExcludeForbiddenCategories::_C
					,Df_Catalog_Model_Event_Category_Collection_Load_Before::_C
					,$o
				);
			}
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
	public function admin_roles_save_after(Varien_Event_Observer $o) {
		try {
			if (df_cfg()->admin()->access_control()->getEnabled()) {
				/** @var Mage_Admin_Model_Roles $role */
				$role = $o['object'];
				df_h()->accessControl()->setLastSavedRoleId($role->getId());
			}
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
	public function controller_action_postdispatch_adminhtml_permissions_role_saverole(
		Varien_Event_Observer $o
	) {
		try {
			df_handle_event(
				Df_AccessControl_Model_Handler_Permissions_Role_Saverole_UpdateCatalogAccessRights::_C
				,Df_AccessControl_Model_Event_Permissions_Role_Saverole::_C
				,$o
			);
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}
}