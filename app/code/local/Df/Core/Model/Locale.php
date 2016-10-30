<?php
class Df_Core_Model_Locale extends Mage_Core_Model_Locale {
	/**
	 * Цель перекрытия —
	 * предотвращение возможного сбоя при обработке информации о валюте.
	 * @override
	 * @param string $currency
	 * @return Zend_Currency
	 */
	public function currency($currency) {
		try {
			$result = parent::currency($currency);
		}
		catch (Exception $e) {
			$result = $this->currencyDf($currency);
		}
		return $result;
	}

	/**
	 * Цель перекрытия:
	 *
	 * Родительский метод почему-то возвращает результат-константу 'USD'.
	 * Наш метод возвращает ту валюту,
	 * которая указана в системных настройках валютой по умолчанию
	 * (а для российских магазинов нам нужен рубль).
	 *
	 * Данная функциональность играет роль
	 * при установке Magento CE одновременно с Российской сборкой Magento:
	 * тогда предустановленным значением учётной валюты магазина
	 * будет российский рубль, а не доллар США
	 * (при установке Magento CE без Росссийской сборки Magento
	 * предустановленным значением учётной валюты магазина будет доллар США).
	 *
	 * @override
	 * @return string
	 */
	public function getCurrency() {return
		df_cfg(self::XML_PATH_DEFAULT_CURRENCY) ?: parent::getCurrency()
	;}

	/**
	 * Цель перекрытия —
	 * предоставление администратору возможности скрытия копеек из сумм на витрине.
	 * В данном методе влияние оказыается на суммы, показываемые посредством JavaScript.
	 * @override
	 * @return array(string => string|int)
	 */
	public function getJsPriceFormat() {
		/** @var array(string => string|int) $result */
		$result = parent::getJsPriceFormat();
		if (df_loc()->needHideDecimals()) {
			$result['requiredPrecision'] = 0;
		}
		return $result;
	}

	/**
	 * Цель перекрытия —
	 * русификация списка часовых поясов.
	 *
	 * Данная функциональность играет роль
	 * при установке Magento CE одновременно с Российской сборкой Magento:
	 * тогда система покажет выпадающий список часовых поясов на русском языке,
	 * причём предустановленным значением будет московский часовой пояс
	 * (при установке Magento CE без Российской сборки Magento
	 * выпадающий список часовых поясов будет на английскком языке,
	 * и предустановленным значенем будет не то, что нужно).
	 *
	 * @override
	 * @return array(array(string => string))
	 */
	public function getOptionTimezones() {
		$options = array();
		// НАЧАЛО ЗАПЛАТКИ
		// заменяем 'windowstotimezone' на 'citytotimezone'
		$zones = $this->getTranslationList('citytotimezone');
		// КОНЕЦ ЗАПЛАТКИ
		ksort($zones);
		foreach ($zones as $code => $name) {
			$name = trim($name);
			$options[]= df_option($code, empty($name) ? $code : $name . ' (' . $code . ')');
		}
		return $this->_sortOptionArray($options);
	}

	/**
	 * Цель перекрытия —
	 * предустановка московского часового пояса
	 * при установке Magento CE одновременно с Российской сборкой Magento
	 * (при установке Magento CE без Российской сборки Magento
	 * предустановленным значенем будет не то, что нужно (UTC)).
	 *
	 * @override
	 * @return string
	 */
	public function getTimezone() {return
		df_cfg(self::XML_PATH_DEFAULT_TIMEZONE) ?: parent::getTimezone()
	;}

	/**
	 * Цель перекрытия —
	 * вместо того, чтобы вываливать перед администратором список из 200 языков,
	 * на которых система по умолчанию будет отображать интерфейс,
	 * оставляем в этом списке только 3 разумных: русский, английский и украинский
	 * @override
	 * @return array(array(string => string))
	 */
	public function getTranslatedOptionLocales() {
		return df_map_to_options(array('ru_RU' => 'Русский', 'en_US' => 'English'));
	}

	/**
	 * @param  string $currency
	 * @return  Zend_Currency
	 */
	private function currencyDf($currency) {
		Varien_Profiler::start('locale/currency');
		if (!isset(self::$_currencyCache[$this->getLocaleCode()][$currency])) {
			try {
				$currencyObject = new Zend_Currency(array('currency' => $currency), $this->getLocale());
			}
			catch (Exception $e) {
				$currencyObject =
					new Zend_Currency(
						array('currency' => $this->getCurrency()), $this->getLocale()
					)
				;
				$options = array(
					'name' => $currency
					,'currency' => $currency
					,'symbol' => $currency
				);
				$currencyObject->setFormat($options);
			}
			self::$_currencyCache[$this->getLocaleCode()][$currency] = $currencyObject;
		}
		Varien_Profiler::stop('locale/currency');
		return self::$_currencyCache[$this->getLocaleCode()][$currency];
	}

	const XML_PATH_DEFAULT_CURRENCY = 'general/locale/currency';
}