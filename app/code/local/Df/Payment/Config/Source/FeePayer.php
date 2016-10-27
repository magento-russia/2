<?php
namespace Df\Payment\Config\Source;
class FeePayer extends \Df\Payment\Config\Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {return
		df_map_to_options([self::VALUE__SHOP => 'магазин', self::VALUE__BUYER => 'покупатель'])
	;}

	const VALUE__BUYER = 'buyer';
	const VALUE__SHOP = 'shop';
}