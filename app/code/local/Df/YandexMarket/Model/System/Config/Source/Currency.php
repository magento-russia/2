<?php
class Df_YandexMarket_Model_System_Config_Source_Currency
	extends Df_Admin_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return $this->getAsOptionArray();
	}

	/** @return string[][] */
	private function getAsOptionArray() {
		/**
		 * Здесь кэшировать результат можно,
		 * потому что у класса нет параметров.
		 */
		if (!isset($this->{__METHOD__})) {
			/** @var string[][] $optionCurrenciesAll */
			$optionCurrenciesAll = Mage::app()->getLocale()->getOptionCurrencies();
			/** @var string[] $optionCurrencyMap */
			$optionCurrencyMap =
				array_combine(
					df_column($optionCurrenciesAll, 'value')
					,df_column($optionCurrenciesAll,'label')
				)
			;
			/** @var string[][] $result */
			$result = array();
			foreach (self::getAllowedCurrencies() as $currencyCode) {
				/** @var string $currencyCode */
				/** @var string|null $label */
				$label = df_a($optionCurrencyMap, $currencyCode);
				if (!is_null($label)) {
					$result[]=
						array(
							'value' => $currencyCode
							,'label' => $label
						)
					;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	/**
	 * «В качестве основной валюты (для которой установлено rate="1")
	 * могут быть использованы только рубль (RUR, RUB),
	 * белорусский рубль (BYR), гривна (UAH) или тенге (KZT).»
	 * @link http://help.yandex.ru/partnermarket/?id=1111480
	 * @return array
	 */
	public static function getAllowedCurrencies() {return array('RUB', 'UAH', 'KZT', 'BYR');}
}