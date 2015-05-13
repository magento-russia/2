<?php
class Df_Catalog_Block_Product_Price extends Mage_Catalog_Block_Product_Price {
	/**
	 * @override
	 * @return string[]
	 */
	public function getCacheKeyInfo() {
		return
			array_merge(
				parent::getCacheKeyInfo()
				,array(
					get_class($this)
					,$this->getProduct()->getId()
					,intval($this->getDisplayMinimalPrice())
					,$this->getIdSuffix()
					,Mage::app()->getStore()->getCurrentCurrencyCode()
				)
			)
		;
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
		if (!isset($needToHide)) {
			$needToHide =
					df_module_enabled(Df_Core_Module::TWEAKS)
				&&
					df_enabled(Df_Core_Feature::TWEAKS)
				&&
					(
							(
									rm_handle_presents(Df_Core_Model_Layout_Handle::CATALOG_PRODUCT_VIEW)
								&&
									df_cfg()->tweaks()->catalog()->product()->view()->needHidePrice()
							)
						||
							(
									df_cfg()->tweaks()->catalog()->product()->_list()->needHidePrice()
								&&
									df_h()->tweaks()->isItCatalogProductList()
							)
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