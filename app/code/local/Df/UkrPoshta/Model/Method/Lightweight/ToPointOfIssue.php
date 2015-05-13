<?php
class Df_UkrPoshta_Model_Method_Lightweight_ToPointOfIssue extends Df_UkrPoshta_Model_Method_Lightweight {
	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {
		return 'lightweight-to-point-of-issue';
	}

	/**
	 * @override
	 * @return bool
	 */
	public function needDeliverToHome() {
		return false;
	}

	const _CLASS = __CLASS__;

}