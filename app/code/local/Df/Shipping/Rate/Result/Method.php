<?php
namespace Df\Shipping\Rate\Result;
use Zend_Date as ZD;
class Method extends \Mage_Shipping_Model_Rate_Result_Method {
	/**
	 * @used-by \Df\Shipping\Block\Carrier\Rate\Label::dateS()
	 * @return ZD|null
	 */
	public function dateMax() {return $this[self::$P__DATE_MAX];}

	/**
	 * @used-by \Df\Shipping\Block\Carrier\Rate\Label::dateS()
	 * @return ZD|null
	 */
	public function dateMin() {return $this[self::$P__DATE_MIN];}

	/** @var string */
	private static $P__DATE_MAX = 'date_max';
	/** @var string */
	private static $P__DATE_MIN = 'date_min';

	/**
	 * 2015-04-06
	 * @used-by \Df\Shipping\Collector::rate()
	 * @param string|null $code
	 * @param string|null $title
	 * @param float $costBase
	 * @param float $price
	 * @param array(string => string) $additional
	 * @param ZD|int|null $dateMin
	 * @param ZD|int|null $dateMax
	 * @return self
	 */
	public static function i($code, $title, $costBase, $price, array $additional, $dateMin, $dateMax) {
		return new self([
			/** @used-by \Mage_Sales_Model_Quote_Address_Rate::importShippingRate() */
			'method' => $code
			, 'method_title' => $title
			, 'cost' => $costBase
			, 'price' => $price
			, self::$P__DATE_MAX => self::date($dateMax)
			, self::$P__DATE_MIN => self::date($dateMin)
		] + $additional);
	}

	/**
	 * 2015-04-07
	 * 1) Если служба доставки возвращает ДАТУ ДОСТАВКИ,
	 * то прибавление к текущей дате количества времени
	 * на обработку заказа интернет-магазином перед передачей заказа в службу доставки
	 * должно производиться перед запросом даты доставки у службы доставки.
	 * Как правило, модуль доставки в таком случае передаёт службе доставки
	 * планируемую дату передачи заказа в службу доставки:
	 * @see \Df\Shipping\Settings\InHouseProcessing::date()
	 *
	 * 2) Если служба доставки возвращает СРОК ДОСТАВКИ,
	 * то прибавление к текущей дате количества времени
	 * на обработку заказа интернет-магазином перед передачей заказа в службу доставки
	 * удобно производить ЗДЕСЬ, УЖЕ ПОСЛЕ РАСЧЁТА сроков доставки
	 * (чтобы не дублировать такие прибавления в каждом модуле доставки).
	 *
	 * @used-by i()
	 * @param ZD|int|null $date
	 * @return ZD|null
	 */
	private static function date($date) {return
		!$date ? null : ($date instanceof ZD ? $date :
			df_today_add(df_nat($date) + \Df\Shipping\Settings\InHouseProcessing::days())
		)
	;}
}