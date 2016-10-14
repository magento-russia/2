<?php
class Df_Spsr_Model_Location extends Df_Shipping_Model_Location {
	/** @return string */
	public function getCountry() {return $this->cfg(self::P__COUNTRY);}

	/**
	 * Обратите внимание, что результатом может быть не число, а строка вида: «63249745|3»
	 * @override
	 * @return string
	 */
	public function getId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = implode('|', array(
				$this->cfg(self::P__ID), $this->cfg(self::P__CITY_OWNER_ID)
			));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getLabel() {return $this->cfg(self::P__LABEL);}

	/** @return string */
	public function getLocation() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->normalizeRegionName($this->cfg(self::P__VALUE));
		}
		return $this->{__METHOD__};
	}
	
	/**
	 * @override
	 * @return string
	 */
	public function getRegion() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = null;
			if (in_array($this->getLocation(), $this->normalizeName(array('Москва', 'Санкт-Петербург')))) {
				$result = $this->getLocation();
			}
			else {
				$result = $this->normalizeRegionName($this->cfg(self::P__REGION));
				if ($this->normalizeName('Белоруссия') === $result) {
					$result = $this->normalizeName('Беларусь');
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__CITY_OWNER_ID, RM_V_INT)
			->_prop(self::P__COUNTRY, RM_V_STRING)
			->_prop(self::P__ID, RM_V_INT)
		;
	}
	const _C = __CLASS__;
	const P__CITY_OWNER_ID = 'city_owner_id';
	const P__COUNTRY = 'country';
	const P__ID = 'id';
	const P__LABEL = 'label';
	const P__REGION = 'region';
	const P__VALUE = 'value';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Spsr_Model_Location
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}