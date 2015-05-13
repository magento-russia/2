<?php
class Df_Tweaks_Model_Settings extends Df_Core_Model_Settings {
	/** @return Df_Tweaks_Model_Settings_Account */
	public function account() {return Df_Tweaks_Model_Settings_Account::s();}
	/** @return Df_Tweaks_Model_Settings_Banners */
	public function banners() {return Df_Tweaks_Model_Settings_Banners::s();}
	/** @return Df_Tweaks_Model_Settings_Remove */
	public function cart() {
		/** @var Df_Tweaks_Model_Settings_Remove $result */
		static $result;
		if (!isset($result)) {
			$result = Df_Tweaks_Model_Settings_Remove::i('cart');
		}
		return $result;
	}
	/** @return Df_Tweaks_Model_Settings_Catalog */
	public function catalog() {return Df_Tweaks_Model_Settings_Catalog::s();}
	/** @return Df_Tweaks_Model_Settings_Checkout */
	public function checkout() {return Df_Tweaks_Model_Settings_Checkout::s();}
	/** @return Df_Tweaks_Model_Settings_Footer */
	public function footer() {return Df_Tweaks_Model_Settings_Footer::s();}
	/** @return Df_Tweaks_Model_Settings_Header */
	public function header() {return Df_Tweaks_Model_Settings_Header::s();}
	/** @return Df_Tweaks_Model_Settings_Jquery */
	public function jquery() {return Df_Tweaks_Model_Settings_Jquery::s();}
	/** @return Df_Tweaks_Model_Settings_Labels */
	public function labels() {return Df_Tweaks_Model_Settings_Labels::s();}
	/** @return Df_Tweaks_Model_Settings_Newsletter */
	public function newsletter() {return Df_Tweaks_Model_Settings_Newsletter::s();}
	/** @return Df_Tweaks_Model_Settings_Paypal */
	public function paypal() {return Df_Tweaks_Model_Settings_Paypal::s();}
	/** @return Df_Tweaks_Model_Settings_Poll */
	public function poll() {return Df_Tweaks_Model_Settings_Poll::s();}
	/** @return Df_Tweaks_Model_Settings_Remove */
	public function recentlyComparedProducts() {
		/** @var Df_Tweaks_Model_Settings_Remove $result */
		static $result;
		if (!isset($result)) {
			$result = Df_Tweaks_Model_Settings_Remove::i('recently_compared_products');
		}
		return $result;
	}
	/** @return Df_Tweaks_Model_Settings_RecentlyViewedProducts */
	public function recentlyViewedProducts() {return Df_Tweaks_Model_Settings_RecentlyViewedProducts::s();}
	/** @return Df_Tweaks_Model_Settings_Tags */
	public function tags() {return Df_Tweaks_Model_Settings_Tags::s();}
	/** @return Df_Tweaks_Model_Settings_Theme */
	public function theme() {return Df_Tweaks_Model_Settings_Theme::s();}
	/** @return Df_Tweaks_Model_Settings_Wishlist */
	public function wishlist() {
		return Df_Tweaks_Model_Settings_Wishlist::s();
	}
	const _CLASS = __CLASS__;
	/** @return Df_Tweaks_Model_Settings */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}