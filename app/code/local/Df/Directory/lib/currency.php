<?php
/**
 * Возвращает имя валюты по её 3-буквенному коду.
 * В случает отсутствия валюты возвращает 3-буквенный код.
 * @param string $code
 * @return string
 */
function rm_currency_name($code) {
	/** @var Zend_Currency|null $currency */
	$currency = rm_currency_zf($code, false);
	return $currency ? $currency->getName() : $code;
}

/**
 * 2015-03-25
 * @used-by Df_Adminhtml_Block_Sales_Order_View_Items_Renderer_Default::displayPricesDf()
 * @used-by rm_money_fl()
 * @used-by Df_Directory_Model_Currency::formatDf()
 * @used-by Df_Directory_Model_Currency::formatTxtDf()
 * @used-by Df_Sales_Model_Order::formatPriceDf()
 * @return int
 */
function rm_currency_precision() {
	static $r; return isset($r) ? $r : $r = (
		rm_loc()->needHideDecimals()
		? 0
		: df_a(df_mage()->core()->localeSingleton()->getJsPriceFormat(), 'requiredPrecision', 2)
	);
}

/** @return Df_Directory_Helper_Currency */
function rm_currency_h() {return Df_Directory_Helper_Currency::s();}

/**
 * @param string $code
 * @param
 * @return Zend_Currency|null
 * @throws Zend_Currency_Exception
 */
function rm_currency_zf($code, $throw = true) {
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

