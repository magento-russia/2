<?php
class Df_Banner_Block_Adminhtml_Banneritem extends Mage_Adminhtml_Block_Widget_Grid_Container {
	/** @return string */
	public function getSaveOrderUrl() {return $this->getUrl('*/*/setOrder');}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_controller = 'adminhtml_banneritem';
		$this->_blockGroup = 'df_banner';
		$this->_headerText = 'Рекламные объявления';
		$this->_addButton('save', array(
			'label' => 'Утвердить порядок показа'
			,'onclick' => 'save_order()'
			,'id' => 'save_cat'
		));
		$this->_addButtonLabel = 'Составить новое объявление...';
	}
}