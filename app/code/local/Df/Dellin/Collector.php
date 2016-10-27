<?php
// 2016-10-26
namespace Df\Dellin;
class Collector extends \Df\Shipping\Collector\Ru {
	/**
	 * 2016-10-26
	 * @override
	 * @see \Df\Shipping\Collector::_collect()
	 * @used-by \Df\Shipping\Collector::collect()
	 * @return void
	 */
	protected function _collect() {
		$this->addRate(100);
	}
}