<?php
class Df_Cms_Block_Admin_Page_Preview_Buttons extends Mage_Adminhtml_Block_Widget_Container {
	/**
	 * @override
	 * @see Mage_Adminhtml_Block_Widget_Container::_toHtml()
	 * @used-by Mage_Core_Block_Abstract::toHtml()
	 * @return string
	 */
	protected function _toHtml() {
		parent::_toHtml();
		return $this->getButtonsHtml();
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_addButton(
				'preview'
				,array(
					'id' => 'preview-buttons-preview'
					,'label' => $this->__('Preview')
					,'class' => 'preview'
					,'onclick' => 'preview()'
				)
			)
		;
		if (Df_Cms_Model_Config::s()->canCurrentUserPublishRevision()) {
			$this
				->_addButton(
					'publish'
					,array(
						'id' => 'preview-buttons-publish'
						,'label' => $this->__('Publish')
						,'class' => 'publish'
						,'onclick' => 'publish()'
					)
				)
			;
		}
	}
}