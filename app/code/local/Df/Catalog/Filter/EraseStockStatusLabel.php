<?php
/**
 * Скрываем состояние наличия товара на складе.
 */
class Df_Catalog_Filter_EraseStockStatusLabel implements Zend_Filter_Interface {
	/**
	 * @param string $value
	 * @throws Zend_Filter_Exception If filtering $value is impossible
	 * @return string
	 */
	public function filter($value) {
		/*************************************
		 * Проверка входных параметров метода
		 */
		df_param_string($value, 0);
		/*************************************/
		$result = $value;
		switch($value) {
			case self::T_AVAILABILITY:
			case self::T_IN_STOCK:
			case self::T_IN_STOCK_2:
			case self::T_OUT_OF_STOCK:
				$result = '';
				break;
		}
		df_result_string($result);
		return $result;
	}

	const T_AVAILABILITY = 'Availability:';
	const T_IN_STOCK = 'In stock:';
	const T_IN_STOCK_2 = 'In stock';
	const T_OUT_OF_STOCK = 'Out of stock';
}