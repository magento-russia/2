<?php
class Df_Shipping_Rate_Result_Method extends Mage_Shipping_Model_Rate_Result_Method {
	/**
	 * @used-by STUB::STUB()
	 * @return Zend_Date|null
	 */
	public function dateMax() {return $this[self::$P__DATE_MAX];}

	/**
	 * @used-by STUB::STUB()
	 * @return Zend_Date|null
	 */
	public function dateMin() {return $this[self::$P__DATE_MIN];}

	/** @var string */
	private static $P__DATE_MAX = 'date_max';
	/** @var string */
	private static $P__DATE_MIN = 'date_min';

	/**
	 * 2015-04-06
	 * @used-by Df_Shipping_Collector::addRate()
	 * @param string|null $code
	 * @param string|null $title
	 * @param float $costBase
	 * @param float $price
	 * @param array(string => string) $additional
	 * @param Zend_Date|int|null $dateMin
	 * @param Zend_Date|int|null $dateMax
	 * @return Df_Shipping_Rate_Result_Method
	 */
	public static function i($code, $title, $costBase, $price, array $additional, $dateMin, $dateMax) {
		return new self(array(
			/** @used-by Mage_Sales_Model_Quote_Address_Rate::importShippingRate() */
			'method' => $code
			, 'method_title' => $title
			, 'cost' => $costBase
			, 'price' => $price
			, self::$P__DATE_MAX = self::date($dateMax)
			, self::$P__DATE_MIN => self::date($dateMin)
		) + $additional);
	}

	/**
	 * 2015-04-07
	 * 1) Если служба доставки возвращает ДАТУ ДОСТАВКИ,
	 * то прибавление к текущей дате количества времени
	 * на обработку заказа интернет-магазином перед передачей заказа в службу доставки
	 * должно производиться перед запросом даты доставки у службы доставки.
	 * Как правило, модуль доставки в таком случае передаёт службе доставки
	 * планируемую дату передачи заказа в службу доставки:
	 * @see Df_Shipping_Settings_InHouseProcessing::date()
	 *
	 * 2) Если служба доставки возвращает СРОК ДОСТАВКИ,
	 * то прибавление к текущей дате количества времени
	 * на обработку заказа интернет-магазином перед передачей заказа в службу доставки
	 * удобно производить ЗДЕСЬ, УЖЕ ПОСЛЕ РАСЧЁТА сроков доставки
	 * (чтобы не дублировать такие прибавления в каждом модуле доставки).
	 *
	 * @used-by i()
	 * @param Zend_Date|int|null $date
	 * @return Zend_Date|null
	 */
	private static function date($date) {
		return !$date ? null : ($date instanceof Zend_Date ? $date :
			rm_today_add(rm_nat($date) + Df_Shipping_Settings_InHouseProcessing::days())
		);
	}
}