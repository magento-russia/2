<?php
class Df_Payment_Model_Config_Source_Service_FeePayer extends Df_Payment_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return
			array(
				array(
					self::OPTION_KEY__VALUE => self::VALUE__SHOP
					,self::OPTION_KEY__LABEL => 'магазин'
				)
				,array(
					self::OPTION_KEY__VALUE => self::VALUE__BUYER
					,self::OPTION_KEY__LABEL => 'покупатель'
				)
			)
		;
	}
	const _CLASS = __CLASS__;
	const VALUE__BUYER = 'buyer';
	const VALUE__SHOP = 'shop';
}