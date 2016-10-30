<?php
class Df_Catalog_Block_Product_List extends Mage_Catalog_Block_Product_List {
	/**
	 * Цель перекрытия —
	 * представить администратору возможность либо скрыть кнопку «в корзину»,
	 * либо заменить её на кнопку «подробнее...».
	 * @override
	 * @return string
	 */
	public function __() {
		/** @var mixed[] $fa */
		$fa = func_get_args();
		/** @var string $result */
		$result = df_translate($fa, 'Mage_Catalog');
		if (df_module_enabled(Df_Core_Module::TWEAKS)) {
			if (
					(
							df_handle(Df_Core_Model_Layout_Handle::CATALOG_PRODUCT_VIEW)
						&&
							df_cfgr()->tweaks()->catalog()->product()->view()->needHideAddToCart()
					)
				||
					(
							df_cfgr()->tweaks()->catalog()->product()->_list()->needHideAddToCart()
						&&
							df_h()->tweaks()->isItCatalogProductList()
					)
			) {
				$textToTranslate = dfa($fa, 0);
				if (is_string($textToTranslate)) {
					switch($textToTranslate) {
						case 'Out of stock':
							$result = '';
							break;
					}
				}
			}
			if (
					df_h()->tweaks()->isItCatalogProductList()
				&&
					df_cfgr()->tweaks()->catalog()->product()->_list()->needReplaceAddToCartWithMore()
			) {
				$textToTranslate = dfa($fa, 0);
				if (is_string($textToTranslate)) {
					switch($textToTranslate) {
						case 'Add to Cart':
							$result = parent::__('More...');
							break;
					}
				}
			}
		}
		return $result;
	}

	/**
	 * Цель перекрытия —
	 * представить администратору возможность
	 * заменить кнопку «в корзину» на кнопку «подробнее...».
	 * @override
	 * @param Mage_Catalog_Model_Product $product
	 * @param array $additional
	 * @return string
	 */
	public function getAddToCartUrl($product, $additional = array()) {
		return
			(
					df_module_enabled(Df_Core_Module::TWEAKS)
				&&
					(
							df_handle(Df_Core_Model_Layout_Handle::CATALOG_CATEGORY_VIEW)
						||
							df_handle(Df_Core_Model_Layout_Handle::CMS_PAGE)
					)
				&&
					df_cfgr()->tweaks()->catalog()->product()->_list()->needReplaceAddToCartWithMore()
			)
		?
			parent::getProductUrl($product, $additional)
		:
			parent::getAddToCartUrl($product, $additional)
		;
	}

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
				df_cfgr()->speed()->blockCaching()->catalogProductList()
		) {
			$result = array_merge($result, array(
				get_class($this)
				/**
				 * Здесь @see md5() не нужно,
				 * потому что @used-by Mage_Core_Block_Abstract::getCacheKey()
				 * использует аналогичную функцию @uses sha1()
				 */
				,$this->getRequest()->getRequestUri()
				,df_store()->getCurrentCurrencyCode()
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
				df_cfgr()->speed()->blockCaching()->catalogProductList()
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