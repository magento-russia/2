<?php
class Df_Reward_Block_Adminhtml_Reward_Rate_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
	/**
	 * Перекрывать надо именно конструктор, а не метод _construct,
	 * потому что родительский класс пихает инициализацию именно в конструктор.
	 * @override
	 * @return Df_Reward_Block_Adminhtml_Reward_Rate_Edit
	 */
	public function __construct() {
		parent::__construct();
		$this->_objectId = 'rate_id';
		$this->_blockGroup = 'df_reward';
		$this->_controller = 'adminhtml_reward_rate';
	}

	/**
	 * Getter.
	 * Return header text in order to create or edit rate
	 * @return string
	 */
	public function getHeaderText()
	{
		if (Mage::registry('current_reward_rate')->getId()) {
			return df_h()->reward()->__('Edit Reward Exchange Rate');
		} else {
			return df_h()->reward()->__('New Reward Exchange Rate');
		}
	}

	/**
	 * rate validation URL getter
	 *
	 */
	public function getValidationUrl()
	{
		return $this->getUrl('*/*/validate', array('_current'=>true));
	}
}