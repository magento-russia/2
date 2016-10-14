<?php
class Df_Garantpost_Model_Method_Light_CashOnDelivery extends Df_Garantpost_Model_Method_Light {
	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {return self::METHOD;}

	/**
	 * @override
	 * @return bool
	 */
	public function isApplicable() {
		return parent::isApplicable() && $this->configS()->needAcceptCashOnDelivery();
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getServiceCode() {return 'op';}

	/**
	 * @override
	 * @return string
	 */
	protected function getTitleBase() {return 'наложенный платёж';}

	const _C = __CLASS__;
	const METHOD = 'light-cashOnDelivery';
}