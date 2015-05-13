<?php
class Df_Directory_Model_Resource_Country_Collection
	extends Mage_Directory_Model_Mysql4_Country_Collection {
	/**
	 * @param string $iso2Code
	 * @param string|null $localeCode [optional]
	 * @return string
	 */
	public function getLocalizedNameByIso2Code($iso2Code, $localeCode = null) {
		if (!$localeCode) {
			$localeCode = Mage::app()->getLocale()->getLocaleCode();
		}
		/** @var string $result */
		$result = df_a($this->getMapFromIso2CodeToLocalizedName($localeCode), $iso2Code);
		if (!$result) {
			df_error(
				'Система не смогла узнать имя страны с кодом «%s» для локали «%s».'
				, $iso2Code
				, $localeCode
			);
		}
		return $result;
	}

	/**
	 * @param string|null $localeCode [optional]
	 * @return array(string => string)
	 */
	private function getMapFromIso2CodeToLocalizedName($localeCode = null) {
		if (!$localeCode) {
			$localeCode = Mage::app()->getLocale()->getLocaleCode();
		}
		if (!isset($this->{__METHOD__}[$localeCode])) {
			/** @var array(string => string) $result */
			$result = array();
			foreach ($this as $country) {
				/** @var Df_Directory_Model_Country $country */
				/** @var string $localizedName */
				$localizedName = null;
				/**
				 * При наличии в названии страны апострофа (например, «Кот-д'Ивуар»)
				 * в Magento CE 1.9.1.0 в методе @see Zend_Locale_Data::_findRoute()
				 * происходит сбой «SimpleXMLElement::xpath(): Invalid predicate»
				 * при вызове @see SimpleXMLElement::xpath()
				 * для выражения вида
				 * /ldml/localeDisplayNames/territories/territory[@type='Кот-д'Ивуар'].
				 * Поэтому с апострофами надо что-то делать.
				 */
				if (!rm_contains($country->getName(), "'")) {
					$localizedName =
						Mage::app()->getLocale()->getLocale()->getTranslation(
							$country->getName(), 'country', $localeCode
						)
					;
				}
				if (!$localizedName) {
					$localizedName = $country->getName();
				}
				/** @var Mage_Directory_Model_Country $country */
				$result[$country->getIso2Code()] = $localizedName;
			}
			$this->{__METHOD__}[$localeCode] = $result;
		}
		return $this->{__METHOD__}[$localeCode];
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Directory_Model_Country::mf(), Df_Directory_Model_Resource_Country::mf());
	}
	const _CLASS = __CLASS__;

	/** @return Df_Directory_Model_Resource_Country_Collection */
	public static function i() {return new self;}
}