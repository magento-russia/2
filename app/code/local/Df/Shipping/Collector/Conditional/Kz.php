<?php
namespace Df\Shipping\Collector\Conditional;
abstract class Kz extends WithForeign {
	/**
	 * @override
	 * @see \Df\Shipping\Collector::currencyCode()
	 * @used-by \Df\Shipping\Collector::fromBase()
	 * @used-by \Df\Shipping\Collector::toBase()
	 * @return string
	 */
	protected function currencyCode() {return 'KZT';}

	/**
	 * @override
	 * @see \Df\Shipping\Collector::domesticIso2()
	 * @used-by \Df\Shipping\Collector::collect()
	 * @used-by \Df\Shipping\Collector\Conditional\WithForeign::suffix()
	 * @return string
	 */
	protected function domesticIso2() {return 'KZ';}
}


