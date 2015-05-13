<?php
class Df_Garantpost_Model_Method_Light_Standard extends Df_Garantpost_Model_Method_Light {
	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {
		return self::METHOD;
	}

	/**
	 * @override
	 * @return bool
	 */
	public function isApplicable() {
		/** @var bool $result */
		$result =
				parent::isApplicable()
			&&
				!$this->getRmConfig()->service()->needAcceptCashOnDelivery()
		;
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getServiceCode() {
		return 'express';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getTitleBase() {
		return 'стандартный';
	}

	const _CLASS = __CLASS__;
	const METHOD = 'light-standard';
}