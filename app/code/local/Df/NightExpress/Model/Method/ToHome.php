<?php
class Df_NightExpress_Model_Method_ToHome extends Df_NightExpress_Model_Method {
	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {
		return 'to-home';
	}

	/**
	 * @override
	 * @return bool
	 */
	protected function needDeliverToHome() {
		return true;
	}

	const _CLASS = __CLASS__;
}