<?php
/**
 * Reward rate grid container
 */
class Df_Reward_Block_Adminhtml_Reward_Rate extends Mage_Adminhtml_Block_Widget_Grid_Container {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_blockGroup = 'df_reward';
		$this->_controller = 'adminhtml_reward_rate';
		$this->_headerText = df_h()->reward()->__('Manage Reward Exchange Rates');
		$this->_updateButton('add', 'label', df_h()->reward()->__('Add New Rate'));
	}
}