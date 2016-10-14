<?php
class Df_AccessControl_AdminController extends Mage_Adminhtml_Controller_Action {
	/** @return void */
	public function categoriesAction() {
		try {
			$this->getResponse()->setBody(Zend_Json::encode(
				Df_AccessControl_Block_Admin_Tab_Tree::getChildrenNodes(rm_request('category'))
			));
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e, true);
		}
	}
}