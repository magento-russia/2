<?php
class Df_Checkout_Block_Cart_Sidebar extends Mage_Checkout_Block_Cart_Sidebar {
	/**
	 * @override
	 * @return string[]
	 */
	public function getCacheKeyInfo() {
		/** @var string[] $result */
		$result = parent::getCacheKeyInfo();
		if (rm_session_checkout()->getQuote()->getId()) {
			$result = array_merge($result, $this->getAdditionalKeys());
		}
		return $result;
	}

	/** @return string[] */
	private function getAdditionalKeys() {
		/** @var string[] $result */
		$result = array($this->getSummaryCount(), $this->getSubtotal());
		foreach ($this->getRecentItems() as $quoteItem) {
			/** @var Mage_Sales_Model_Quote_Item $quoteItem */
			$result[]= $quoteItem->getProductId();
			$result[]= $quoteItem->getQty();
		}
		return $result;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		if (
				df_module_enabled(Df_Core_Module::SPEED)
			&&
				df_cfg()->speed()->blockCaching()->checkoutCartSidebar()
		) {
			/**
			 * Чтобы блок кэшировался стандартным, заложенным в @see Mage_Core_Block_Abstract способом,
			 * продолжительность хранения кэша надо указывать обязательно,
			 * потому что значением продолжительности по умолчанию является «null»,
			 * что в контексте @see Mage_Core_Block_Abstract
			 * (и в полную противоположность Zend Framework
			 * и всем остальным частям Magento, где используется кэширование)
			 * означает, что блок не удет кэшироваться вовсе!
			 * @see Mage_Core_Block_Abstract::_loadCache()
			 */
			$this->setData('cache_lifetime', Df_Core_Block_Template::CACHE_LIFETIME_STANDARD);
		}
	}
}