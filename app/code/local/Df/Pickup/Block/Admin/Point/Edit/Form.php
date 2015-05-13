<?php
class Df_Pickup_Block_Admin_Point_Edit_Form extends Df_Core_Block_Admin_Entity_Edit_Form {
	/**
	 * @override
	 * @return string
	 */
	protected function getBuilderClass() {return Df_Pickup_Model_Form_Point_Builder::_CLASS;}
	const _CLASS = __CLASS__;
}