<?php
class Df_Reward_Block_Tooltip extends Df_Core_Block_Template_NoCache {
	/**
	 * Reward instance
	 *
	 * @var Df_Reward_Model_Reward
	 */
	protected $_rewardInstance = null;

	/**
	 * Reward action instance
	 *
	 * @var Df_Reward_Model_Action_Abstract
	 */
	protected $_actionInstance = null;

	/**
	 * @param $action
	 * @return void
	 */
	public function initRewardType($action){
		if ($action && df_h()->reward()->isEnabledOnFront()) {
			$customer = df_session_customer()->getCustomer();
			$this->_rewardInstance = Df_Reward_Model_Reward::s()
				->setCustomer($customer)
				->setWebsiteId(rm_website_id())
				->loadByCustomer()
			;
			$this->_actionInstance = $this->_rewardInstance->getActionInstance($action, true);
		}
	}

	/**
	 * Getter for amount customer may be rewarded for current action
	 * Can format as currency
	 *
	 * @param float|null $amount [optional]
	 * @param bool $asCurrency [optional]
	 * @return string|null
	 */
	public function getRewardAmount($amount = null, $asCurrency = false) {
		$amount = null === $amount ? $this->_getData('reward_amount') : $amount;
		return df_h()->reward()->formatAmount($amount, $asCurrency);
	}

	public function renderLearnMoreLink($format = '<a href="%1$s">%2$s</a>', $anchorText = null)
	{
		$anchorText = null === $anchorText ? df_h()->reward()->__('Learn more...') : $anchorText;
		return df_sprintf($format, $this->getLandingPageUrl(), $anchorText);
	}

	/**
	 * Set various template variables
	 */
	protected function _prepareTemplateData()
	{
		if ($this->_actionInstance) {
			$this->addData(array(
				'reward_points' =>
					$this->_rewardInstance->estimateRewardPoints($this->_actionInstance),'landing_page_url' => df_h()->reward()->getLandingPageUrl(),));
			if ($this->_rewardInstance->getId()) {
				// estimate qty limitations (actually can be used without customer reward record)
				$qtyLimit = $this->_actionInstance->estimateRewardsQtyLimit();
				if (null !== $qtyLimit) {
					$this->setData('qty_limit', $qtyLimit);
				}

				if ($this->hasGuestNote()) {
					$this->unsGuestNote();
				}

				$this->addData(array(
					'points_balance' => $this->_rewardInstance->getPointsBalance(),'currency_balance' => $this->_rewardInstance->getCurrencyAmount(),));
				// estimate monetary reward
				$amount = $this->_rewardInstance->estimateRewardAmount($this->_actionInstance);
				if (null !== $amount) {
					$this->setData('reward_amount', $amount);
				}
			} else {
				if ($this->hasIsGuestNote() && !$this->hasGuestNote()) {
					$this->setGuestNote(df_h()->reward()->__('Applies only to registered customers, may vary when logged in.'));
				}
			}
		}
	}

	/**
	 * @override
	 * @see Mage_Core_Block_Template::_toHtml()
	 * @used-by Mage_Core_Block_Abstract::toHtml()
	 * @return string
	 */
	protected function _toHtml() {
		$this->_prepareTemplateData();
		if (!$this->_actionInstance || !$this->getRewardPoints() || $this->hasQtyLimit() && !$this->getQtyLimit()) {
			return '';
		}
		return parent::_toHtml();
	}
}