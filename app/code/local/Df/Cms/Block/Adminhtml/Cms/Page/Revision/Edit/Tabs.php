<?php
class Df_Cms_Block_Adminhtml_Cms_Page_Revision_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setId('page_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(df_h()->cms()->__('Revision Information'));
	}
}