<?php
abstract class Df_Kazpost_Model_Collector_Child extends Df_Shipping_Model_Collector_Child {
	/**
	 * @override
	 * @see Df_Shipping_Model_Collector_Simple::feePercentOfDeclaredValue()
	 * @used-by Df_Shipping_Model_Collector_Simple::addRate()
	 * «Плата за объявленную ценность (процент от стоимости вложения): 1%»
	 * @link http://www.kazpost.kz/uploads/content/files/СТАНДАРТ%20Тарифы%20по%20почтовым%20услугам.docx
	 * @return int|float
	 */
	protected function feePercentOfDeclaredValue() {return 1;}
}