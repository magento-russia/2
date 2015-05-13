<?php
interface Df_Catalog_Block_Navigation_Submenu {
	/**
	 * @abstract
	 * @param Varien_Data_Tree $additionalRoot
	 * @return Mage_Core_Block_Abstract
	 */
	function appendMenu(Varien_Data_Tree $additionalRoot);
}