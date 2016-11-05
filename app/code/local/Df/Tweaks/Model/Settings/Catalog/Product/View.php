<?php
class Df_Tweaks_Model_Settings_Catalog_Product_View extends Df_Core_Model_Settings {
	/** @return boolean */
	public function needHideAddToCart() {return $this->getYesNo('add_to_cart');}
	/** @return boolean */
	public function needHideAddToCompare() {return $this->getYesNo('add_to_compare');}
	/** @return boolean */
	public function needHideAddReviewLink() {return $this->getYesNo('add_review_link');}
	/** @return boolean */
	public function needHideAddToWishlist() {return $this->getYesNo('add_to_wishlist');}
	/** @return boolean */
	public function needHideAvailability() {return $this->getYesNo('availability');}
	/** @return boolean */
	public function needHideEmailToFriend() {return $this->getYesNo('email_to_friend');}
	/** @return boolean */
	public function needHideEmptyAttributes() {return $this->getYesNo('empty_attributes');}
	/** @return boolean */
	public function needHidePrice() {return $this->getYesNo('price');}
	/** @return boolean */
	public function needHideProductNameFromBreadcrumbs() {
		return $this->getYesNo('product_name_from_breadcrumbs');
	}
	/** @return boolean */
	public function needHideRating() {return $this->getYesNo('rating');}
	/** @return boolean */
	public function needHideReviewsLink() {return $this->getYesNo('reviews_link');}
	/** @return boolean */
	public function needHideShortDescription() {return $this->getYesNo('short_description');}
	/** @return boolean */
	public function needHideTags() {return $this->getYesNo('tags');}
	/** @return Df_Tweaks_Model_Settings_Catalog_Product_View_Sku */
	public function sku() {return Df_Tweaks_Model_Settings_Catalog_Product_View_Sku::s();}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_tweaks/catalog_product_view/hide_';}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}