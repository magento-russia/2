<?php
class Df_Cms_Block_Page extends Mage_Cms_Block_Page {
	/**
	 * @override
	 * @see Mage_Core_Block_Abstract::getCacheKeyInfo()
	 * @used-by Df_Core_Block_Abstract::getCacheKey()
	 * @return string[]
	 */
	public function getCacheKeyInfo() {
		/** @var string[] $result */
		$result = parent::getCacheKeyInfo();
		if (
				df_module_enabled(Df_Core_Module::SPEED)
			&&
				df_cfgr()->speed()->blockCaching()->cmsPage()
		) {
			$result = array_merge($result, array(
				get_class($this)
				,$this->getPage()->getId()
				/**
				 * Здесь @see md5() не нужно,
				 * потому что @used-by Mage_Core_Block_Abstract::getCacheKey()
				 * использует аналогичную функцию @uses sha1()
				 */
				,$this->getMessagesBlock()->toHtml()
			));
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
				df_cfgr()->speed()->blockCaching()->cmsPage()
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
}