<?php
/**
 * Reward tab block
 */
class Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward
	extends Df_Core_Block_Admin
	implements Mage_Adminhtml_Block_Widget_Tab_Interface {
	/**
	 * Return tab label
	 * @return string
	 */
	public function getTabLabel() {return df_h()->reward()->__('Reward Points');}

	/**
	 * Return tab title
	 * @return string
	 */
	public function getTabTitle() {return df_h()->reward()->__('Reward Points');}

	/**
	 * Check if can show tab
	 * @return boolean
	 */
	public function canShowTab() {
		$customer = Mage::registry('current_customer');
		return $customer->getId() && df_h()->reward()->isEnabled() && rm_admin_allowed('df_reward/balance');
	}

	/**
	 * Check if tab hidden
	 * @return boolean
	 */
	public function isHidden() {return false;}

	/**
	 * Prepare layout.
	 * Add accordion items
	 * @return Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward
	 */
	protected function _prepareLayout() {
		$accordion = rm_block_l('adminhtml/widget_accordion');
		$accordion->addItem('reward_points_history', array(
			'title'	=> df_h()->reward()->__('Reward Points History')
			,'open' => false
			,'class' => ''
			,'ajax' => true
			,'content_url' => $this->getUrl('*/customer_reward/history'
			, array('_current' => true))
		));
		$this->setChild('history_accordion', $accordion);
		return parent::_prepareLayout();
	}

	/**
	 * Precessor tab ID getter
	 * @return string
	 */
	public function getAfter() {return 'tags';}
}