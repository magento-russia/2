<?php
abstract class Df_Shipping_Collector_Conditional extends Df_Shipping_Collector {
	/**
	 * @used-by _collect()
	 * @see Df_Shipping_Collector_Conditional_WithForeign::suffix()
	 * @return string
	 */
	abstract protected function suffix();

	/**
	 * @override
	 * @see Df_Shipping_Collector::_collect()
	 * @used-by Df_Shipping_Collector::collect()
	 * @return void
	 */
	protected function _collect() {
		$this->collectPrepare();
		Df_Shipping_Collector_Child::s_collect($this->suffix(), $this);
	}

	/**
	 * @used-by _collect()
	 * @return void
	 */
	protected function collectPrepare() {}
}