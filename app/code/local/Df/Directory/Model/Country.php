<?php
/**
 * @method Df_Directory_Model_Resource_Country getResource()
 */
class Df_Directory_Model_Country extends Mage_Directory_Model_Country {
	/** @return Df_Localization_Model_Morpher_Response|null */
	public function getMorpher() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				Df_Localization_Model_Morpher::s()->getResponseSilent($this->getNameLocalized())
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/**
	 * @param string $case
	 * @param string $defaultTemplate
	 * @return string
	 */
	public function getNameInCase($case, $defaultTemplate) {
		if (!isset($this->{__METHOD__}[$case])) {
			/** @var string $result */
			$result = df_a($this->getNameInCases(), $case);
			if (is_null($result)) {
				if (!is_null($this->getMorpher())) {
					$result =
						call_user_func(
							array($this->getMorpher(), df_concat('getInCase', ucfirst($case)))
						)
					;
				}
				if (is_null($result)) {
					$result = rm_sprintf('%s «%s»', $defaultTemplate, $this->getNameLocalized());
				}
			}
			df_result_string($result);
			$this->{__METHOD__}[$case] = $result;
		}
		return $this->{__METHOD__}[$case];
	}

	/** @return string */
	public function getNameInCaseAccusative() {
		return $this->getNameInCase('accusative', 'страну');
	}

	/** @return string */
	public function getNameInCaseDative() {
		return $this->getNameInCase('dative', 'стране');
	}

	/** @return string */
	public function getNameInCaseGenitive() {
		return $this->getNameInCase('genitive', 'страны');
	}

	/** @return string */
	public function getNameInFormDestination() {
		return
			!is_null($this->getMorpher())
			? $this->getMorpher()->getInFormDestination()
			: rm_sprintf('в %s', $this->getNameInCaseAccusative())
		;
	}

	/** @return string */
	public function getNameInFormOrigin() {
		return
			!is_null($this->getMorpher())
			? $this->getMorpher()->getInFormOrigin()
			: rm_sprintf('из %s', $this->getNameInCaseGenitive())
		;
	}

	/** @return string */
	public function getNameLocalized() {
		return $this->countryHelper()->getLocalizedNameByIso2Code($this->getIso2Code());
	}

	/** @return string */
	public function getNameRussian() {
		return $this->countryHelper()->getLocalizedNameByIso2Code($this->getIso2Code(), 'ru_RU');
	}

	/**
	 * @return int
	 * @link http://zend-framework.sourcearchive.com/documentation/1.9.4/classZend__Locale__DataTest_a50ab83cf3da1666114bf6f5f762406d7.html
	 */
	public function getNumericCode() {
		/** @var array(string => string) */
		static $numericCodeMap;
		if (!isset($numericCodeMap)) {
			$numericCodeMap = Zend_Locale::getTranslationList('numerictoterritory');
		}
		return rm_int(df_a($numericCodeMap, $this->getIso2Code()));
	}

	/**
	 * @override
	 * @return Df_Directory_Model_Resource_Region_Collection
	 */
	public function getRegionCollection() {
		/** @var Df_Directory_Model_Resource_Region_Collection $result */
		$result = Df_Directory_Model_Resource_Region_Collection::i();
		$result->addCountryFilter($this->getId());
		return $result;
	}
	
	/** @return Df_Directory_Model_Resource_Region_Collection */
	public function getRegionsCached() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getRegionCollection();
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string) */
	private function getNameInCases() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_a(
					df_h()->directory()->country()->getMapFromIso2CodeToNameCases()
					,$this->getIso2Code()
					,array()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function isRussia() {
		return Df_Directory_Helper_Country::ISO_2_CODE__RUSSIA === $this->getIso2Code();
	}

	/** @return Df_Directory_Helper_Country */
	private function countryHelper() {return df_h()->directory()->country();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Directory_Model_Resource_Country::mf());
	}
	const _CLASS = __CLASS__;

	/** @return Df_Directory_Model_Resource_Country_Collection */
	public static function c() {return self::s()->getCollection();}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Directory_Model_Country
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param string $isoCode
	 * @return Df_Directory_Model_Country
	 */
	public static function ld($isoCode) {return self::i()->loadByCode($isoCode);}
	/**
	 * @see Df_Directory_Model_Resource_Country_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_Directory_Model_Country */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}