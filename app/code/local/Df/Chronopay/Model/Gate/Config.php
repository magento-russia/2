<?php
class Df_Chronopay_Model_Gate_Config extends Varien_Object {


	/**
	 * @param string $field
	 * @param string|null $default [optional]
	 * @return string|null
	 */
	public function getParam($field, $default = null) {
		$result = $this->getPaymentModel()->getConfigData($field);
		return (is_null($result)) ? $default : $result;
	}

	/** @return Df_Chronopay_Model_Gate */
	private function getPaymentModel() {
		return Df_Chronopay_Model_Gate::s();
	}

	/** @return string */
	public function getSiteId()
	{
		return $this->getParam('site_id');
	}

	/** @return string */
	public function getProductId()
	{
		return $this->getParam('product_id');
	}

	/** @return string */
	public function getSharedSecret()
	{
		return $this->getParam('shared_sec');
	}

	/** @return string */
	public function getDescription() {
		return $this->getParam('description');
	}

	/** @return string */
	public function getNewOrderStatus()
	{
		return $this->getParam('order_status');
	}

	/** @return string */
	public function getCurrency()
	{
		return $this->getParam('currency');
	}

	/** @return string */
	public function getLanguage()
	{
		return $this->getParam('language');
	}
}