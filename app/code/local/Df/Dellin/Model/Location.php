<?php
class Df_Dellin_Model_Location extends Df_Shipping_Model_Location {
	/**
	 * @override
	 * @return string
	 */
	public function getCity() {return $this->normalizeName($this->cfg(self::P__CITY));}

	/**
	 * @override
	 * @return string
	 */
	public function getId() {return $this->cfg(self::P__ID);}
	
	/**
	 * @override
	 * @return string
	 */
	public function getRegion() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$this->{__METHOD__} = '';
			/** @var string $fullName */
			$fullName = $this->cfg(self::P__FULL_NAME);
			df_assert_string_not_empty($fullName);
			/** @var string|null $name */
			$name = rm_preg_match('#\(([^\)]+)\)#u', $fullName, false);
			if (!is_null($name)) {
				/**
				 * Информация о регионе может отсутствовать, например, для Москвы:
				 * в этом случае в качестве fullName сервер возвращает «г. Москва».
				 */
				$this->{__METHOD__} = $this->normalizeRegionName($name);
			}
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function hasTerminal() {return $this->cfg(self::P__IS_TERMINAL);}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getRegionPartsToClean() {return array('АО', 'край', 'обл.', 'Респ.');}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getRegionReplacementMap() {
		return array_merge(
			parent::getRegionReplacementMap()
			, array('Саха /Якутия/' => 'Саха (Якутия)')
		);
	}

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
		$this
			->_prop(self::P__ID, RM_V_STRING)
			->_prop(self::P__IS_TERMINAL, RM_V_BOOL)
		;
	}
	const _C = __CLASS__;
	const P__CITY = 'city';
	const P__FULL_NAME = 'fullName';
	const P__ID = 'code';
	const P__IS_TERMINAL = 'isTerminal';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Dellin_Model_Location
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}