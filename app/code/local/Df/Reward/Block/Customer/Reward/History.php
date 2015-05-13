<?php
class Df_Reward_Block_Customer_Reward_History extends Df_Core_Block_Template_NoCache {
	/**
	 * History records collection
	 *
	 * @var Df_Reward_Model_Resource_Reward_History_Collection
	 */
	protected $_collection = null;

	/**
	 * Get history collection if needed
	 * @return Df_Reward_Model_Resource_Reward_History_Collection|bool
	 */
	public function getHistory() {
		if (0 == $this->_getCollection()->getSize()) {
			return false;
		}
		return $this->_collection;
	}

	/**
	 * @param Df_Reward_Model_Reward_History $item
	 * @return string
	 */
	public function getPointsDelta(Df_Reward_Model_Reward_History $item) {
		return df_h()->reward()->formatPointsDelta($item->getPointsDelta());
	}

	/**
	 * @param Df_Reward_Model_Reward_History $item
	 * @return string
	 */
	public function getPointsBalance(Df_Reward_Model_Reward_History $item) {
		return $item->getPointsBalance();
	}

	/**
	 * @param Df_Reward_Model_Reward_History $item
	 * @return string
	 */
	public function getCurrencyBalance(Df_Reward_Model_Reward_History $item) {
		return df_mage()->coreHelper()->currency($item->getCurrencyAmount());
	}

	/**
	 * @param Df_Reward_Model_Reward_History $item
	 * @return string
	 */
	public function getMessage(Df_Reward_Model_Reward_History $item) {return $item->getMessage();}

	/**
	 * History item reference additional explanation getter
	 *
	 * @param Df_Reward_Model_Reward_History $item
	 * @return string
	 */
	public function getExplanation(Df_Reward_Model_Reward_History $item) {
		return ''; // TODO
	}

	/**
	 * History item creation date getter
	 *
	 * @param Df_Reward_Model_Reward_History $item
	 * @return string
	 */
	public function getDate(Df_Reward_Model_Reward_History $item)
	{
		return df_mage()->coreHelper()->formatDate($item->getCreatedAt(), 'short', true);
	}

	/**
	 * History item expiration date getter
	 *
	 * @param Df_Reward_Model_Reward_History $item
	 * @return string
	 */
	public function getExpirationDate(Df_Reward_Model_Reward_History $item)
	{
		$expiresAt = $item->getExpiresAt();
		if ($expiresAt) {
			return df_mage()->coreHelper()->formatDate($expiresAt, 'short', true);
		}
		return '';
	}

	/**
	 * Return reword points update history collection by customer and website
	 * @return Df_Reward_Model_Resource_Reward_History_Collection
	 */
	protected function _getCollection()
	{
		if (!$this->_collection) {
			$websiteId = Mage::app()->getWebsite()->getId();
			$this->_collection =
				Df_Reward_Model_Resource_Reward_History_Collection::i()
					->addCustomerFilter(rm_session_customer()->getCustomerId())
					->addWebsiteFilter($websiteId)
					->setExpiryConfig(df_h()->reward()->getExpiryConfig())
					->addExpirationDate($websiteId)
					->skipExpiredDuplicates()
					->setDefaultOrder()
			;
		}
		return $this->_collection;
	}

	/**
	 * Instantiate Pagination
	 * @return Df_Reward_Block_Customer_Reward_History
	 */
	protected function _prepareLayout()
	{
		if ($this->_isEnabled()) {
			$pager = df_block('page/html_pager', 'reward.history.pager')
				->setCollection($this->_getCollection())->setIsOutputRequired(false)
			;
			$this->setChild('pager', $pager);
		}
		return parent::_prepareLayout();
	}

	/**
	 * Whether the history may show up
	 * @return string
	 */
	protected function _toHtml()
	{
		if ($this->_isEnabled()) {
			return parent::_toHtml();
		}
		return '';
	}

	/**
	 * Whether the history is supposed to be rendered
	 * @return bool
	 */
	protected function _isEnabled()
	{
		return df_h()->reward()->isEnabledOnFront()
			&& df_h()->reward()->getGeneralConfig('publish_history');
	}
}