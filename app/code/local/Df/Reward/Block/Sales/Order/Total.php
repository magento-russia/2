<?php
class Df_Reward_Block_Sales_Order_Total extends Df_Core_Block_Template_NoCache {
	/**
	 * Get label cell tag properties
	 * @return string
	 */
	public function getLabelProperties()
	{
		return $this->getParentBlock()->getLabelProperties();
	}

	/**
	 * Get order store object
	 * @return Mage_Sales_Model_Order
	 */
	public function getOrder()
	{
		return $this->getParentBlock()->getOrder();
	}

	/**
	 * Get totals source object
	 * @return Mage_Sales_Model_Order
	 */
	public function getSource()
	{
		return $this->getParentBlock()->getSource();
	}

	/**
	 * Get value cell tag properties
	 * @return string
	 */
	public function getValueProperties()
	{
		return $this->getParentBlock()->getValueProperties();
	}

	/**
	 * Initialize reward points totals
	 * @return Df_Reward_Block_Sales_Order_Total
	 */
	public function initTotals() {
		if ((float)$this->getOrder()->getBaseRewardCurrencyAmount()) {
			$source = $this->getSource();
			$value  = - $source->getRewardCurrencyAmount();
			$this->getParentBlock()->addTotal(new Varien_Object(array(
				'code' => 'reward_points'
				,'strong' => false
				,'label' => df_h()->reward()->formatReward($source->getRewardPointsBalance())
				,'value' => $source instanceof Mage_Sales_Model_Order_Creditmemo ? - $value : $value
			)));
		}
		return $this;
	}
}