<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Payment_Model_Config_Source_PaymentMethod extends Df_Payment_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return
			$this->getPaymentMethod()->getRmConfig()->service()
				->getAvailablePaymentMethodsAsOptionArray()
		;
	}
	const _CLASS = __CLASS__;
}