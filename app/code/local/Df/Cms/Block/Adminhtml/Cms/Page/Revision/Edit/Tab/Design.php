<?php
class Df_Cms_Block_Adminhtml_Cms_Page_Revision_Edit_Tab_Design
	extends Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Design {
	/**
	 * Adding onchange js call
	 * @return Df_Cms_Block_Adminhtml_Cms_Page_Revision_Edit_Tab_Design
	 */
	protected function _prepareForm()
	{
		parent::_prepareForm();
		df_h()->cms()->addOnChangeToFormElements($this->getForm(), 'dataChanged();');
		return $this;
	}

	/**
	 * Check permission for passed action
	 * Rewrite CE save permission to EE save_revision
	 *
	 * @param string $action
	 * @return bool
	 */
	protected function _isAllowedAction($action)
	{
		if ('save' === $action) {
			$action = 'save_revision';
		}
		return parent::_isAllowedAction($action);
	}
}