<?php
class Df_Warehousing_Model_Form_Warehouse extends Df_Core_Model_Form {
	/**
	 * @override
	 * @return int
	 */
	public function getId() {return rm_nat0($this->getField(Df_Warehousing_Form_Warehouse::FIELD__ID));}

	/**
	 * @override
	 * @return int
	 */
	public function getLocationId() {
		return rm_nat0($this->getField(Df_Warehousing_Model_Warehouse::P__LOCATION_ID));
	}

	/** @return string */
	public function getName() {return df_nts($this->getField(Df_Warehousing_Model_Warehouse::P__NAME));}

	/**
	 * @override
	 * @return string
	 */
	protected function getZendFormClass() {return Df_Warehousing_Form_Warehouse::_CLASS;}

	/**
	 * @param array $zendFormValues
	 * @return Df_Warehousing_Model_Form_Warehouse
	 */
	public static function i(array $zendFormValues) {
		return new self(array(self::P__ZEND_FORM_VALUES => $zendFormValues));
	}
}