<?php
/**
 * @method string|null getAddressType()
 * @method string|null getFirstname()
 * @method string|null getLastname()
 * @method string|null getMiddlename()
 * @method Df_Customer_Model_Address setCity(string $value)
 * @method Df_Customer_Model_Address setCountryId(string $value)
 * @method Df_Customer_Model_Address setCustomerId(int $value)
 * @method Df_Customer_Model_Address setFirstname(string $value)
 * @method Df_Customer_Model_Address setIsDefaultBilling(bool $value)
 * @method Df_Customer_Model_Address setIsDefaultShipping(bool $value)
 * @method Df_Customer_Model_Address setLastname(string $value)
 * @method Df_Customer_Model_Address setMiddlename(string $value)
 * @method Df_Customer_Model_Address setPostcode(string $value)
 * @method Df_Customer_Model_Address setRegion(string $value)
 * @method Df_Customer_Model_Address setRegionId(int $value)
 * @method Df_Customer_Model_Address setSaveInAddressBook(bool $value)
 * @method Df_Customer_Model_Address setStreetFull(string $value)
 * @method Df_Customer_Model_Address setTelephone(string $value)
 */
class Df_Customer_Model_Address extends Mage_Customer_Model_Address {
	/**
	 * @override
	 * @return Df_Customer_Model_Resource_Address_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/**
	 * Цель перекрытия —
	 * учёт настроек видимости и обязательности для заполнения полей оформления заказа
	 * модуля «Удобная настройка витрины».
	 * @override
	 * @return bool|array
	 */
	public function validate() {
		/** @var string|null $addressType */
		$addressType = $this->getAddressType();
		/** @var bool|array $result */
		$result =
				!$addressType
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

	/**
	 * @override
	 * @return Df_Customer_Model_Resource_Address
	 */
	protected function _getResource() {return Df_Customer_Model_Resource_Address::s();}

	/** @return Df_Customer_Model_Address_Validator */
	private function getValidator() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Customer_Model_Address_Validator::i($this);
		}
		return $this->{__METHOD__};
	}

	/** @used-by Df_Customer_Model_Resource_Address_Collection::_construct() */
	const _C = __CLASS__;
	const P__CITY = 'city';
	const P__COUNTRY_ID = 'country_id';
	const P__FIRSTNAME = 'firstname';
	const P__LASTNAME = 'lastname';
	const P__MIDDLENAME = 'middlename';
	const P__POSTCODE = 'postcode';
	const P__REGION = 'region';
	const P__REGION_ID = 'region_id';
	const P__TELEPHONE = 'telephone';

	/** @return Df_Customer_Model_Resource_Address_Collection */
	public static function c() {return new Df_Customer_Model_Resource_Address_Collection;}
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
}