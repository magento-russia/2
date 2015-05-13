<?php
class Df_Pickup_Block_Admin_Point_Edit extends Df_Adminhtml_Block_Widget_Form_Container {
	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {return Df_Pickup_Model_Point::_CLASS;}

	/**
	 * @override
	 * @return string
	 */
	protected function getNewEntityTitle() {return 'Новый пункт выдачи товара';}

	const _CLASS = __CLASS__;
}