<?php
abstract class Df_Shipping_Model_Collector_Conditional_WithForeign
	extends Df_Shipping_Model_Collector_Conditional {
	/**
	 * @override
	 * @see Df_Shipping_Model_Collector_Conditional::childClass()
	 * @used-by Df_Shipping_Model_Collector_Conditional::_collect()
	 * @return string
	 */
	protected function childClass() {
		return $this->domesticIso2() === $this->countryDestIso2() ? 'Domestic' : 'Foreign';
	}
}