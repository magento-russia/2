<?php
class Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_History extends Df_Core_Block_Admin {
	/** @return int */
	public function getCustomerId() {return $this[self::$P__CUSTOMER_ID];}

	/**
	 * @override
	 * @return Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_History
	 */
	protected function _prepareLayout() {
		$this->setChild('grid',
			Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_History_Grid::i($this->getCustomerId())
		);
		return parent::_prepareLayout();
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__CUSTOMER_ID, DF_V_NAT0);
		$this->setTemplate('df/reward/customer/edit/history.phtml');
	}
	/** @var string */
	private static $P__CUSTOMER_ID = 'customer_id';

	/**
	 * @used-by Df_Reward_Adminhtml_Customer_RewardController::historyAction()
	 * @param int $customerId
	 * @return Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_History
	 */
	public static function i($customerId) {
		return df_block_l(new self(array(self::$P__CUSTOMER_ID => $customerId)));
	}
}