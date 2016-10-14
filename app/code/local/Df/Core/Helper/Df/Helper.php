<?php
class Df_Core_Helper_Df_Helper extends Mage_Core_Helper_Abstract {
	/** @return Df_AccessControl_Helper_Data */
	public function accessControl() {return Df_AccessControl_Helper_Data::s();}
	/** @return Df_Admin_Helper_Data */
	public function admin() {return Df_Admin_Helper_Data::s();}
	/** @return Df_AdminNotification_Helper_Data */
	public function adminNotification() {return Df_AdminNotification_Helper_Data::s();}
	/** @return Df_Banner_Helper_Data */
	public function banner() {return Df_Banner_Helper_Data::s();}
	/** @return Df_Bundle_Helper_Data */
	public function bundle() {return Df_Bundle_Helper_Data::s();}
	/** @return Df_Catalog_Helper_Data */
	public function catalog() {return Df_Catalog_Helper_Data::s();}
	/** @return Df_CatalogInventory_Helper_Data */
	public function catalogInventory() {return Df_CatalogInventory_Helper_Data::s();}
	/** @return Df_CatalogSearch_Helper_Data */
	public function catalogSearch() {return Df_CatalogSearch_Helper_Data::s();}
	/** @return Df_Checkout_Helper_Data */
	public function checkout() {return Df_Checkout_Helper_Data::s();}
	/** @return Df_Chronopay_Helper_Data */
	public function chronopay() {return Df_Chronopay_Helper_Data::s();}
	/** @return Df_Cms_Helper_Data */
	public function cms() {return Df_Cms_Helper_Data::s();}
	/** @return Df_Compiler_Helper_Data */
	public function compiler() {return Df_Compiler_Helper_Data::s();}
	/** @return Df_Connect_Helper_Data */
	public function connect() {return Df_Connect_Helper_Data::s();}
	/** @return Df_Customer_Helper_Data */
	public function customer() {return Df_Customer_Helper_Data::s();}
	/** @return Df_Dataflow_Helper_Data */
	public function dataflow() {return Df_Dataflow_Helper_Data::s();}
	/** @return Df_Directory_Helper_Data */
	public function directory() {return Df_Directory_Helper_Data::s();}
	/** @return Df_Downloadable_Helper_Data */
	public function downloadable() {return Df_Downloadable_Helper_Data::s();}
	/** @return Df_Eav_Helper_Data */
	public function eav() {return Df_Eav_Helper_Data::s();}
	/** @return Df_Ems_Helper_Data */
	public function ems() {return Df_Ems_Helper_Data::s();}
	/** @return Df_Index_Helper_Data */
	public function index() {return Df_Index_Helper_Data::s();}
	/** @return Df_Invitation_Helper_Data */
	public function invitation() {return Df_Invitation_Helper_Data::s();}
	/** @return Df_Localization_Helper_Data */
	public function localization() {return Df_Localization_Helper_Data::s();}
	/** @return Df_Logging_Helper_Data */
	public function logging() {return Df_Logging_Helper_Data::s();}
	/** @return Df_OnPay_Helper_Data */
	public function onPay() {return Df_OnPay_Helper_Data::s();}
	/** @return Df_Page_Helper_Data */
	public function page() {return Df_Page_Helper_Data::s();}
	/** @return Df_PageCache_Helper_Data */
	public function pageCache() {return Df_PageCache_Helper_Data::s();}
	/** @return Df_Parser_Helper_Data */
	public function parser() {return Df_Parser_Helper_Data::s();}
	/** @return Df_Payment_Helper_Data */
	public function payment() {return Df_Payment_Helper_Data::s();}
	/** @return Df_PayOnline_Helper_Data */
	public function payOnline() {return Df_PayOnline_Helper_Data::s();}
	/** @return Df_Pd4_Helper_Data */
	public function pd4() {return Df_Pd4_Helper_Data::s();}
	/** @return Df_PromoGift_Helper_Data */
	public function promoGift() {return Df_PromoGift_Helper_Data::s();}
	/** @return Df_Promotion_Helper_Data */
	public function promotion() {return Df_Promotion_Helper_Data::s();}
	/** @return Df_Qiwi_Helper_Data */
	public function qiwi() {return Df_Qiwi_Helper_Data::s();}
	/** @return Df_Rating_Helper_Data */
	public function rating() {return Df_Rating_Helper_Data::s();}
	/** @return Df_Reports_Helper_Data */
	public function reports() {return Df_Reports_Helper_Data::s();}
	/** @return Df_Reward_Helper_Data */
	public function reward() {return Df_Reward_Helper_Data::s();}
	/** @return Df_Sales_Helper_Data */
	public function sales() {return Df_Sales_Helper_Data::s();}
	/** @return Df_SalesRule_Helper_Data */
	public function salesRule() {return Df_SalesRule_Helper_Data::s();}
	/** @return Df_Sitemap_Helper_Data */
	public function sitemap() {return Df_Sitemap_Helper_Data::s();}
	/** @return Df_Sms_Helper_Data */
	public function sms() {return Df_Sms_Helper_Data::s();}
	/** @return Df_Tweaks_Helper_Data */
	public function tweaks() {return Df_Tweaks_Helper_Data::s();}
	/** @return Df_Vk_Helper_Data */
	public function vk() {return Df_Vk_Helper_Data::s();}
	/** @return Df_WalletOne_Helper_Data */
	public function walletOne() {return Df_WalletOne_Helper_Data::s();}
	/** @return Df_Wishlist_Helper_Data */
	public function wishlist() {return Df_Wishlist_Helper_Data::s();}
	/** @return Df_YandexMarket_Helper_Data */
	public function yandexMarket() {return Df_YandexMarket_Helper_Data::s();}
	/** @return Df_Zf_Helper_Data */
	public function zf() {return Df_Zf_Helper_Data::s();}
	/**
	 * @buyer {buyer}
	 * @return Df_Core_Helper_Df_Helper
	 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}