<?php
class Df_Payment_Model_Config_Source_PaymentCard_PaymentAction extends Df_Payment_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return
			array(
				array(
					self::OPTION_KEY__VALUE => self::VALUE__AUTHORIZE
					,self::OPTION_KEY__LABEL =>
						df_h()->payment()->__('резервировать стоимость заказа на карте покупателя')
				)
				,array(
					self::OPTION_KEY__VALUE => self::VALUE__CAPTURE
					,self::OPTION_KEY__LABEL =>
						df_h()->payment()->__('снимать стоимость заказа с карты покупателя')
				)
			)
		;
	}
	const _CLASS = __CLASS__;
	const VALUE__AUTHORIZE = 'authorize';
	const VALUE__CAPTURE = 'capture';
}