<?php
// 2016-10-26
class Df_Dellin_Collector extends Df_Shipping_Collector_Ru {
	/**
	 * 2016-10-26
	 * @override
	 * @see Df_Shipping_Collector::_collect()
	 * @used-by Df_Shipping_Collector::collect()
	 * @return void
	 */
	protected function _collect() {
		$this->addRate(100);
	}
}