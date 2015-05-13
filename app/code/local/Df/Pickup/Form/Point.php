<?php
class Df_Pickup_Form_Point extends Df_Zf_Form {
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
				'text'
				,Df_Pickup_Model_Point::P__NAME
				,array(
					'label' => self::LABEL__NAME
					,'required' => true
					,'filters' => array('StringTrim')
				)
			)
			->addElement(
				'text'
				,Df_Pickup_Model_Point::P__LOCATION_ID
				,array(
					'label' => self::LABEL__LOCATION_ID
					,'required' => true
					,'validators' => array('Int')
				)
			)
		;
	}

	const _CLASS = __CLASS__;
	const FIELD__ID = 'id';
	const LABEL__LOCATION_ID = 'Идентификатор пункта выдачи товара';
	const LABEL__NAME = 'Название';
}