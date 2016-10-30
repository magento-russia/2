<?php
class Df_Wishlist_Block_Customer_Sidebar extends Mage_Wishlist_Block_Customer_Sidebar {
	/**
	 * @override
	 * @see Mage_Core_Block_Template::getCacheKeyInfo()
	 * @used-by Df_Core_Block_Abstract::getCacheKey()
	 * @return string[]
	 */
	public function getCacheKeyInfo() {
		/** @var string[] $result */
		$result = parent::getCacheKeyInfo();
		if (
				df_module_enabled(Df_Core_Module::SPEED)
			&&
				df_cfgr()->speed()->blockCaching()->wishlistCustomerSidebar()
		) {
			$result = array_merge($result, array(get_class($this)), $this->getProductIds());
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
				df_cfgr()->speed()->blockCaching()->wishlistCustomerSidebar()
		) {
			/**
			 * Чтобы блок кэшировался стандартным, заложенным в @see Mage_Core_Block_Abstract способом,
			 * продолжительность хранения кэша надо указывать обязательно,
			 * потому что значением продолжительности по умолчанию является «null»,
			 * что в контексте @see Mage_Core_Block_Abstract
			 * (и в полную противоположность Zend Framework
			 * и всем остальным частям Magento, где используется кэширование)
			 * означает, что блок не удет кэшироваться вовсе!
			 * @used-by Mage_Core_Block_Abstract::_loadCache()
			 */
			$this->setData('cache_lifetime', Df_Core_Block_Template::CACHE_LIFETIME_STANDARD);
		}
	}

	/** @return int[] */
	private function getProductIds() {
		// План покупок могут иметь только авторизованные посетители.
		// Исключение попытки получения плана покупок для неавторизованных посетителей
		// значительно ускоряет систему.
		/** @uses Mage_Wishlist_Model_Item::getProductId() */
		return !df_customer_logged_in() ? array() : $this->getWishlistItems()->walk('getProductId');
	}
}