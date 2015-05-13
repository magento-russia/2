<?php
class Df_Invitation_Model_Adminhtml_System_Config_Backend_Cache
	extends Mage_Adminhtml_Model_System_Config_Backend_Cache {
	/**
	 * Cache tags to clean
	 *
	 * @var array
	 */
	protected $_cacheTags = array(
		Mage_Adminhtml_Block_Page_Menu::CACHE_TAGS
	);
}