<?php
class Df_CustomerBalance_Block_Checkout_Payment extends Df_Core_Block_Template_NoCache {
	/**
	 * Customer balance instance
	 *
	 * @var Df_CustomerBalance_Model_Balance
	 */
	protected $_balanceModel = null;

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
			$result->setWebsiteId(df_website_id());
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
	 * @return Df_Customer_Model_Customer
	 */
	protected function _getCustomer() {return df_session_customer()->getCustomer();}

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
	public function getAmountToCharge() {
		if ($this->isCustomerBalanceUsed()) {
			return df_quote()->getCustomerBalanceAmountUsed();
		}
		return min($this->getBalance(), df_quote()->getBaseGrandTotal());
	}

	/**
	 * Check whether customer balance is used in current quote
	 * @return bool
	 */
	public function isCustomerBalanceUsed() {return df_quote()->getUseCustomerBalance();}

	/**
	 * Check whether customer balance fully covers quote
	 * @return bool
	 */
	public function isFullyPaidAfterApplication()
	{
		return $this->_getBalanceModel()->isFullAmountCovered(df_quote(), true);
	}

	/**
	 * @param string $file
	 * @return string
	 */
	public static function html($file) {
		/** @var array(string => string) $cache */
		static $cache;
		if (!isset($cache[$file])) {
			$cache[$file] = df_render(__CLASS__, "df/customerbalance/checkout/payment/{$file}.phtml");
		}
		return $cache[$file];
	}
}