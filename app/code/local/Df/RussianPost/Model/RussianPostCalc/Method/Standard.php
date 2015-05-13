<?php
class Df_RussianPost_Model_RussianPostCalc_Method_Standard extends Df_RussianPost_Model_RussianPostCalc_Method {
	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {
		return 'standard';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getTitleBase() {
		return 'стандартная';
	}

	const _CLASS = __CLASS__;
}