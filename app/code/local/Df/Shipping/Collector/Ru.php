<?php
abstract class Df_Shipping_Collector_Ru extends Df_Shipping_Collector {
	/**
	 * @override
	 * @see Df_Shipping_Collector::currencyCode()
	 * @used-by Df_Shipping_Collector::fromBase()
	 * @used-by Df_Shipping_Collector::toBase()
	 * @return string
	 */
	protected function currencyCode() {return 'RUB';}

	/**
	 * @override
	 * @see Df_Shipping_Collector::domesticIso2()
	 * @used-by Df_Shipping_Collector::collect()
	 * @used-by Df_Shipping_Collector_Conditional_WithForeign::childClass()
	 * @return string
	 */
	protected function domesticIso2() {return 'RU';}
}