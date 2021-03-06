<?php
class Df_Shipping_Model_Origin extends Df_Core_Model_Abstract {
	/** @return string */
	public function getCity() {return $this->cfg(self::P__CITY);}

	/** @return Df_Directory_Model_Country|null */
	public function getCountry() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				!$this->getCountryId()
				? null
				: df_h()->directory()->country()->getByIso2Code($this->getCountryId())
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return string */
	public function getCountryId() {return $this->cfg(self::P__COUNTRY_ID);}

	/** @return string */
	public function getPostalCode() {return $this->cfg(self::P__POSTAL_CODE);}

	/** @return Df_Directory_Model_Region|null */
	private function getRegion() {
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

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__CITY, self::V_STRING, self::F_TRIM)
			->_prop(self::P__COUNTRY_ID, self::V_STRING, false)
			->_prop(self::P__POSTAL_CODE, self::V_STRING, self::F_TRIM)
			->_prop(self::P__REGION_NAME, self::V_STRING, self::F_TRIM)
			->_prop(self::P__REGION_ID, self::V_NAT0)
		;
	}
	const _CLASS = __CLASS__;
	const P__CITY = 'city';
	const P__COUNTRY_ID = 'country_id';
	const P__POSTAL_CODE = 'postal_code';
	const P__REGION_NAME = 'region_name';
	const P__REGION_ID = 'region_id';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Shipping_Model_Origin
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}