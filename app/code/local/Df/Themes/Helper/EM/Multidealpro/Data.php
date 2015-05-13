<?php
class Df_Themes_Helper_EM_Multidealpro_Data extends EM_Multidealpro_Helper_Data {
	/**
	 * @override
	 * @param Df_Catalog_Model_Product $_product
	 * @return string
	 */
	public function getClock($_product) {
		/**
		 * Оператор @ защищает от сбоя
		 * Warning: date_format() expects parameter 1 to be DateTimeInterface, boolean given
		 * который происходит в методе @see EM_Multidealpro_Helper_Data::getTimeClock()
		 * на строке
		 * return date_format(date_create($time), 'Y/m/d H:i:s');
		 * в том случае, когда переменная $time = 0.
		 */
		return strtr(@parent::getClock($_product), array(
			'>days<' => '>дней<'
			, '>hours<' => '>часов<'
			, '>minutes<' => '>минут<'
			, '>seconds<' => '>секунд<'
		));
	}
}