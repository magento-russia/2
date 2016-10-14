<?php
/**
 * @method float|null getCurrencyAmount()
 */
class Df_Reward_Block_Adminhtml_Sales_Order_Create_Payment extends Df_Core_Block_Admin {
	/**
	 * Getter
	 * @return Mage_Adminhtml_Model_Sales_Order_Create
	 */
	protected function _getOrderCreateModel() {
		return Mage::getSingleton('adminhtml/sales_order_create');
	}

	/** @return Mage_Sales_Model_Quote */
	public function getQuote() {return $this->_getOrderCreateModel()->getQuote();}

	/**
	 * Check whether can use customer reward points
	 * @return boolean
	 */
	public function canUseRewardPoints() {
		return
				(float)$this->getCurrencyAmount()
			&&
				df_h()->reward()->isEnabledOnFront(
					rm_store($this->getQuote()->getStoreId())->getWebsiteId()
				)
		;
	}

	/**
	 * Getter.
	 * Retrieve reward points model
	 * @return Df_Reward_Model_Reward
	 */
	public function getReward() {
		if (!$this->_getData('reward')) {
			/* @var $reward Df_Reward_Model_Reward */
			$reward = Df_Reward_Model_Reward::i()
				->setCustomer($this->getQuote()->getCustomer())
				->setStore($this->getQuote()->getStore())
				->loadByCustomer();
			$this->setData('reward', $reward);
		}
		return $this->_getData('reward');
	}

	/**
	 * @override
	 * @see Df_Core_Block_Admin::_toHtml()
	 * @used-by Mage_Core_Block_Abstract::toHtml()
	 * @return string
	 */
	protected function _toHtml()
	{
		$points = $this->getReward()->getPointsBalance();
		$amount = $this->getReward()->getCurrencyAmount();
		$rewardFormatted = df_h()->reward()
			->formatReward($points, $amount, $this->getQuote()->getStore()->getId());
		$this->setPointsBalance($points)->setCurrencyAmount($amount)
			->setUseLabel($this->__('Use my reward points, %s available', $rewardFormatted))
		;
		return parent::_toHtml();
	}

	/**
	 * Check if reward points applied in quote
	 * @return boolean
	 */
	public function useRewardPoints() {
		return !!$this->_getOrderCreateModel()->getQuote()->getUseRewardPoints();
	}
}