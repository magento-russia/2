<?php
class Df_Payment_Config_Area_Admin extends Df_Payment_Config_Area {
	/**
	 * @override
	 * @return string
	 */
	protected function getAreaPrefix() {return 'admin';}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getStandardKeys() {
		return array_merge(parent::getStandardKeys(), array('order_status','payment_action'));
	}
}