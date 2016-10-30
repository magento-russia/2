<?php
// 2016-10-30
namespace Df\Pec;
class Collector extends \Df\Shipping\Collector\Ru {
	/**
	 * 2016-10-30
	 * @override
	 * @see \Df\Shipping\Collector::_collect()
	 * @used-by \Df\Shipping\Collector::collect()
	 * @return void
	 */
	protected function _collect() {
		$this->addRate(100);
	}
}