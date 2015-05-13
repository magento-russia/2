<?php
class Df_Page_Block_Js_Cookie extends Mage_Page_Block_Js_Cookie {
	/**
	 * @override
	 * @return string[]
	 */
	public function getCacheKeyInfo() {
		return array_merge(parent::getCacheKeyInfo(), array(
			get_class($this)
			,$this->getDomain()
			,$this->getPath()
		));
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
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