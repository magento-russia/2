<?php
class Df_Reward_Block_Checkout_Payment_Additional extends Df_Core_Block_Template_NoCache {
	/**
	 * Getter
	 * @return Mage_Customer_Model_Customer
	 */
	public function getCustomer()
	{
		return rm_session_customer()->getCustomer();
	}

	/**
	 * Getter
	 * @return Mage_Sales_Model_Quote
	 */
	public function getQuote() {
		return rm_session_checkout()->getQuote();
	}

	/**
	 * Getter
	 * @return Df_Reward_Model_Reward
	 */
	public function getReward()
	{
		if (!$this->_getData('reward')) {
			$reward = Df_Reward_Model_Reward::i()
				->setCustomer($this->getCustomer())
				->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
				->loadByCustomer();
			$this->setData('reward', $reward);
		}
		return $this->_getData('reward');
	}

	/**
	 * Return flag from quote to use reward points or not
	 * @return boolean
	 */
	public function useRewardPoints() {return !!$this->getQuote()->getUseRewardPoints();}

	/**
	 * Return true if customer can use his reward points.
	 * In case if currency amount of his points more then 0 and has minimum limit of points
	 * @return boolean
	 */
	public function getCanUseRewardPoints()
	{
		if (!df_h()->reward()->getHasRates()
			|| !df_h()->reward()->isEnabledOnFront()) {
			return false;
		}
		$minPointsToUse = df_h()->reward()
			->getGeneralConfig('min_points_balance', (int)Mage::app()->getWebsite()->getId());
		$canUseRewadPoints = ($this->getPointsBalance() >= $minPointsToUse) ? true : false;
		return(boolean)(((float)$this->getCurrencyAmount() > 0) && $canUseRewadPoints);
	}

	/**
	 * Getter
	 * @return integer
	 */
	public function getPointsBalance()
	{
		return $this->getReward()->getPointsBalance();
	}

	/**
	 * Getter
	 * @return float
	 */
	public function getCurrencyAmount()
	{
		return $this->getReward()->getCurrencyAmount();
	}

	/**
	 * Check if customer has enough points to cover total
	 * @return boolean
	 */
	public function isEnoughPoints()
	{
		$baseGrandTotal = $this->getQuote()->getBaseGrandTotal()+$this->getQuote()->getBaseRewardCurrencyAmount();
		return $this->getReward()->isEnoughPointsToCoverAmount($baseGrandTotal);
	}

	const _CLASS = __CLASS__;
	/**
	 * @param string|null $name [optional]
	 * @return Df_Reward_Block_Checkout_Payment_Additional
	 */
	public static function i($name = null) {return df_block(__CLASS__, $name);}
}