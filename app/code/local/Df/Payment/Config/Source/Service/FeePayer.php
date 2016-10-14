<?php
class Df_Payment_Config_Source_Service_FeePayer extends Df_Payment_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return df_map_to_options(array(
			self::VALUE__SHOP => 'магазин', self::VALUE__BUYER => 'покупатель'
		));
	}
	const _C = __CLASS__;
	const VALUE__BUYER = 'buyer';
	const VALUE__SHOP = 'shop';
}