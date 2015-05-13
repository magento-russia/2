<?php
/**
 * @param string $value
 * @return string
 */
function df_json_pretty_print($value) {
	df_param_string($value, 0);
	/** @var Df_Zf_Filter_Json_PrettyPrint $filter */
	$filter = new Df_Zf_Filter_Json_PrettyPrint();
	/** @var string $result */
	$result = $filter->filter($value);
	df_result_string($result);
	return $result;
}

/**
 * @param string $code
 * @return Zend_Currency
 * @throws Zend_Currency_Exception
 */
function df_zf_currency($code) {return Mage::app()->getLocale()->currency($code);}