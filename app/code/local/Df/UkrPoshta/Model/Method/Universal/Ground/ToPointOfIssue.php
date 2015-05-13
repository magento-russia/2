<?php
class Df_UkrPoshta_Model_Method_Universal_Ground_ToPointOfIssue
	extends Df_UkrPoshta_Model_Method_Universal_Ground {
	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {
		return 'universal-ground-to-point-of-issue';
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