<?php
use Df_Directory_Model_Currency as Currency;
use Mage_Core_Model_Store as Store;
use Mage_Sales_Model_Order as O;

/**
 * 2015-12-28
 * @param int|string|null|bool|Store $store [optional]
 * @return string[]
 */
function df_currencies_codes_allowed($store = null) {
	return df_store($store)->getAvailableCurrencyCodes(true);
}

/**
 * 2016-07-04
 * «How to load a currency by its ISO code?» https://mage2.pro/t/1840
 * @param Currency|string|null $currency [optional]
 * @return Currency
 */
function df_currency($currency = null) {
	/** @var Currency $result */
	if (!$currency) {
		$result = df_currency_base();
	}
	else if ($currency instanceof Currency) {
		$result = $currency;
	}
	else {
		/** @var array(string => Currency) $cache */
		static $cache;
		if (!isset($cache[$currency])) {
			$cache[$currency] = Currency::ld($currency);
		}
		$result = $cache[$currency];
	}
	return $result;
}

/**
 * 2016-07-04
 * «How to programmatically get the base currency's ISO code for a store?» https://mage2.pro/t/1841
 * @param null|string|int|Store|O $scope [optional]
 * @return Currency
 */
function df_currency_base($scope = null) {
	if ($scope instanceof O) {
		$scope = $scope->getStore();
	}
	/** @var string $code */
	$code = df_cfg(Currency::XML_PATH_CURRENCY_BASE, $scope);
	df_assert_string_not_empty($code);
	return df_currency($code);
}

/**
 * 2016-09-05
 * @param null|string|int|Store $scope [optional]
 * @return string
 */
function df_currency_base_c($scope = null) {return df_currency_base($scope)->getCode();}

/**
 * 2016-07-04
 * @param Currency|string|null $currency [optional]
 * @return string
 */
function df_currency_code($currency = null) {return df_currency($currency)->getCode();}

/**
 * 2016-07-04
 * «How to programmatically convert a money amount from a currency to another one?» https://mage2.pro/t/1842
 * 2016-09-05
 * Обратите внимание, что перевод из одной валюты в другую
 * надо осуществлять только в направлении 'базовая валюта' => 'второстепенная валюта',
 * но не наоборот
 * (Magento не умеет выполнять первод 'второстепенная валюта' => 'базовая валюта'
 * даже при наличии курса 'базовая валюта' => 'второстепенная валюта',
 * и возбуждает исключительную ситуацию).
 *
 * Курс валюты сау на себя в системе всегда есть:
 * @see Mage_Directory_Model_Resource_Currency::getRate()
 * https://github.com/magento/magento2/blob/2.1.1/app/code/Magento/Directory/Model/ResourceModel/Currency.php#L56-L58
 *
 * @uses Mage_Directory_Model_Currency::convert() прекрасно понимает нулевой $to:
 * https://github.com/magento/magento2/blob/2.1.1/app/code/Magento/Directory/Model/Currency.php#L216-L217
 *
 * @param float $amount
 * @param Currency|string|null $from [optional]
 * @param Currency|string|null $to [optional]
 * @param null|string|int|Store $scope [optional]
 * @return float
 */
function df_currency_convert($amount, $from = null, $to = null, $scope = null) {return
	df_currency_convert_from_base(df_currency_convert_to_base($amount, $from, $scope), $to, $scope)
;}

/**
 * 2016-09-05
 * @param float $amount
 * @param Currency|string|null $to
 * @param null|string|int|Store $scope [optional]
 * @return float
 */
function df_currency_convert_from_base($amount, $to, $scope = null) {return
	df_currency_base($scope)->convert($amount, $to)
;}

/**
 * 2016-09-05
 * @param float $amount
 * @param Currency|string|null $from
 * @param null|string|int|Store $scope [optional]
 * @return float
 */
function df_currency_convert_to_base($amount, $from, $scope = null) {return
	$amount / df_currency_base($scope)->convert(1, $from)
;}

/**
 * 2016-08-08
 * http://magento.stackexchange.com/a/108013
 * В отличие от @see df_currency_base() здесь мы вынуждены использовать не $scope, а $store,
 * потому что учётную валюту можно просто считать из настроек,
 * а текущая валюта может меняться динамически (в том числе посетителем магазина и сессией).
 * @param int|string|null|bool|Store $store [optional]
 * @return Currency
 */
function df_currency_current($store = null) {return df_store($store)->getCurrentCurrency();}

/**
 * 2016-09-05
 * В отличие от @see df_currency_base_с() здесь мы вынуждены использовать не $scope, а $store,
 * потому что учётную валюту можно просто считать из настроек,
 * а текущая валюта может меняться динамически (в том числе посетителем магазина и сессией).
 * @param int|string|null|bool|Store $store [optional]
 * @return string
 */
function df_currency_current_c($store = null) {return df_currency_current($store)->getCode();}

/** @return Df_Directory_Helper_Currency */
function df_currency_h() {return Df_Directory_Helper_Currency::s();}

/**
 * Возвращает имя валюты по её 3-буквенному коду.
 * В случает отсутствия валюты возвращает 3-буквенный код.
 * @param string $code
 * @return string
 */
function df_currency_name($code) {
	/** @var Zend_Currency|null $currency */
	$currency = df_currency_zf($code, false);
	return $currency ? $currency->getName() : $code;
}

/**
 * 2015-03-25
 * @used-by Df_Adminhtml_Block_Sales_Order_View_Items_Renderer_Default::displayPricesDf()
 * @used-by df_money_fl()
 * @used-by Df_Directory_Model_Currency::formatDf()
 * @used-by Df_Directory_Model_Currency::formatTxtDf()
 * @used-by Df_Sales_Model_Order::formatPriceDf()
 * @return int
 */
function df_currency_precision() {
	static $r; return isset($r) ? $r : $r = (
		df_loc()->needHideDecimals()
		? 0
		: dfa(df_mage()->core()->localeSingleton()->getJsPriceFormat(), 'requiredPrecision', 2)
	);
}

/**
 * 2016-08-08
 * @return float
 */
function df_currency_rate_to_current() {return df_currency_base()->getRate(df_currency_current());}

/**
 * @param string $code
 * @param
 * @return Zend_Currency|null
 * @throws Zend_Currency_Exception
 */
function df_currency_zf($code, $throw = true) {
	/** @var Zend_Currency|null $result */
	try {
		$result = Mage::app()->getLocale()->currency($code);
	}
	catch (Zend_Currency_Exception $e) {
		if ($throw) {
			throw $e;
		}
		$result = $code;
	}
	return $result;
}