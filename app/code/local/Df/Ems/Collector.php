<?php
class Df_Ems_Collector extends Df_Shipping_Collector_Ru {
	/**
	 * @override
	 * @see Df_Shipping_Collector::_collect()
	 * @used-by Df_Shipping_Collector::collect()
	 * @return void
	 */
	protected function _collect() {
		$this->addRate(100);
	}
}


