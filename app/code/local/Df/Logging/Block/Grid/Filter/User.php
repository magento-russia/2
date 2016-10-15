<?php
/**
 * User column filter for Event Log grid
 */
class Df_Logging_Block_Grid_Filter_User extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select {
	/**
	 * Build filter options list
	 * @return array
	 */
	public function _getOptions() {
		$options = array(df_option('', Df_Logging_Helper_Data::s()->__('All Users')));
		foreach (Df_Logging_Model_Resource_Event::s()->getUserNames() as $username) {
			/** @var string $username */
			$options[]= df_option($username, $username);
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