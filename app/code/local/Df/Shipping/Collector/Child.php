<?php
namespace Df\Shipping\Collector;
abstract class Child extends \Df\Shipping\Collector {
	/**
	 * @override
	 * @see \Df\Shipping\Collector::_result()
	 * @used-by \Df\Shipping\Collector::addError()
	 * @used-by \Df\Shipping\Collector::rate()
	 * @used-by \Df\Shipping\Collector::call()
	 * @used-by \Df\Shipping\Collector::r()
	 * @return \Mage_Shipping_Model_Rate_Result
	 */
	public function _result() {return $this->parent()->_result();}

	/**
	 * @override
	 * @see \Df\Shipping\Collector::currencyCode()
	 * @used-by \Df\Shipping\Collector::fromBase()
	 * @used-by \Df\Shipping\Collector::toBase()
	 * @return string
	 */
	protected function currencyCode() {return $this->parent()->currencyCode();}

	/**
	 * @override
	 * @see \Df\Shipping\Collector::domesticIso2()
	 * @used-by \Df\Shipping\Collector::_result()
	 * @used-by \Df\Shipping\Collector\Conditional\WithForeign::suffix()
	 * @return string
	 */
	protected function domesticIso2() {return $this->parent()->domesticIso2();}

	/**
	 * @used-by currencyCode()
	 * @used-by domesticIso2()
	 * @used-by getRateResult()
	 * @return Conditional
	 */
	private function parent() {return $this[self::$P__PARENT];}

	/**
	 * @see \Df\Shipping\Collector::rateDefaultCode()
	 * @used-by \Df\Shipping\Collector::rate()
	 * @return string
	 */
	protected function rateDefaultCode() {return dfc($this, function() {
		return mb_strtoupper(df_last(df_explode_class($this)));
	});}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__PARENT, Conditional::class);
	}
	/** @var string */
	private static $P__PARENT = 'parent';

	/**
	 * @used-by \Df\Shipping\Collector\Conditional::_collect()
	 * @param string $suffix
	 * @param Conditional $parent
	 * @return void
	 */
	public static function s_collect($suffix, Conditional $parent) {
		/** @var string $class */
		$class = df_cts($parent) . df_class_delimiter($parent) . $suffix;
		/** @var self $i */
		$i = df_ic($class, __CLASS__, [self::$P__PARENT => $parent] + $parent->getData());
		$i->_collect();
	}
}