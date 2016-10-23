<?php
/**
 * @singleton
 * В этом классе нельзя кешировать результаты вычислений!
 */
class Df_Payment_Config_Source_Currency extends Df_Payment_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return $this->method()->configS()->getAllowedCurrenciesAsOptionArray();
	}
}