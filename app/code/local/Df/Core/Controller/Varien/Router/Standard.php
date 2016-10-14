<?php
class Df_Core_Controller_Varien_Router_Standard extends Mage_Core_Controller_Varien_Router_Standard {
	/**
	 * @override
	 * @return string
	 */
	protected function _getDefaultPath() {
		/** @var string $result */
		$result = Df_Core_Model_Design_Package::s()->getDefaultRoute();
		/**
		 * Убрал проверку результата ради модуля Mage-World CMS Pro.
		 * http://magento-forum.ru/topic/3696/
		 */
		return $result ? $result : parent::_getDefaultPath();
	}
}