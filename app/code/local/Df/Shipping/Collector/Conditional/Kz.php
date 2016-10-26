<?php
abstract class Df_Shipping_Collector_Conditional_Kz
	extends Df_Shipping_Collector_Conditional_WithForeign {
	/**
	 * @override
	 * @see Df_Shipping_Collector::currencyCode()
	 * @used-by Df_Shipping_Collector::fromBase()
	 * @used-by Df_Shipping_Collector::toBase()
	 * @return string
	 */
	protected function currencyCode() {return 'KZT';}

	/**
	 * @override
	 * @see Df_Shipping_Collector::domesticIso2()
	 * @used-by Df_Shipping_Collector::collect()
	 * @used-by Df_Shipping_Collector_Conditional_WithForeign::suffix()
	 * @return string
	 */
	protected function domesticIso2() {return 'KZ';}
}


