<?php
class Df_Catalog_Block_Product_List_Related extends Mage_Catalog_Block_Product_List_Related {
	/**
	 * @override
	 * @see Mage_Core_Block_Template::getCacheKeyInfo()
	 * @used-by Df_Core_Block_Abstract::getCacheKey()
	 * @return string[]
	 */
	public function getCacheKeyInfo() {
		return array_merge(parent::getCacheKeyInfo(), array(get_class($this), $this->getProduct()->getId()));
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		if (!df_quote_has_items()) {
			$this->addData(array(
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
				'cache_lifetime' => Df_Core_Block_Template::CACHE_LIFETIME_STANDARD
				/**
				 * При такой инициализации тегов
				 * (без перекрытия метода @see Mage_Core_Block_Abstract::getCacheTags())
				 * тег @see Mage_Core_Block_Abstract::CACHE_GROUP будет добавлен автоматически.
				 * @used-by Mage_Core_Block_Abstract::getCacheTags()
				 */
				,'cache_tags' => array(Mage_Catalog_Model_Product::CACHE_TAG)
			));
		}
	}
}