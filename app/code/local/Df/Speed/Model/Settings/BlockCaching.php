<?php
class Df_Speed_Model_Settings_BlockCaching extends Df_Core_Model_Settings {
	/** @return boolean */
	public function catalogProductCompareSidebar() {
		return $this->getYesNo('catalog_product_compare_sidebar');
	}
	/** @return boolean */
	public function catalogProductList() {return $this->getYesNo('catalog_product_list');}
	/** @return boolean */
	public function checkoutCartSidebar() {return $this->getYesNo('checkout_cart_sidebar');}
	/** @return boolean */
	public function cmsPage() {return $this->getYesNo('cms_page');}
	/** @return boolean */
	public function googleAnalytics() {return $this->getYesNo('google_analytics');}
	/** @return boolean */
	public function pageHtmlBreadcrumbs() {return $this->getYesNo('page_html_breadcrumbs');}
	/** @return boolean */
	public function pageHtmlTopmenu() {return $this->getYesNo('page_html_topmenu');}
	/** @return boolean */
	public function pageSwitch() {return $this->getYesNo('page_switch');}
	/** @return boolean */
	public function pageTemplateLinks() {return $this->getYesNo('page_template_links');}
	/** @return boolean */
	public function wishlistCustomerSidebar() {return $this->getYesNo('wishlist_customer_sidebar');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_speed/block_caching/';}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}