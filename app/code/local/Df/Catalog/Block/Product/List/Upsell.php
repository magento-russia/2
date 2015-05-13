<?php
class Df_Catalog_Block_Product_List_Upsell extends Mage_Catalog_Block_Product_List_Upsell {
	/**
	 * @override
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
		if (
				!df_mage()->catalogHelper()->isModuleEnabled('Mage_Checkout')
			||
				!rm_session_checkout()->getQuoteId()
			||
				!rm_session_checkout()->getQuote()->getItemsCount()
		) {
			$this->addData(array(
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
				'cache_lifetime' => Df_Core_Block_Template::CACHE_LIFETIME_STANDARD
				/**
				 * При такой инициализации тегов
				 * (без перекрытия метода @see Mage_Core_Block_Abstract::getCacheTags())
				 * тег Mage_Core_Block_Abstract::CACHE_GROUP будет добавлен автоматически.
				 * @see Mage_Core_Block_Abstract::getCacheTags()
				 */
				,'cache_tags' => array(Mage_Catalog_Model_Product::CACHE_TAG)
			));
		}
	}
}