<?php
class Df_Core_Helper_Mage_Adminhtml extends Mage_Core_Helper_Abstract {
	/** @return Mage_Adminhtml_Helper_Data */
	public function helper() {return Mage::helper('adminhtml');}
	/** @return Df_Core_Helper_Mage_Adminhtml_Html */
	public function html() {return Df_Core_Helper_Mage_Adminhtml_Html::s();}
	/** @return Df_Core_Helper_Mage_Adminhtml_System */
	public function system() {return Df_Core_Helper_Mage_Adminhtml_System::s();}
	/** @return Mage_Adminhtml_Model_Url */
	public function urlSingleton() {return Mage::getSingleton('adminhtml/url');}
	/** @return Mage_Adminhtml_Model_System_Config_Source_Yesno */
	public function yesNo() {return Mage::getSingleton('adminhtml/system_config_source_yesno');}
	/** @return Df_Core_Helper_Mage_Adminhtml */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}