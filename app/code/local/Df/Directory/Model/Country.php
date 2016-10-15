<?php
/**
 * @method Df_Directory_Model_Resource_Country getResource()
 */
class Df_Directory_Model_Country extends Mage_Directory_Model_Country {
	/** @return Df_Localization_Morpher_Response|null */
	public function getMorpher() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set(
				Df_Localization_Morpher::s()->getResponseSilent($this->getNameLocalized())
			);
		}
		return df_n_get($this->{__METHOD__});
	}

	/**
	 * @param string $case
	 * @param string $defaultTemplate
	 * @return string
	 */
	public function getNameInCase($case, $defaultTemplate) {
		if (!isset($this->{__METHOD__}[$case])) {
			/** @var string $result */
			$result = dfa($this->getNameInCases(), $case);
			if (!$result && $this->getMorpher()) {
				$result = $this->getMorpher()->getInCase($case);
			}
			$this->{__METHOD__}[$case] =
				$result ? $result : sprintf('%s «%s»', $defaultTemplate, $this->getNameLocalized())
			;
		}
		return $this->{__METHOD__}[$case];
	}

	/** @return string */
	public function getNameInCaseAccusative() {return $this->getNameInCase('accusative', 'страну');}

	/** @return string */
	public function getNameInCaseDative() {return $this->getNameInCase('dative', 'стране');}

	/** @return string */
	public function getNameInCaseGenitive() {return $this->getNameInCase('genitive', 'страны');}

	/** @return string */
	public function getNameInFormDestination() {
		return
			!is_null($this->getMorpher())
			? $this->getMorpher()->getInFormDestination()
			: 'в ' . $this->getNameInCaseAccusative()
		;
	}

	/** @return string */
	public function getNameInFormOrigin() {
		return
			!is_null($this->getMorpher())
			? $this->getMorpher()->getInFormOrigin()
			: 'из ' . $this->getNameInCaseGenitive()
		;
	}

	/** @return string */
	public function getNameLocalized() {return rm_country_ctn($this->getIso2Code());}

	/** @return string */
	public function getNameRussian() {return rm_country_ctn_ru($this->getIso2Code());}

	/**
	 * @return int
	 * http://zend-framework.sourcearchive.com/documentation/1.9.4/classZend__Locale__DataTest_a50ab83cf3da1666114bf6f5f762406d7.html
	 */
	public function getNumericCode() {
		/** @var array(string => string) $map */
		static $map; if (!$map) {$map = Zend_Locale::getTranslationList('numerictoterritory');}
		return df_int(dfa($map, $this->getIso2Code()));
	}

	/**
	 * @override
	 * @return Df_Directory_Model_Resource_Region_Collection
	 */
	public function getRegionCollection() {
		return Df_Directory_Model_Region::c()->addCountryFilter($this->getId());
	}
	
	/** @return Df_Directory_Model_Resource_Region_Collection */
	public function getRegionsCached() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getRegionCollection();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return Df_Directory_Model_Resource_Country_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/**
	 * @override
	 * @return Df_Directory_Model_Resource_Country
	 */
	public function _getResource() {return Df_Directory_Model_Resource_Country::s();}

	/** @return array(string => string) */
	private function getNameInCases() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				dfa(
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
	 * @used-by Df_Core_Format_MobilePhoneNumber::_construct()
	 * @used-by Df_Directory_Model_Resource_Country_Collection::_construct()
	 * @used-by Df_RussianPost_Model_Official_Request_International::_construct()
	 */


	/** @return Df_Directory_Model_Resource_Country_Collection */
	public static function c() {return new Df_Directory_Model_Resource_Country_Collection;}
	/** @return Df_Directory_Model_Resource_Country_Collection */
	public static function cs() {return Df_Directory_Model_Resource_Country_Collection::s();}
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
	/** @return Df_Directory_Model_Country */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}