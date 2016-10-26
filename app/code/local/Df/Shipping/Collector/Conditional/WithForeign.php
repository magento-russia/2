<?php
abstract class Df_Shipping_Collector_Conditional_WithForeign
	extends Df_Shipping_Collector_Conditional {
	/**
	 * @override
	 * @see Df_Shipping_Collector_Conditional::suffix()
	 * @used-by Df_Shipping_Collector_Conditional::_collect()
	 * @return string
	 */
	protected function suffix() {return
		$this->domesticIso2() === $this->countryDestIso2() ? 'Domestic' : 'Foreign'
	;}
}