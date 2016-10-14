<?php
class Df_Banner_Block_Adminhtml_Banner extends Mage_Adminhtml_Block_Widget_Grid_Container {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_controller = 'adminhtml_banner';
		$this->_blockGroup = 'df_banner';
		$this->_headerText = 'Рекламные щиты';
		$this->_addButtonLabel = 'Повесить новый щит...';
	}
}