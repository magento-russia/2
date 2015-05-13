<?php
class Df_Wishlist_Helper_Data extends Mage_Wishlist_Helper_Data {
	/**
	 * Цель перекрытия —
	 * предоставление администратору возможности скрывать ссылку для добавления товара в план покупок
	 * со страницы товара и с мини-карточек товаров со страницы товарного раздела.
	 * @override
	 * @return bool
	 */
	public function isAllow() {
		$result = parent::isAllow();
		if ($result) {
			if (df_module_enabled(Df_Core_Module::TWEAKS) && df_enabled(Df_Core_Feature::TWEAKS)) {
				if (
						(
								rm_handle_presents(Df_Core_Model_Layout_Handle::CATALOG_PRODUCT_VIEW)
							&&
								df_cfg()->tweaks()->catalog()->product()->view()->needHideAddToWishlist()
						)
					||
						(
								df_cfg()->tweaks()->catalog()->product()->_list()->needHideAddToWishlist()
							&&
								df_h()->tweaks()->isItCatalogProductList()
						)
				) {
					$result = false;
				}
			}
		}
		return $result;
	}

	const _CLASS = __CLASS__;

	/** @return Df_Wishlist_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}