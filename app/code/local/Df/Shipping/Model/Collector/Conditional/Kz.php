<?php
abstract class Df_Shipping_Model_Collector_Conditional_Kz
	extends Df_Shipping_Model_Collector_Conditional_WithForeign {
	/**
	 * @override
	 * @see Df_Shipping_Model_Collector_Simple::currencyCode()
	 * @used-by Df_Shipping_Model_Collector_Simple::fromBase()
	 * @used-by Df_Shipping_Model_Collector_Simple::toBase()
	 * @return string
	 */
	protected function currencyCode() {return 'KZT';}

	/**
	 * @override
	 * @see Df_Shipping_Model_Collector_Simple::domesticIso2()
	 * @used-by Df_Shipping_Model_Collector_Simple::getRateResult()
	 * @used-by Df_Shipping_Model_Collector_Conditional_WithForeign::childClass()
	 * @return string
	 */
	protected function domesticIso2() {return 'KZ';}
}


