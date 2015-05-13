<?php
class Df_OnPay_Model_Config_Source_Service_ReceiptCurrency extends Df_Payment_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return
			array(
				array(
					self::OPTION_KEY__VALUE => self::VALUE__PAYMENT
					,self::OPTION_KEY__LABEL => df_h()->payment()->__('в валюте платежа')
				)
				,array(
					self::OPTION_KEY__VALUE => self::VALUE__BILL
					,self::OPTION_KEY__LABEL => df_h()->payment()->__('в валюте счёта')
				)
			)
		;
	}
	const _CLASS = __CLASS__;
	const VALUE__BILL = 'в валюте счёта';
	const VALUE__PAYMENT = 'в валюте платежа';
}