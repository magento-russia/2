<?php
/**
 * @method Df_Warehousing_Model_Warehouse getEntity()
 */
class Df_Warehousing_Model_Form_Warehouse_Builder extends Df_Core_Model_Form_Builder {
	/**
	 * @override
	 * @return Df_Core_Model_Form_Builder
	 */
	protected function addFormFields() {
		$this
			->addField(
				$name = Df_Warehousing_Model_Warehouse::P__NAME
				,$label = Df_Warehousing_Form_Warehouse::LABEL__NAME
				,$type = Df_Varien_Data_Form_Element_Abstract::TYPE__TEXT
				,$required = true
			)
		;
		$this
			->runDependentBuilder(
				Df_Core_Model_Form_Location_Builder::_CLASS
				,Df_Warehousing_Model_Warehouse::DEPENDENCY__LOCATION
			)
		;
		return $this;
	}

	/**
	 * @override
	 * @return array
	 */
	protected function getDataDefault() {
		return
			array_merge(
				parent::getDataDefault()
				,array(Df_Warehousing_Model_Warehouse::P__NAME => '')
			)
		;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {
		return Df_Warehousing_Model_Warehouse::_CLASS;
	}

	const _CLASS = __CLASS__;
}