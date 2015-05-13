<?php
/**
 * @method Df_Core_Model_Resource_Location getResource()
 */
class Df_Core_Model_Location extends Df_Core_Model_Entity {
	/** @return string|null */
	public function getCity() {return $this->cfg(self::P__CITY);}

	/** @return Df_Directory_Model_Country */
	public function getCountry() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Directory_Model_Country::i();
			$this->{__METHOD__}->loadByCode($this->getCountryIso2Code());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getCountryIso2Code() {return $this->cfg(self::P__COUNTRY_ISO2);}

	/** @return float */
	public function getLatitude() {return $this->cfg(self::P__LATITUDE);}

	/** @return float */
	public function getLongitude() {return $this->cfg(self::P__LONGITUDE);}

	/**
	 * @override
	 * @return string
	 */
	public function getName() {
		return is_null($this->getId()) ? 'Место' : rm_sprintf('Место №%d', $this->getId());
	}

	/** @return string|null */
	public function getPostalCode() {return $this->cfg(self::P__POSTAL_CODE);}

	/** @return Df_Directory_Model_Region|null */
	public function getRegion() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				!$this->getRegionId()
				? null
				: Df_Directory_Model_Region::ld($this->getRegionId())
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return int */
	public function getRegionId() {return $this->cfg(self::P__REGION_ID);}

	/** @return string */
	public function getRegionName() {
		return
			!is_null($this->getRegion())
			? $this->getRegion()->getName()
			: $this->cfg(self::P__REGION_NAME)
		;
	}

	/** @return string|null */
	private function getStreetAddress() {return $this->cfg(self::P__STREET_ADDRESS);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Core_Model_Resource_Location::mf());
		$this
			->_prop(self::P__LATITUDE, self::V_FLOAT)
			->_prop(self::P__LONGITUDE, self::V_FLOAT)
			->_prop(self::P__REGION_ID, self::V_NAT0)
		;
	}
	const _CLASS = __CLASS__;
	const P__CITY = 'city';
	const P__COUNTRY_ISO2 = 'country_iso2';
	const P__ID = 'location_id';
	const P__LATITUDE = 'latitude';
	const P__LONGITUDE = 'longitude';
	const P__PHONE = 'phone';
	const P__POSTAL_CODE = 'postal_code';
	const P__REGION_ID = 'region_id';
	const P__REGION_NAME = 'region_name';
	const P__STREET_ADDRESS = 'street_address';

	/** @return Df_Core_Model_Resource_Location_Collection */
	public static function c() {return self::s()->getCollection();}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Core_Model_Location
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Core_Model_Location
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
	/**
	 * @see Df_Core_Model_Resource_Location_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_Core_Model_Location */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}