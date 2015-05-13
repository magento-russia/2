<?php
class Df_Warehousing_Block_Admin_Warehouse_Index extends Mage_Adminhtml_Block_Widget_Grid_Container {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_blockGroup = 'df_warehousing';
		/**
		 * На самом деле, это поле никогда не используется в родительских классах
		 * для работы с контроллером.
		 * Вместо этого, оно лишь обозначает класс блока перечня элементов после косой черты ("/").
		 */
		$this->_controller = 'admin_warehouse_index';
		$this->_headerText = 'Склады';
		$this->_addButtonLabel = 'добавить склад';
	}
	const _CLASS = __CLASS__;
}