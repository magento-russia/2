<?php
/**
 * @method Df_Core_Model_Location getEntity()
 */
class Df_Core_Model_Form_Location_Builder extends Df_Core_Model_Form_Builder {
	/**
	 * @override
	 * @return Df_Core_Model_Form_Builder
	 */
	protected function addFormFields() {
		$this->getFieldset()
			->addType(
				Df_Varien_Data_Form_Element_Map::_CLASS
				,Df_Varien_Data_Form_Element_Map::_CLASS
			)
		;
		$this
			->addField(
				$name = Df_Core_Model_Location::P__CITY
				,$label = Df_Core_Form_Location::LABEL__CITY
				,$type = Df_Varien_Data_Form_Element_Abstract::TYPE__TEXT
				,$required = true
			)
			->addField(
				$name = Df_Core_Model_Location::P__STREET_ADDRESS
				,$label = Df_Core_Form_Location::LABEL__STREET_ADDRESS
				,$type = Df_Varien_Data_Form_Element_Abstract::TYPE__TEXTAREA
				,$required = true
				,$config = array(
					'style' => 'height: 3em;'
				)
			)
			->addField(
				$name = Df_Core_Form_Location::FIELD__MAP
				,$label = Df_Core_Form_Location::LABEL__MAP
				,$type = Df_Varien_Data_Form_Element_Map::_CLASS
				,$required = false
				,$config = array(
				)
			)
		;
		return $this;
	}

	/**
	 * @override
	 * @return array
	 */
	protected function getDataDefault() {
		/** @var array $result */
		$result =
			array_merge(
				parent::getDataDefault()
				,array(
					Df_Core_Model_Location::P__CITY => ''
				)
			)
		;
		df_result_array($result);
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {
		return Df_Core_Model_Location::_CLASS;
	}

	const _CLASS = __CLASS__;
}