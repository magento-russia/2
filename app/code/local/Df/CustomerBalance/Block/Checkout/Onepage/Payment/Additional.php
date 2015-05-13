<?php
class Df_CustomerBalance_Block_Checkout_Onepage_Payment_Additional
	extends Df_Core_Block_Template_NoCache {
	/**
	 * Customer balance instance
	 *
	 * @var Df_CustomerBalance_Model_Balance
	 */
	protected $_balanceModel = null;

	/**
	 * Get quote instance
	 * @return Mage_Sales_Model_Quote
	 */
	protected function _getQuote() {return rm_session_checkout()->getQuote();}

	/**
	 * Getter
	 * @return Mage_Sales_Model_Quote
	 */
	public function getQuote() {return $this->_getQuote();}

	/**
	 * Get balance instance
	 * @return Df_CustomerBalance_Model_Balance
	 */
	protected function _getBalanceModel()
	{
		if (is_null($this->_balanceModel)) {
			/** @var Df_CustomerBalance_Model_Balance $result */
			$result = Df_CustomerBalance_Model_Balance::i();
			$result->setCustomer($this->_getCustomer());
			$result->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
			//load customer balance for customer in case we have
			//registered customer and this is not guest checkout
			if ($this->_getCustomer()->getId()) {
				$result->loadByCustomer();
			}
			$this->_balanceModel = $result;
		}
		return $this->_balanceModel;
	}

	/**
	 * Get customer instance
	 * @return Mage_Customer_Model_Customer
	 */
	protected function _getCustomer()
	{
		return rm_session_customer()->getCustomer();
	}

	/**
	 * Can display customer balance container
	 * @return bool
	 */
	public function isDisplayContainer()
	{
		if (!$this->_getCustomer()->getId()) {
			return false;
		}

		if (!$this->getBalance()) {
			return false;
		}
		return true;
	}

	/**
	 * Check whether customer balance is allowed as additional payment option
	 * @return bool
	 */
	public function isAllowed()
	{
		if (!$this->isDisplayContainer()) {
			return false;
		}

		if (!$this->getAmountToCharge()) {
			return false;
		}
		return true;
	}

	/**
	 * Get balance amount
	 * @return float
	 */
	public function getBalance()
	{
		if (!$this->_getCustomer()->getId()) {
			return 0;
		}
		return $this->_getBalanceModel()->getAmount();
	}

	/**
	 * Get balance amount to be charged
	 * @return float
	 */
	public function getAmountToCharge()
	{
		if ($this->isCustomerBalanceUsed()) {
			return $this->_getQuote()->getCustomerBalanceAmountUsed();
		}
		return min($this->getBalance(), $this->_getQuote()->getBaseGrandTotal());
	}

	/**
	 * Check whether customer balance is used in current quote
	 * @return bool
	 */
	public function isCustomerBalanceUsed() {
		return $this->_getQuote()->getUseCustomerBalance();
	}

	/**
	 * Check whether customer balance fully covers quote
	 * @return bool
	 */
	public function isFullyPaidAfterApplication()
	{
		return $this->_getBalanceModel()->isFullAmountCovered($this->_getQuote(), true);
	}

	const _CLASS = __CLASS__;
	/**
	 * @param string|null $name [optional]
	 * @return Df_CustomerBalance_Block_Checkout_Onepage_Payment_Additional
	 */
	public static function i($name = null) {return df_block(__CLASS__, $name);}
}