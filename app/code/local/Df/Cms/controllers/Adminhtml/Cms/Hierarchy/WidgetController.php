<?php
class Df_Cms_Adminhtml_Cms_Hierarchy_WidgetController extends Mage_Adminhtml_Controller_Action {
	/**
	 * Chooser Source action
	 */
	public function chooserAction()
	{
		$this->getResponse()->setBody(
			$this->_getTreeBlock()->getTreeHtml()
		);
	}

	/**
	 * Tree block instance
	 */
	protected function _getTreeBlock() {
		return Df_Cms_Block_Adminhtml_Cms_Hierarchy_Widget_Chooser::i(
			$this->getRequest()->getParam('uniq_id')
		);
	}
}