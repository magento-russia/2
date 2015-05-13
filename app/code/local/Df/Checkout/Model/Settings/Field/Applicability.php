<?php
class Df_Checkout_Model_Settings_Field_Applicability extends Df_Core_Model_Settings_Group {
	/** @return string */
	public function confirm_password() {
		/** @var string $result */
		$result = $this->getValue(Df_Checkout_Const_Field::CONFIRM_PASSWORD);
		df_result_string($result);
		return $result;
	}

	/** @return string */
	public function customer_password() {
		/** @var string $result */
		$result = $this->getValue(Df_Checkout_Const_Field::CUSTOMER_PASSWORD);
		df_result_string($result);
		return $result;
	}

	/** @return bool */
	public function isEnabled() {
		return $this->getYesNo('enabled');
	}

	/** @return string */
	public function region() {
		/** @var string $result */
		$result = $this->getValue(Df_Checkout_Const_Field::REGION);
		df_result_string($result);
		return $result;
	}

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
	private function getAddressType() {
		/** @var string $result */
		$result = $this->cfg(self::P__ADDRESS_TYPE);
		df_result_string($result);
		return $result;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__ADDRESS_TYPE, self::V_STRING_NE)
			->addData(array(self::P__SECTION => 'df_checkout'))
		;
	}
	const _CLASS = __CLASS__;
	const P__ADDRESS_TYPE = 'address_type';
	/**
	 * @static
	 * @param string $addressType
	 * @return Df_Checkout_Model_Settings_Field_Applicability
	 */
	public static function i($addressType) {return new self(array(self::P__ADDRESS_TYPE => $addressType));}
}