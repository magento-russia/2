<?php
namespace Df\Shipping\Collector\Conditional;
abstract class WithForeign extends \Df\Shipping\Collector\Conditional {
	/**
	 * @override
	 * @see \Df\Shipping\Collector\Conditional::suffix()
	 * @used-by \Df\Shipping\Collector\Conditional::_collect()
	 * @return string
	 */
	protected function suffix() {return
		$this->domesticIso2() === $this->dCountryIso2() ? 'Domestic' : 'Foreign'
	;}
}