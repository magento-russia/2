<?php
class Df_PromoGift_Model_Settings extends Df_Core_Model_Settings {
	/** @return boolean */
	public function enableAddToCartButton() {return $this->getYesNo('enable_add_to_cart_button');}
	/** @return boolean */
	public function getAutoAddToCart() {return $this->getYesNo('auto_add_to_cart');}
	/** @return string */
	public function getChooserPositionOnProductViewPage() {
		return $this->v('chooser__show_on_product_view');
	}
	/** @return boolean */
	public function getEnabled() {return $this->getYesNo('enabled');}
	/** @return boolean */
	public function needShowChooserOnCartPage() {return $this->getYesNo('chooser__show_on_cart_page');}
	/** @return boolean */
	public function needShowChooserOnCmsPage() {return $this->getYesNo('chooser__show_on_cms_pages');}
	/** @return boolean */
	public function needShowChooserOnFrontPage() {return $this->getYesNo('chooser__show_on_front_page');}
	/** @return boolean */
	public function needShowChooserOnProductListPage() {
		return $this->getYesNo('chooser__show_on_product_list');
	}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_promotion/gifts/';}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}