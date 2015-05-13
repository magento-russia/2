<?php
class Df_Directory_Helper_Country extends Mage_Core_Helper_Abstract {
	/**
	 * @param string $iso2Code
	 * @return Df_Directory_Model_Country
	 */
	public function getByIso2Code($iso2Code) {
		df_param_string($iso2Code, 0);
		if (!isset($this->{__METHOD__}[$iso2Code])) {
			/** @var Df_Directory_Model_Country $result */
			$result = Df_Directory_Model_Country::i();
			$result->loadByCode($iso2Code);
			if (is_null($result->getCountryId())) {
				df_error(
					'Неизвестный код страны: %s'
					,$iso2Code
				);
			}
			df_assert_eq($iso2Code, $result->getIso2Code());
			$this->{__METHOD__}[$iso2Code] = $result;
		}
		return $this->{__METHOD__}[$iso2Code];
	}

	/**
	 * @param string $name
	 * @return string|null
	 */
	public function getIso2CodeByName($name) {
		return df_a($this->getMapFromNameToIso2Code(), mb_strtoupper(df_trim($name)));
	}

	/** @return Df_Directory_Model_Country */
	public function getKazakhstan() {return $this->getByIso2Code(self::ISO_2_CODE__KAZAKHSTAN);}

	/**
	 * @param string $iso2Code
	 * @param string|null $localeCode [optional]
	 * @return string
	 */
	public function getLocalizedNameByIso2Code($iso2Code, $localeCode = null) {
		/** @var Df_Directory_Model_Resource_Country_Collection $collection */
		static $collection;
		if (!isset($collection)) {
			$collection = Df_Directory_Model_Resource_Country_Collection::i();
		}
		if (!$localeCode) {
			$localeCode = Mage::app()->getLocale()->getLocaleCode();
		}
		/** @var string $result */
		$result = $collection->getLocalizedNameByIso2Code($iso2Code, $localeCode);
		df_result_string($result);
		return $result;
	}

	/** @return array(string => array(string => string)) */
	public function getMapFromIso2CodeToNameCases() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Zend_Json::decode(
					file_get_contents(
						df_concat_path(
							Mage::getModuleDir(
								'etc'
								,df()->reflection()->getModuleName(get_class($this))
							)
							,'countries.json'
						)
					)
				)
			;
			df_result_array($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string) */
	private function getMapFromNameToIso2Code() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Directory_Model_Resource_Country_Collection $countries */
			$countries = Df_Directory_Model_Resource_Country_Collection::i();
			/** @var array(array(string => string)) $countriesAsOptions */
			$countriesAsOptions = $countries->toOptionArray($emptyLabel = false);
			/** @var array(string => string) $result  */
			foreach ($countriesAsOptions as $countryAsOption) {
				/** @var array(string => string) */
				$result[mb_strtoupper(df_a($countryAsOption, 'label'))] =
					df_a($countryAsOption, 'value')
				;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Mage_Directory_Model_Country $country
	 * @param Zend_Locale $locale
	 * @return string
	 */
	public function getName(Mage_Directory_Model_Country $country, Zend_Locale $locale) {
		return
			Mage::app()->getLocale()->getLocale()->getTranslation(
				$country->getId(), 'country', $locale
			)
		;
	}

	/**
	 * @param Mage_Directory_Model_Country $country
	 * @return string
	 */
	public function getNameEnglish(Mage_Directory_Model_Country $country) {
		return $this->getName($country, df_h()->directory()->getLocaleEnglish());
	}

	/** @return Df_Directory_Model_Country */
	public function getRussia() {return $this->getByIso2Code(self::ISO_2_CODE__RUSSIA);}

	/** @return Df_Directory_Model_Country */
	public function getUkraine() {return $this->getByIso2Code(self::ISO_2_CODE__UKRAINE);}

	/** @return Df_Directory_Helper_Country_Russia */
	public function russia() {return Df_Directory_Helper_Country_Russia::s();}

	const ISO_2_CODE__BELARUS = 'BY';
	const ISO_2_CODE__CANADA = 'CA';
	const ISO_2_CODE__KAZAKHSTAN = 'KZ';
	const ISO_2_CODE__RUSSIA = 'RU';
	const ISO_2_CODE__UKRAINE = 'UA';
	const ISO_2_CODE__USA = 'US';

	/** @return Df_Directory_Helper_Country */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}