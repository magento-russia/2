<?php
class Df_Reward_Block_Customer_Reward_Info extends Df_Core_Block_Template_NoCache {
	/**
	 * Reward pts model instance
	 *
	 * @var Df_Reward_Model_Reward
	 */
	protected $_rewardInstance = null;

	/**
	 * @override
	 * @see Mage_Core_Block_Template::_toHtml()
	 * @used-by Mage_Core_Block_Abstract::toHtml()
	 * @return string
	 */
	protected function _toHtml() {
		$customer = df_session_customer()->getCustomer();
		$this->_rewardInstance =
			Df_Reward_Model_Reward::i()
				->setCustomer($customer)
				->setWebsiteId(rm_website_id())
				->loadByCustomer()
		;
		if ($this->_rewardInstance->getId()) {
			$this->_prepareTemplateData();
			return parent::_toHtml();
		}
		return '';
	}

	/**
	 * Set various variables requested by template
	 */
	protected function _prepareTemplateData()
	{
		$maxBalance = (int)df_h()->reward()->getGeneralConfig('max_points_balance');
		$minBalance = (int)df_h()->reward()->getGeneralConfig('min_points_balance');
		$balance = $this->_rewardInstance->getPointsBalance();
		$this->addData(array(
			'points_balance' => $balance
			,'currency_balance' => $this->_rewardInstance->getCurrencyAmount()
			,'pts_to_amount_rate_pts' => $this->_rewardInstance->getRateToCurrency()->getPoints(true)
			,'pts_to_amount_rate_amount' =>
				$this->_rewardInstance->getRateToCurrency()->getCurrencyAmount()
			,'amount_to_pts_rate_amount' =>
				$this->_rewardInstance->getRateToPoints()->getCurrencyAmount()
			,'amount_to_pts_rate_pts' => $this->_rewardInstance->getRateToPoints()->getPoints(true)
			,'max_balance' => $maxBalance
			,'is_max_balance_reached' => $balance >= $maxBalance
			,'min_balance' => $minBalance
			,'is_min_balance_reached' => $balance >= $minBalance
			,'expire_in' => (int)df_h()->reward()->getGeneralConfig('expiration_days')
			,'is_history_published' => (int)df_h()->reward()->getGeneralConfig('publish_history')
		));
	}

	/**
	 * @override
	 * @see Df_Core_Block_Abstract::needToShow()
	 * @used-by Df_Core_Block_Abstract::_loadCache()
	 * @used-by Df_Core_Block_Abstract::getCacheKey()
	 * @return bool
	 */
	protected function needToShow() {return rm_customer_logged_in();}
}