<?php
class Df_Cms_Block_Adminhtml_Cms_Page_Preview_Store extends Mage_Adminhtml_Block_Store_Switcher {
	/**
	 * Retrieve id of currently selected store
	 * @return int
	 */
	public function getStoreId()
	{
		if (!$this->hasStoreId()) {
			$this->setData('store_id', (int)$this->getRequest()->getPost('preview_selected_store'));
		}
		return $this->_getData('store_id');
	}
}