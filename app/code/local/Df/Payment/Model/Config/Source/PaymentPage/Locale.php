<?php
/**
 * @singleton
 * В этом классе нельзя кешировать результаты вычислений!
 */
class Df_Payment_Model_Config_Source_PaymentPage_Locale extends Df_Payment_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return
			$this->getPaymentMethod()->getRmConfig()->service()
				->getAllowedLocalesAsOptionArray()
		;
	}
	const _CLASS = __CLASS__;
}