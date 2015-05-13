<?php
/**
 * @method Df_Pickup_Model_Point getEntity()
 */
class Df_Pickup_Model_Form_Point_Builder extends Df_Core_Model_Form_Builder {
	/**
	 * @override
	 * @return Df_Core_Model_Form_Builder
	 */
	protected function addFormFields() {
		$this
			->addField(
				$name = Df_Pickup_Model_Point::P__NAME
				,$label = Df_Pickup_Form_Point::LABEL__NAME
				,$type = Df_Varien_Data_Form_Element_Abstract::TYPE__TEXT
				,$required = true
			)
		;
		$this
			->runDependentBuilder(
				Df_Core_Model_Form_Location_Builder::_CLASS
				,Df_Pickup_Model_Point::DEPENDENCY__LOCATION
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
				,array(Df_Pickup_Model_Point::P__NAME => '')
			)
		;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {
		return Df_Pickup_Model_Point::_CLASS;
	}

	const _CLASS = __CLASS__;
}