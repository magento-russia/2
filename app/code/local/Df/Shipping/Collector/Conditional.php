<?php
namespace Df\Shipping\Collector;
abstract class Conditional extends \Df\Shipping\Collector {
	/**
	 * @used-by _collect()
	 * @see \Df\Shipping\Collector\Conditional\WithForeign::suffix()
	 * @return string
	 */
	abstract protected function suffix();

	/**
	 * @override
	 * @see \Df\Shipping\Collector::_collect()
	 * @used-by \Df\Shipping\Collector::collect()
	 * @return void
	 */
	protected function _collect() {
		$this->collectPrepare();
		Child::s_collect($this->suffix(), $this);
	}

	/**
	 * @used-by _collect()
	 * @return void
	 */
	protected function collectPrepare() {}
}