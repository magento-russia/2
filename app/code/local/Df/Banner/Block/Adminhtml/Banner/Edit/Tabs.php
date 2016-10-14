<?php
class Df_Banner_Block_Adminhtml_Banner_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
	/**
	 * @override
	 * @return Df_Banner_Block_Adminhtml_Banner_Edit_Tabs
	 */
	protected function _beforeToHtml() {
		$this->addTab('form_section', array(
			'label' => 'Основные'
			,'title' => 'Основные'
			,'content' => rm_render('Df_Banner_Block_Adminhtml_Banner_Edit_Tab_Form')
		));
		parent::_beforeToHtml();
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setId('df_banner_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle('Настройки');
	}
}