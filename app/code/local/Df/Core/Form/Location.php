<?php
class Df_Core_Form_Location extends Df_Zf_Form {
	/** @return void */
	public function init() {
		parent::init();
		$this
			->addElement(
				Df_Varien_Data_Form_Element_Abstract::TYPE__HIDDEN
				,self::FIELD__ID
				,array('required' => false)
			)
			->addElement(
				Df_Varien_Data_Form_Element_Abstract::TYPE__TEXT
				,Df_Core_Model_Location::P__CITY
				,array(
					'label' => self::LABEL__CITY
					,'required' => true
					,'filters' => array('StringTrim')
				)
			)
			->addElement(
				Df_Varien_Data_Form_Element_Abstract::TYPE__TEXTAREA
				,Df_Core_Model_Location::P__STREET_ADDRESS
				,array(
					'label' => self::LABEL__STREET_ADDRESS
					,'required' => true
					,'filters' => array('StringTrim')
				)
			)
		;
	}

	const _CLASS = __CLASS__;
	const FIELD__ID = 'id';
	const FIELD__MAP = 'map';
	const LABEL__CITY = 'Город';
	const LABEL__MAP = 'Карта';
	const LABEL__STREET_ADDRESS = 'Адрес';
}