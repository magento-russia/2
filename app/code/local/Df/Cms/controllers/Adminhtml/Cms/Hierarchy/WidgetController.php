<?php
class Df_Cms_Adminhtml_Cms_Hierarchy_WidgetController extends Mage_Adminhtml_Controller_Action {
	/** @return void */
	public function chooserAction() {
		$this->getResponse()->setBody(
			Df_Cms_Block_Admin_Hierarchy_Widget_Chooser::i(df_request('uniq_id'))->getTreeHtml()
		);
	}
}