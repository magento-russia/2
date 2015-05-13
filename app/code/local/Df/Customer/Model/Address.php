<?php
/**
 * @method Df_Customer_Model_Address setStreetFull()
 * @method Df_Customer_Model_Address setCustomerId()
 * @method Df_Customer_Model_Address setIsDefaultBilling()
 * @method Df_Customer_Model_Address setIsDefaultShipping()
 * @method Df_Customer_Model_Address setSaveInAddressBook()
 */
class Df_Customer_Model_Address extends Mage_Customer_Model_Address {
	/** @return string|null */
	public function getNameFirst() {return $this->_getData(self::P__NAME_FIRST);}

	/** @return string|null */
	public function getNameLast() {return $this->_getData(self::P__NAME_LAST);}

	/** @return string|null */
	public function getNameMiddle() {return $this->_getData(self::P__NAME_MIDDLE);}

	/**
	 * @param string|null $value
	 * @return Df_Customer_Model_Address
	 */
	public function setCity($value) {
		if (!is_null($value)) {
			df_param_string($value, 0);
		}
		$this->setData(self::P__CITY, $value);
		return $this;
	}

	/**
	 * @param string|null $value
	 * @return Df_Customer_Model_Address
	 */
	public function setCountryId($value) {
		df_param_string_not_empty($value, 0);
		$this->setData(self::P__COUNTRY_ID, $value);
		return $this;
	}

	/**
	 * @param string|null $value
	 * @return Df_Customer_Model_Address
	 */
	public function setNameFirst($value) {
		if (!is_null($value)) {
			df_param_string($value, 0);
		}
		$this->setData(self::P__NAME_FIRST, $value);
		return $this;
	}

	/**
	 * @param string|null $value
	 * @return Df_Customer_Model_Address
	 */
	public function setNameLast($value) {
		if (!is_null($value)) {
			df_param_string($value, 0);
		}
		$this->setData(self::P__NAME_LAST, $value);
		return $this;
	}

	/**
	 * @param string|null $value
	 * @return Df_Customer_Model_Address
	 */
	public function setNameMiddle($value) {
		if (!is_null($value)) {
			df_param_string($value, 0);
		}
		$this->setData(self::P__NAME_MIDDLE, $value);
		return $this;
	}

	/**
	 * @param string|null $value
	 * @return Df_Customer_Model_Address
	 */
	public function setPostcode($value) {
		if (!is_null($value)) {
			df_param_string($value, 0);
		}
		$this->setData(self::P__POSTCODE, $value);
		return $this;
	}

	/**
	 * @param string|null $value
	 * @return Df_Customer_Model_Address
	 */
	public function setRegion($value) {
		if (!is_null($value)) {
			df_param_string($value, 0);
		}
		$this->setData(self::P__REGION, $value);
		return $this;
	}

	/**
	 * @param int|null|string $value
	 * @return Df_Customer_Model_Address
	 */
	public function setRegionId($value) {
		if (!$value) {
			$value = null;
		}
		if (!is_null($value)) {
			df_param_integer($value, 0);
		}
		$this->setData(self::P__REGION_ID, $value);
		return $this;
	}

	/**
	 * @param string|null $value
	 * @return Df_Customer_Model_Address
	 */
	public function setTelephone($value) {
		if (!is_null($value)) {
			df_param_string($value, 0);
		}
		$this->setData(self::P__TELEPHONE, $value);
		return $this;
	}

	/**
	 * Цель перекрытия —
	 * учёт настроек видимости и обязательности для заполнения полей оформления заказа
	 * модуля «Удобная настройка витрины».
	 * @override
	 * @return bool|array
	 */
	public function validate() {
		/** @var string|null $addressType */
		$addressType = $this->getDataUsingMethod('address_type');
		/** @var bool|array $result */
		$result =
				is_null($addressType)
			||
				!df_cfg()->checkout()->field()->getApplicabilityByAddressType($addressType)->isEnabled()
			? parent::validate()
			: $this->getValidator()->validate()
		;
		if (!is_array($result)) {
			df_result_boolean($result);
		}
		return $result;
	}

	/** @return Df_Customer_Model_Address_Validator */
	private function getValidator() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Customer_Model_Address_Validator::i($this);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Customer_Model_Resource_Address::mf());
	}
	const _CLASS = __CLASS__;
	const P__CITY = 'city';
	const P__COUNTRY_ID = 'country_id';
	const P__NAME_FIRST = 'firstname';
	const P__NAME_LAST = 'lastname';
	const P__NAME_MIDDLE = 'middlename';
	const P__POSTCODE = 'postcode';
	const P__REGION = 'region';
	const P__REGION_ID = 'region_id';
	const P__TELEPHONE = 'telephone';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Customer_Model_Address
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Customer_Model_Address
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
	/**
	 * @see Df_Customer_Model_Resource_Address_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
}