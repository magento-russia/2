<?php
abstract class Df_Shipping_Collector_Child extends Df_Shipping_Collector {
	/**
	 * @override
	 * @see Df_Shipping_Collector::_result()
	 * @used-by Df_Shipping_Collector::addError()
	 * @used-by Df_Shipping_Collector::addRate()
	 * @used-by Df_Shipping_Collector::call()
	 * @used-by Df_Shipping_Collector::r()
	 * @return Mage_Shipping_Model_Rate_Result
	 */
	public function _result() {return $this->parent()->_result();}

	/**
	 * @override
	 * @see Df_Shipping_Collector::currencyCode()
	 * @used-by Df_Shipping_Collector::fromBase()
	 * @used-by Df_Shipping_Collector::toBase()
	 * @return string
	 */
	protected function currencyCode() {return $this->parent()->currencyCode();}

	/**
	 * @override
	 * @see Df_Shipping_Collector::domesticIso2()
	 * @used-by Df_Shipping_Collector::_result()
	 * @used-by Df_Shipping_Collector_Conditional_WithForeign::childClass()
	 * @return string
	 */
	protected function domesticIso2() {return $this->parent()->domesticIso2();}

	/**
	 * @used-by currencyCode()
	 * @used-by domesticIso2()
	 * @used-by getRateResult()
	 * @return Df_Shipping_Collector_Conditional
	 */
	private function parent() {return $this->cfg(self::$P__PARENT);}

	/**
	 * @see Df_Shipping_Collector::rateDefaultCode()
	 * @used-by Df_Shipping_Collector::addRate()
	 * @return string
	 */
	protected function rateDefaultCode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = mb_strtoupper(rm_last(rm_explode_class($this)));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__PARENT, 'Df_Shipping_Collector_Conditional');
	}
	/** @var string */
	private static $P__PARENT = 'parent';

	/**
	 * @used-by Df_Shipping_Collector_Conditional::_collect()
	 * @param string $class
	 * @param Df_Shipping_Collector_Conditional $parent
	 * @return void
	 */
	public static function s_collect($class, Df_Shipping_Collector_Conditional $parent) {
		/** @var string $m */
		$m = rm_module_name($parent->main());
		/** @var bool $full */
		$full = $class && rm_starts_with($class, $m);
		$class = $full ? $class : rm_concat_class($m, 'Model_Collector', $class);
		/** @var Df_Shipping_Collector_Child $i */
		$i = rm_ic($class, __CLASS__, array(self::$P__PARENT => $parent) + $parent->getData());
		$i->_collect();
	}
}