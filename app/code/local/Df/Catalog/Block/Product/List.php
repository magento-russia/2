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
		$fa = func_get_args();
		/**
		 * Раньше вместо @see Df_Localization_Helper_Translation::translateByModule()
		 * тут стоял вызов @see Df_Localization_Helper_Translation::translateByParent().
		 * translateByModule точнее, потому что translateByParent не будет работать,
		 * если класс Df_Catalog_Block_Product_List перекрыт его потомком
		 * (такое бывает иногда при адаптации сторонних оформительских тем).
		 */
		$result = rm_translate($fa, 'Mage_Catalog');
		if (df_module_enabled(Df_Core_Module::TWEAKS) && df_enabled(Df_Core_Feature::TWEAKS)) {
			if (
					(
							rm_handle_presents(Df_Core_Model_Layout_Handle::CATALOG_PRODUCT_VIEW)
						&&
							df_cfg()->tweaks()->catalog()->product()->view()->needHideAddToCart()
					)
				||
					(
							df_cfg()->tweaks()->catalog()->product()->_list()->needHideAddToCart()
						&&
							df_h()->tweaks()->isItCatalogProductList()
					)
			) {
				$textToTranslate = df_a($fa, 0);
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
					df_cfg()->tweaks()->catalog()->product()->_list()->needReplaceAddToCartWithMore()
			) {
				$textToTranslate = df_a($fa, 0);
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
					df_enabled(Df_Core_Feature::TWEAKS)
				&&
					(
							rm_handle_presents(Df_Core_Model_Layout_Handle::CATALOG_CATEGORY_VIEW)
						||
							rm_handle_presents(Df_Core_Model_Layout_Handle::CMS_PAGE)
					)
				&&
					df_cfg()->tweaks()->catalog()->product()->_list()->needReplaceAddToCartWithMore()
			)
		?
			parent::getProductUrl($product, $additional)
		:
			parent::getAddToCartUrl($product, $additional)
		;
	}

	/**
	 * @override
	 * @return string[]
	 */
	public function getCacheKeyInfo() {
		/** @var string[] $result */
		$result = parent::getCacheKeyInfo();
		if (
				df_module_enabled(Df_Core_Module::SPEED)
			&&
				df_cfg()->speed()->blockCaching()->catalogProductList()
		) {
			$result =
				array_merge(
					$result
					,array(
						get_class($this)
						/**
						 * Здесь md5 не нужно,
						 * потому что @see Mage_Core_Block_Abstract::getCacheKey()
						 * использует аналогичную md5 функцию sha1
						 */
						,$this->getRequest()->getRequestUri()
						,Mage::app()->getStore()->getCurrentCurrencyCode()
					)
				)
			;
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
				df_cfg()->speed()->blockCaching()->catalogProductList()
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