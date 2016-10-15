<?php
class Df_Directory_Helper_Country extends Mage_Core_Helper_Abstract {
	/** @return Df_Directory_Model_Country */
	public function getKazakhstan() {return df_country(self::ISO_2_CODE__KAZAKHSTAN);}

	/** @return array(string => array(string => string)) */
	public function getMapFromIso2CodeToNameCases() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_json_decode(file_get_contents(df_cc_path(
				Mage::getModuleDir('etc', df_module_name($this)), 'countries.json'
			)));
			df_result_array($this->{__METHOD__});
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
	public function getRussia() {return df_country(self::ISO_2_CODE__RUSSIA);}

	/** @return Df_Directory_Model_Country */
	public function getUkraine() {return df_country(self::ISO_2_CODE__UKRAINE);}

	/** @return Df_Directory_Helper_Country_Russia */
	public function russia() {return Df_Directory_Helper_Country_Russia::s();}

	/**
	 * 2-буквенные коды по стандарту ISO 3166-1 alpha-2
	 * наиболее часто используемых Российской сборкой Magento стран.
	 * https://ru.wikipedia.org/wiki/ISO_3166-1
	 */
	const ISO_2_CODE__BELARUS = 'BY';
	const ISO_2_CODE__CANADA = 'CA';
	const ISO_2_CODE__KAZAKHSTAN = 'KZ';
	const ISO_2_CODE__RUSSIA = 'RU';
	const ISO_2_CODE__UKRAINE = 'UA';
	const ISO_2_CODE__USA = 'US';

	/** @return Df_Directory_Helper_Country */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}