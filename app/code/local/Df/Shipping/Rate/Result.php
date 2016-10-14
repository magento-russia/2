<?php
class Df_Shipping_Rate_Result extends Mage_Shipping_Model_Rate_Result {
	/**
	 * @used-by Df_Shipping_Carrier::getConfigData()
	 * @return bool
	 */
	public function isInternalError() {return $this->_internalError;}

	/**
	 * @used-by Df_Shipping_Collector::call()
	 * @return void
	 */
	public function markInternalError() {$this->_internalError = true;}

	/**
	 * @used-by isInternalError()
	 * @used-by markInternalError()
	 * @var bool
	 */
	private $_internalError = false;
}