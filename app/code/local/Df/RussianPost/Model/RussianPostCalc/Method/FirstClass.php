<?php
class Df_RussianPost_Model_RussianPostCalc_Method_FirstClass extends Df_RussianPost_Model_RussianPostCalc_Method {
	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {
		return 'first-class';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getTitleBase() {
		return 'первый класс';
	}

	const _CLASS = __CLASS__;
}