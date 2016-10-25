<?php
// 2016-10-25
namespace Df\Ems;
class Locator extends \Df_Shipping_Locator {
	/**
	 * 2016-10-25
	 * @override
	 * @see \Df_Shipping_Locator::_map()
	 * @used-by \Df_Shipping_Locator::map()
	 * @param string $type
	 * @return array(string => string|int|array(string|int))
	 */
	protected function _map($type) {
		return [];
	}
}


