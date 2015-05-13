<?php
class Df_Warehousing_Block_Admin_Warehouse_Edit_Form extends Df_Core_Block_Admin_Entity_Edit_Form {
	/**
	 * @override
	 * @return string
	 */
	protected function getBuilderClass() {
		return Df_Warehousing_Model_Form_Warehouse_Builder::_CLASS;
	}

	const _CLASS = __CLASS__;

}