<?php
class Df_Payment_Model_Config_Area_Admin
	extends Df_Payment_Model_Config_Area_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getAreaPrefix() {
		/** @var string $result */
		$result = self::AREA_PREFIX;
		df_result_string($result);
		return $result;
	}

	/**
	 * @override
	 * @return array
	 */
	protected function getStandardKeys() {
		/** @var array $result */
		$result =
			array_merge(
				parent::getStandardKeys()
				,array(
 					'order_status'
					,'payment_action'
				)
			)
		;
		return $result;
	}

	const _CLASS = __CLASS__;
	const AREA_PREFIX = 'admin';
}