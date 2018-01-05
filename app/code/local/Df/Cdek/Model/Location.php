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
	 * @override
	 * @see Df_Shipping_Model_Location::normalizeNameSingle()
	 * @used-by Df_Shipping_Model_Location::normalizeName()
	 * @param string $name
	 * @return string
	 */
	public function normalizeNameSingle($name) {
		return
			parent::normalizeNameSingle(
				df_trim(
					/**
					 * СДЭК может вернуть название населенного пункта в формате
					 * «Киев, Киевская обл., Украина»
					 * @link http://api.edostavka.ru/city/getListByTerm/jsonp.php?q=Киев
					 */
					rm_first(explode(',', $name))
				)
			)
		;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__COUNTRY, self::V_STRING)
			->_prop(self::P__ID, self::V_NAT0)
		;
	}
	const _CLASS = __CLASS__;
	const P__CITY = 'cityName';
	const P__COUNTRY = 'countryName';
	const P__ID = 'id';
	const P__NAME = 'name';
	const P__REGION = 'regionName';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Cdek_Model_Location
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}