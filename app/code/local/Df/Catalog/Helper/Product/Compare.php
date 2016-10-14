<?php
class Df_Catalog_Helper_Product_Compare extends Mage_Catalog_Helper_Product_Compare {
	/**
	 * Цель перекрытия —
	 * предоставить администратору возможность скрывать ссылку «сравнить»
	 * (с витринной товарной карточки и с мини-карточкек товаров на страницах товарных разделов).
	 * @override
	 * @param  Mage_Catalog_Model_Product $product
	 * @return  string|null
	 */
	public function getAddUrl(
		/**
		 * Мы не можем явно указать тип параметра $product,
		 * потому что иначе интерпретатор сделает нам замечание:
		 * «Strict Notice: Declaration of Df_Catalog_Helper_Product_Compare::getAddUrl()
		 * should be compatible with that of Mage_Catalog_Helper_Product_Compare::getAddUrl()»
		 */
		$product
	) {
		return
			$this->needHideLink_addToCompare()
			? null
			: parent::_getUrl('catalog/product_compare/add', $this->_getUrlParams($product))
		;
	}

	/** @return bool */
	private function needHideLink_addToCompare() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Tweaks_Model_Settings_Catalog_Product $settings */
			$settings = df_cfg()->tweaks()->catalog()->product();
			$this->{__METHOD__} =
					df_module_enabled(Df_Core_Module::TWEAKS)
				&&
					(
							(
									rm_handle_presents(Df_Core_Model_Layout_Handle::CATALOG_PRODUCT_VIEW)
								&&
									$settings->view()->needHideAddToCompare()
							)
						||
							(
									$settings->_list()->needHideAddToCompare()
								&&
									df_h()->tweaks()->isItCatalogProductList()
							)
					)
			;
		}
		return $this->{__METHOD__};
	}
}