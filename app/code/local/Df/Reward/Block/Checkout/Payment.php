<?php
class Df_Reward_Block_Checkout_Payment extends Df_Core_Block_Template_NoCache {
	/** @return Df_Customer_Model_Customer */
	public function getCustomer() {return df_session_customer()->getCustomer();}

	/**
	 * Getter
	 * @return Df_Reward_Model_Reward
	 */
	public function getReward() {
		if (!$this->_getData('reward')) {
			$reward = Df_Reward_Model_Reward::i()
				->setCustomer($this->getCustomer())
				->setWebsiteId(df_website_id())
				->loadByCustomer();
			$this->setData('reward', $reward);
		}
		return $this->_getData('reward');
	}

	/**
	 * Return flag from quote to use reward points or not
	 * @return boolean
	 */
	public function useRewardPoints() {return !!df_quote()->getUseRewardPoints();}

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
		$minPointsToUse = df_h()->reward()->getGeneralConfig('min_points_balance', df_website_id());
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
		$baseGrandTotal = df_quote()->getBaseGrandTotal() + df_quote()->getBaseRewardCurrencyAmount();
		return $this->getReward()->isEnoughPointsToCoverAmount($baseGrandTotal);
	}

	/**
	 * @param string $suffix
	 * @return string
	 */
	public static function html($suffix) {
		/** @var array(string => string) $cache */
		static $cache;
		if (!isset($cache[$suffix])) {
			$cache[$suffix] = df_render(__CLASS__, "df/reward/checkout/payment/{$suffix}.phtml");
		}
		return $cache[$suffix];
	}
}