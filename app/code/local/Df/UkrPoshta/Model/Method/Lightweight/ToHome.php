<?php
class Df_UkrPoshta_Model_Method_Lightweight_ToHome extends Df_UkrPoshta_Model_Method_Lightweight {
	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {
		return 'lightweight-to-home';
	}

	/**
	 * @override
	 * @return bool
	 */
	public function needDeliverToHome() {
		return true;
	}

	const _CLASS = __CLASS__;

}