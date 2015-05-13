<?php
class Df_Pickup_Block_Admin_Point_Index extends Mage_Adminhtml_Block_Widget_Grid_Container {
	/**
	 * @override
	 * @return Df_Pickup_Block_Admin_Point_Index
	 */
	protected function _construct() {
		parent::_construct();
		$this->_blockGroup = 'df_pickup';
		/**
		 * На самом деле, это поле никогда не используется в родительских классах
		 * для работы с контроллером.
		 * Вместо этого, оно лишь обозначает класс блока перечня элементов после косой черты ("/").
		 */
		$this->_controller = 'admin_point_index';
		$this->_headerText = 'Пункты выдачи товарв';
		$this->_addButtonLabel = 'добавить пункт';
	}
	const _CLASS = __CLASS__;
}