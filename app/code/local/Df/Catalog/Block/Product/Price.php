<?php
class Df_Catalog_Block_Product_Price extends Mage_Catalog_Block_Product_Price {
	/**
	 * @override
	 * @see Mage_Core_Block_Template::getCacheKeyInfo()
	 * @used-by Df_Core_Block_Abstract::getCacheKey()
	 * @return string[]
	 */
	public function getCacheKeyInfo() {
		return array_merge(parent::getCacheKeyInfo(), array(
			get_class($this)
			,$this->getProduct()->getId()
			,(int)($this->getDisplayMinimalPrice())
			,$this->getIdSuffix()
			,df_store()->getCurrentCurrencyCode()
		));
	}

	/**
	 * Цель перекрытия —
	 * предоставить администратору возможность скрывать с витрины ценники.
	 * @override
	 * @return string|null
	 */
	public function getTemplate() {
		/** @var bool $needToHide */
		static $needToHide;
		if (is_null($needToHide)) {
			$needToHide =
				df_module_enabled(Df_Core_Module::TWEAKS)
				&&
					(
							df_handle(Df_Core_Model_Layout_Handle::CATALOG_PRODUCT_VIEW)
							&& df_cfgr()->tweaks()->catalog()->product()->view()->needHidePrice()
						||
							df_cfgr()->tweaks()->catalog()->product()->_list()->needHidePrice()
							&& df_h()->tweaks()->isItCatalogProductList()
					)
			;
		}
		return $needToHide ? null : parent::getTemplate();
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
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