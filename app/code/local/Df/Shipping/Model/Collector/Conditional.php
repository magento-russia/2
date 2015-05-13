<?php
abstract class Df_Shipping_Model_Collector_Conditional extends Df_Shipping_Model_Collector_Simple {
	/**
	 * @used-by _collect()
	 * @return string
	 */
	abstract protected function childClass();

	/**
	 * @override
	 * @see Df_Shipping_Model_Collector_Simple::_collect()
	 * @used-by Df_Shipping_Model_Collector_Simple::collect()
	 * @return void
	 */
	protected function _collect() {
		$this->collectPrepare();
		Df_Shipping_Model_Collector_Child::s_collect($this->childClass(), $this);
	}

	/**
	 * @used-by _collect()
	 * @return void
	 */
	protected function collectPrepare() {}
}