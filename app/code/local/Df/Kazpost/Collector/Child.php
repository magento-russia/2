<?php
namespace Df\Kazpost\Collector;
abstract class Child extends \Df\Shipping\Collector\Child {
	/**
	 * @override
	 * @see \Df\Shipping\Collector::feePercentOfDeclaredValue()
	 * @used-by \Df\Shipping\Collector::rate()
	 * «Плата за объявленную ценность (процент от стоимости вложения): 1%»
	 * http://www.kazpost.kz/uploads/content/files/СТАНДАРТ%20Тарифы%20по%20почтовым%20услугам.docx
	 * @return int|float
	 */
	protected function feePercentOfDeclaredValue() {return 1;}
}