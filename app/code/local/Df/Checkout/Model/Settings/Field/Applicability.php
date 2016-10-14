<?php
class Df_Checkout_Model_Settings_Field_Applicability extends Df_Core_Model_Settings_Group {
	/** @return bool */
	public function isEnabled() {return $this->getYesNo('enabled');}

	/**
	 * @param string $field
	 * @return bool
	 */
	public function isRequired($field) {
		return
				Df_Checkout_Model_Config_Source_Field_Applicability::VALUE__REQUIRED
			===
				$this->getValue($field)
		;
	}

	/** @return string */
	public function region() {return $this->getValue(Df_Checkout_Const_Field::REGION);}

	/**
	 * @override
	 * @return string
	 */
	protected function getGroup() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getAddressType() . '_field_applicability';
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getAddressType() {return $this->cfg(self::P__ADDRESS_TYPE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__ADDRESS_TYPE, RM_V_STRING_NE)
			->addData(array(self::P__SECTION => 'df_checkout'))
		;
	}
	const _C = __CLASS__;
	const P__ADDRESS_TYPE = 'address_type';
	/**
	 * @static
	 * @param string $addressType
	 * @return Df_Checkout_Model_Settings_Field_Applicability
	 */
	public static function i($addressType) {return new self(array(self::P__ADDRESS_TYPE => $addressType));}
}