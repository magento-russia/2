<?php
/**
 * 2016-02-08
 * Взял реализацию из 1.9.2.2
 * Реализация _toHtml() из 1.7.0.2:
 * return Mage::app()->getLayout()->getBlock('header')->getWelcome();
 * Она приводит к сбою полностраничного кэширования:
 * Call to a member function getWelcome() on boolean.
 */
class Df_Page_Block_Html_WelcomeM extends Mage_Page_Block_Html_Welcome {
	/**
	 * Get customer session
	 *
	 * @return Mage_Customer_Model_Session
	 */
	protected function _getSession()
	{
		return Mage::getSingleton('customer/session');
	}

	/**
	 * Get block message
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		if (empty($this->_data['welcome'])) {
			if (Mage::isInstalled() && $this->_getSession()->isLoggedIn()) {
				$this->_data['welcome'] = $this->__('Welcome, %s!', $this->escapeHtml($this->_getSession()->getCustomer()->getName()));
			} else {
				$this->_data['welcome'] = Mage::getStoreConfig('design/header/welcome');
			}
		}

		return $this->_data['welcome'];
	}

	/**
	 * Get tags array for saving cache
	 *
	 * @return array
	 */
	public function getCacheTags()
	{
		/**
		 * 2016-12-12
		 * Метод @uses Mage_Core_Block_Abstract::addModelTags() отсутствует в версиях < 1.8.
		 */
		if ($this->_getSession()->isLoggedIn() && method_exists($this, 'addModelTags')) {
			$this->addModelTags($this->_getSession()->getCustomer());
		}

		return parent::getCacheTags();
	}
}