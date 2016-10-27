<?php
namespace Df\Shipping\Collector;
abstract class Ru extends \Df\Shipping\Collector {
	/**
	 * @override
	 * @see \Df\Shipping\Collector::currencyCode()
	 * @used-by \Df\Shipping\Collector::fromBase()
	 * @used-by \Df\Shipping\Collector::toBase()
	 * @return string
	 */
	protected function currencyCode() {return 'RUB';}

	/**
	 * @override
	 * @see \Df\Shipping\Collector::domesticIso2()
	 * @used-by \Df\Shipping\Collector::collect()
	 * @used-by \Df\Shipping\Collector\Conditional\WithForeign::suffix()
	 * @return string
	 */
	protected function domesticIso2() {return 'RU';}
}