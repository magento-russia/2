<?php
namespace Df\Ems;
class Conditions extends \Df\Ems\Request {
	/**
	 * @override
	 * @return float
	 */
	protected function _getRate() {return $this->p('price');}

	/**
	 * Для международных отправлений калькулятор EMS не сообщает сроки
	 * @override
	 * @param string|int|null $value
	 * @return int
	 */
	protected function _filterDeliveryTime($value) {return is_null($value) ? 0 : df_nat($value);}

	/**
	 * @override
	 * @return int
	 */
	protected function _getDeliveryTimeMax() {return $this->p('term/max');}

	/**
	 * @override
	 * @return int
	 */
	protected function _getDeliveryTimeMin() {return $this->p('term/min');}

	/**
	 * @param string $from
	 * @param string $to
	 * @param float $weight
	 * @param string $type
	 * @return $this
	 */
	public static function i2($from, $to, $weight, $type) {return new self([self::P__PARAMS_QUERY => [
		'from' => $from
		,'method' => 'ems.calculate'
		,'to' => $to
		,'type' => $type
		,'weight' => $weight
	]]);}
}