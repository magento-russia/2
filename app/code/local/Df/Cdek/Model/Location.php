<?php
class Df_Cdek_Model_Location extends Df_Shipping_Model_Location {
	/** @return string */
	public function getCity() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->normalizeName($this->cfg(self::P__CITY));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getCountry() {return $this->cfg(self::P__COUNTRY);}

	/**
	 * @override
	 * @return int
	 */
	public function getId() {return $this->cfg(self::P__ID);}

	/**
	 * @override
	 * @return string
	 */
	public function getRegion() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				in_array($this->getCity(), $this->normalizeName(array('Минск')))
				? $this->getCity()
				: $this->normalizeRegionName($this->cfg(self::P__REGION))
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * СДЭК может вернуть название населенного пункта в формате
	 * «Киев, Киевская обл., Украина»
	 * http://api.edostavka.ru/city/getListByTerm/jsonp.php?q=Киев
	 * @override
	 * @param string $name
	 * @return string
	 */
	protected function normalizeNameSingle($name) {
		return parent::normalizeNameSingle(df_trim(rm_first(df_csv_parse($name))));
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__COUNTRY, RM_V_STRING)
			->_prop(self::P__ID, RM_V_NAT0)
		;
	}
	/** @used-by Df_Cdek_Model_Map::requestLocationsFromServer() */
	const _C = __CLASS__;
	const P__CITY = 'cityName';
	const P__COUNTRY = 'countryName';
	const P__ID = 'id';
	const P__NAME = 'name';
	const P__REGION = 'regionName';
	/**
	 * @static
	 * @param array(string => string|int|null) $locationAsArray
	 * @return Df_Cdek_Model_Location
	 */
	public static function i(array $locationAsArray) {return new self($locationAsArray);}
}