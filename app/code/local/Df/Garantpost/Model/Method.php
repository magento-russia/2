<?php
abstract class Df_Garantpost_Model_Method extends Df_Shipping_Model_Method_Russia {
	/**
	 * @abstract
	 * @return int
	 */
	abstract protected function getCostInRoubles();

	/**
	 * @override
	 * @return void
	 * @throws Exception
	 */
	protected function checkApplicability() {
		parent::checkApplicability();
		$this->checkCountryOriginIsRussia();
	}

	/** @return bool */
	protected function isDeliveryFromMoscow() {return $this->rr()->isOriginMoscow();}
}