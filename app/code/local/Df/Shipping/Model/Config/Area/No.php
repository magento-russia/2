<?php
class Df_Shipping_Model_Config_Area_No extends Df_Shipping_Model_Config_Area_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getAreaPrefix() {
		/** @var string $result */
		$result = '';
		df_result_string($result);
		return $result;
	}

	const _CLASS = __CLASS__;
}