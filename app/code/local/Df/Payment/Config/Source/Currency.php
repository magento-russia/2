<?php
namespace Df\Payment\Config\Source;
/**
 * @singleton
 * В этом классе нельзя кешировать результаты вычислений!
 */
class Currency extends \Df\Payment\Config\Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {return
		$this->method()->configS()->getAllowedCurrenciesAsOptionArray()
	;}
}