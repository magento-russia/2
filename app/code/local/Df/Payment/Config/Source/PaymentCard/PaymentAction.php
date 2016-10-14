<?php
class Df_Payment_Config_Source_PaymentCard_PaymentAction extends Df_Payment_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return df_map_to_options(array(
			self::VALUE__AUTHORIZE => 'резервировать стоимость заказа на карте покупателя'
			,self::VALUE__CAPTURE => 'снимать стоимость заказа с карты покупателя'
		));
	}
	const VALUE__AUTHORIZE = 'authorize';
	const VALUE__CAPTURE = 'capture';
}