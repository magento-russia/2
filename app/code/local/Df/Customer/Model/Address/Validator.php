<?php
class Df_Customer_Model_Address_Validator extends Df_Core_Model_Abstract {
	/** @return bool|array */
	public function validate() {
		df_assert(!is_null($this->getApplicabilityManager()));
		/** @var bool|array $result */
		$result = true;
		$this->getAddress()->implodeStreetAddress();
		foreach ($this->getValidationMap() as $field => $errorMessage) {
			$this->validateNotEmpty($field, $errorMessage);
		}
		if (
				(
						Df_Checkout_Model_Config_Source_Field_Applicability::VALUE__REQUIRED
					===
						$this->getApplicabilityManager()->getValue(Df_Checkout_Const_Field::STREET)
				)
			&&
				!$this->getAddress()->getStreet(1)
		) {
			$this->getException()->addMessage(
				df_mage()->core()->messageSingleton()->error('Please enter the street.')
			);
		}
		/** @var array $_havingOptionalZip */
		$havingOptionalZip = df_mage()->directoryHelper()->getCountriesWithOptionalZip();
		if (
				(
						Df_Checkout_Model_Config_Source_Field_Applicability::VALUE__REQUIRED
					===
						$this->getApplicabilityManager()->getValue(Df_Checkout_Const_Field::POSTCODE)
				)
			&&
				!in_array($this->getAddress()->getDataUsingMethod('country_id'), $havingOptionalZip)
			&&
				!$this->getAddress()->getDataUsingMethod(Df_Checkout_Const_Field::POSTCODE)
		) {
			$this->getException()->addMessage(
				df_mage()->core()->messageSingleton()->error('Please enter the zip/postal code.')
			);
		}
		if (
				(
						Df_Checkout_Model_Config_Source_Field_Applicability::VALUE__REQUIRED
					===
						$this->getApplicabilityManager()->getValue(Df_Checkout_Const_Field::COUNTRY)
				)
			&&
				!$this->getAddress()->getDataUsingMethod('country_id')
		) {
			$this->getException()->addMessage(
				df_mage()->core()->messageSingleton()->error('Please enter the country.')
			);
		}
		if (
				(
						Df_Checkout_Model_Config_Source_Field_Applicability::VALUE__REQUIRED
					===
						$this->getApplicabilityManager()->getValue(Df_Checkout_Const_Field::REGION)
				)
			&&
				count($this->getAddress()->getCountryModel()->getRegionCollection())
			&&
				!$this->getAddress()->getRegionId()
		) {
			$this->getException()->addMessage(
				df_mage()->core()->messageSingleton()->error('Please enter the state/province.')
			);
		}
		if (0 < count($this->getException()->getMessages())) {
			$result = array();
			foreach ($this->getException()->getMessages() as $message) {
				/** @var Mage_Core_Model_Message_Abstract $message */
				$result[]= df_mage()->customerHelper()->__($message->getText());
			}
		}
		if (!is_array($result)) {
			df_result_boolean($result);
		}
		return $result;
	}

	/** @return Mage_Customer_Model_Address_Abstract */
	private function getAddress() {return $this->cfg(self::P__ADDRESS);}

	/** @return string|null */
	private function getAddressType() {
		/** @var string|null $result */
		$result = $this->getAddress()->getDataUsingMethod('address_type');
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	/** @return Df_Checkout_Model_Settings_Field_Applicability|null */
	private function getApplicabilityManager() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				!$this->getAddressType()
				? null
				: df_cfg()->checkout()->field()->getApplicabilityByAddressType($this->getAddressType())
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return Mage_Core_Exception */
	private function getException() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new Mage_Core_Exception();
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string) */
	private function getValidationMap() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array(
				Df_Checkout_Const_Field::FIRSTNAME => 'Please enter the first name.'
				,Df_Checkout_Const_Field::LASTNAME => 'Please enter the last name.'
				,Df_Checkout_Const_Field::CITY => 'Please enter the city.'
				,Df_Checkout_Const_Field::TELEPHONE => 'Please enter the telephone number.'
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $fieldName
	 * @param string $errorMessage
	 * @return Df_Customer_Model_Address_Validator
	 */
	private function validateNotEmpty($fieldName, $errorMessage) {
		if (
				(
						Df_Checkout_Model_Config_Source_Field_Applicability::VALUE__REQUIRED
					===
						$this->getApplicabilityManager()->getValue($fieldName)
				)
			&&
				!Zend_Validate::is($this->getAddress()->getDataUsingMethod($fieldName), 'NotEmpty')
		) {
			$this->getException()->addMessage(
				df_mage()->core()->messageSingleton()->error($errorMessage)
			);
		}
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__ADDRESS, Df_Customer_Const::ADDRESS_ABSTRACT_CLASS);
	}
	const _CLASS = __CLASS__;
	const P__ADDRESS = 'address';
	/**
	 * @static
	 * @param Mage_Customer_Model_Address_Abstract $address
	 * @return Df_Customer_Model_Address_Validator
	 */
	public static function i(Mage_Customer_Model_Address_Abstract $address) {
		return new self(array(self::P__ADDRESS => $address));
	}
}