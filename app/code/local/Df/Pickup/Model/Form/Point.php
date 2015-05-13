<?php
class Df_Pickup_Model_Form_Point extends Df_Core_Model_Form {
	/**
	 * @override
	 * @return int
	 */
	public function getId() {return intval($this->getField(Df_Pickup_Form_Point::FIELD__ID));}

	/**
	 * @override
	 * @return int
	 */
	public function getLocationId() {return rm_nat0($this->getField(Df_Pickup_Model_Point::P__LOCATION_ID));}

	/** @return string */
	public function getName() {return df_nts($this->getField(Df_Pickup_Model_Point::P__NAME));}

	/**
	 * @override
	 * @return string
	 */
	protected function getZendFormClass() {
		return Df_Pickup_Form_Point::_CLASS;
	}

	/**
	 * @param array $zendFormValues
	 * @return Df_Pickup_Model_Form_Point
	 */
	public static function i(array $zendFormValues) {
		return new self(array(self::P__ZEND_FORM_VALUES => $zendFormValues));
	}
}