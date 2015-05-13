<?php
class Df_Tweaks_Model_Settings_Catalog_Product_List extends Df_Core_Model_Settings {
	/** @return boolean */
	public function needHideAddToCart() {return $this->getYesNo('hide_add_to_cart');}
	/** @return boolean */
	public function needHideAddToCompare() {return $this->getYesNo('hide_add_to_compare');}
	/** @return boolean */
	public function needHideAddToWishlist() {return $this->getYesNo('hide_add_to_wishlist');}
	/** @return boolean */
	public function needHidePrice() {return $this->getYesNo('hide_price');}
	/** @return boolean */
	public function needHideRating() {return $this->getYesNo('hide_rating');}
	/** @return boolean */
	public function needHideReviews() {return $this->getYesNo('hide_reviews');}
	/** @return boolean */
	public function needReplaceAddToCartWithMore() {return $this->getYesNo('replace_add_to_cart_with_more');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_tweaks/catalog_product_list/';}
	/** @return Df_Tweaks_Model_Settings_Catalog_Product_List */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}