<?php
/**
 * User column filter for Event Log grid
 */
class Df_Logging_Block_Adminhtml_Grid_Filter_User extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select {
	/**
	 * Build filter options list
	 * @return array
	 */
	public function _getOptions()
	{
		$options = array(array('value' => '', 'label' => Df_Logging_Helper_Data::s()->__('All Users')));
		foreach (Mage::getResourceModel('df_logging/event')->getUserNames() as $username) {
			$options[]= array('value' => $username, 'label' => $username);
		}
		return $options;
	}

	/**
	 * Filter condition getter
	 *
	 * @string
	 */
	public function getCondition()
	{
		return $this->getValue();
	}
}