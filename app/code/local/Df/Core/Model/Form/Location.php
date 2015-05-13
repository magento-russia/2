<?php
class Df_Core_Model_Form_Location extends Df_Core_Model_Form {
	/** @return string */
	public function getCity() {
		return df_nts($this->getField(Df_Core_Model_Location::P__CITY));
	}

	/** @return string */
	public function getStreetAddress() {
		return df_nts($this->getField(Df_Core_Model_Location::P__STREET_ADDRESS));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getZendFormClass() {
		return Df_Core_Form_Location::_CLASS;
	}

	/**
	 * @param array $zendFormValues
	 * @return Df_Core_Model_Form_Location
	 */
	public static function i(array $zendFormValues) {
		return new self(array(self::P__ZEND_FORM_VALUES => $zendFormValues));
	}
}