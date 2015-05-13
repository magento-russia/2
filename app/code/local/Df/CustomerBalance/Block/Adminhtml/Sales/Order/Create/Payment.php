<?php
class Df_CustomerBalance_Block_Adminhtml_Sales_Order_Create_Payment
	extends Df_Core_Block_Template {
	/**
	 * @var Df_CustomerBalance_Model_Balance
	 */
	protected $_balanceInstance;

	/**
	 * Retrieve order create model
	 * @return Mage_Adminhtml_Model_Sales_Order_Create
	 */
	protected function _getOrderCreateModel() {
		return Mage::getSingleton('adminhtml/sales_order_create');
	}

	/**
	 * Format value as price
	 *
	 * @param numeric $value
	 * @return string
	 */
	public function formatPrice($value)
	{
		return Mage::getSingleton('adminhtml/session_quote')->getStore()->formatPrice($value);
	}

	/**
	 * Balance getter
	 * @return float
	 */
	public function getBalance()
	{
		if (!df_h()->customer()->balance()->isEnabled() || !$this->_getBalanceInstance()) {
			return 0.0;
		}
		return $this->_getBalanceInstance()->getAmount();
	}

	/**
	 * Check whether quote uses customer balance
	 * @return bool
	 */
	public function getUseCustomerBalance()
	{
		return $this->_getOrderCreateModel()->getQuote()->getUseCustomerBalance();
	}

	/**
	 * Check whether customer balance fully covers quote
	 * @return bool
	 */
	public function isFullyPaid()
	{
		if (!$this->_getBalanceInstance()) {
			return false;
		}
		return $this->_getBalanceInstance()->isFullAmountCovered($this->_getOrderCreateModel()->getQuote());
	}

	/**
	 * Check whether quote uses customer balance
	 * @return bool
	 */
	public function isUsed()
	{
		return $this->getUseCustomerBalance();
	}

	/**
	 * Instantiate/load balance and return it
	 * @return Df_CustomerBalance_Model_Balance|bool
	 */
	protected function _getBalanceInstance() {
		if (!$this->_balanceInstance) {
			$quote = $this->_getOrderCreateModel()->getQuote();
			if (!$quote || !$quote->getCustomerId() || !$quote->getStoreId()) {
				return false;
			}
			$store = Mage::app()->getStore($quote->getStoreId());
			/** @var Df_CustomerBalance_Model_Balance $result */
			$result = Df_CustomerBalance_Model_Balance::i();
			$result->setCustomerId($quote->getCustomerId());
			$result->setWebsiteId($store->getWebsiteId());
			$result->loadByCustomer();
			$this->_balanceInstance = $result;
		}
		return $this->_balanceInstance;
	}
}