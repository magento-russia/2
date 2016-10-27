<?php
namespace Df\Shipping\Rate;
class Result extends \Mage_Shipping_Model_Rate_Result {
	/**
	 * @used-by \Df\Shipping\Carrier::getConfigData()
	 * @return bool
	 */
	public function isInternalError() {return $this->_internalError;}

	/**
	 * @used-by \Df\Shipping\Collector::call()
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