<?php
namespace Df\Ems;
class Cond extends \Df\Ems\Request {
	/**
	 * @override
	 * @see \Df\Shipping\Request::_rate()
	 * @return float
	 */
	protected function _rate() {return $this->p('price');}

	/**
	 * Для международных отправлений калькулятор EMS не сообщает сроки
	 * @override
	 * @param string|int|null $value
	 * @return int
	 */
	protected function _deliveryTimeFilter($value) {return is_null($value) ? 0 : df_nat($value);}

	/**
	 * @override
	 * @return int
	 */
	protected function _deliveryTimeMax() {return $this->p('term/max');}

	/**
	 * @override
	 * @return int
	 */
	protected function _deliveryTimeMin() {return $this->p('term/min');}

	/**
	 * @param string $from
	 * @param string $to
	 * @param float $weight
	 * @param string $type
	 * @return $this
	 */
	public static function i2($from, $to, $weight, $type) {return new self([self::P__QUERY => [
		'from' => $from
		,'method' => 'ems.calculate'
		,'to' => $to
		,'type' => $type
		,'weight' => $weight
	]]);}
}