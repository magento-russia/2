<?php
class Df_Warehousing_Block_Admin_Warehouse_Edit extends Df_Adminhtml_Block_Widget_Form_Container {
	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {
		return Df_Warehousing_Model_Warehouse::_CLASS;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getNewEntityTitle() {
		return 'Новый склад';
	}

	const _CLASS = __CLASS__;

}