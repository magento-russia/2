<?php
class Df_PonyExpress_Model_Location extends Df_Shipping_Model_Location {
	/** @return string */
	public function getCity() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->normalizeName(rm_first($this->asArray()));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getRegion() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->normalizeRegionName(df_nts(df_a($this->asArray(), 1)));
		}
		return $this->{__METHOD__};
	}
	
	/** @return string */
	public function asText() {return $this->cfg(self::P__AS_TEXT);}

	/**
	 * Информация о регионе отсутствует для Москвы и Санкт-Петербурга
	 * @return bool
	 */
	public function hasRegion() {return !!$this->getRegion();}

	/** @return string[] */
	public function asArray() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = explode(', ', $this->asText());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getRegionPartsToClean() {return array('AO', 'край', 'респ.', 'АР');}

	/**
	 * @override
	 * @return bool
	 */
	protected function isRegionCleaningCaseSensitive() {return true;}
	
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__AS_TEXT, self::V_STRING);
	}
	const _CLASS = __CLASS__;
	const P__AS_TEXT = 'as_text';
	/**
	 * @static
	 * @param string|null $locationAsText [optional]
	 * @return Df_PonyExpress_Model_Location
	 */
	public static function i($locationAsText = null) {
		return new self(array(self::P__AS_TEXT => $locationAsText));
	}
}