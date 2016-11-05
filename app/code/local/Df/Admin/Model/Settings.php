<?php
class Df_Admin_Model_Settings extends Df_Core_Model_Settings {
	/** @return Df_Admin_Model_Settings_Admin */
	public function admin() {return Df_Admin_Model_Settings_Admin::s();}
	/** @return Df_Admin_Model_Settings_Base */
	public function base() {return Df_Admin_Model_Settings_Base::s();}
	/** @return Df_Catalog_Model_Settings */
	public function catalog() {return Df_Catalog_Model_Settings::s();}
	/** @return Df_Checkout_Model_Settings */
	public function checkout() {return Df_Checkout_Model_Settings::s();}
	/** @return Df_Cms_Model_Settings */
	public function cms() {return Df_Cms_Model_Settings::s();}
	/** @return Df_Dataflow_Model_Settings */
	public function dataflow() {return Df_Dataflow_Model_Settings::s();}
	/** @return Df_Directory_Settings */
	public function directory() {return Df_Directory_Settings::s();}
	/** @return Df_Index_Model_Settings */
	public function index() {return Df_Index_Model_Settings::s();}
	/**
	 * @used-by Df_Page_Helper_Head::needSkipAsJQuery()
	 * @used-by Df_Page_JQueryInjecter::p()
	 * @return Df_Core_Model_Settings_Jquery
	 */
	public function jquery() {return Df_Core_Model_Settings_Jquery::s();}
	/** @return Df_Logging_Model_Settings */
	public function logging() {return Df_Logging_Model_Settings::s();}
	/** @return Df_Newsletter_Model_Settings */
	public function newsletter() {return Df_Newsletter_Model_Settings::s();}
	/** @return Df_Promotion_Model_Settings */
	public function promotion() {return Df_Promotion_Model_Settings::s();}
	/** @return Df_Reports_Model_Settings */
	public function reports() {return Df_Reports_Model_Settings::s();}
	/** @return Df_Sales_Model_Settings */
	public function sales() {return Df_Sales_Model_Settings::s();}
	/** @return Df_Seo_Model_Settings */
	public function seo() {return Df_Seo_Model_Settings::s();}
	/** @return \Df\Shipping\Settings */
	public function shipping() {return \Df\Shipping\Settings::s();}
	/** @return Df_Sms_Model_Settings */
	public function sms() {return Df_Sms_Model_Settings::s();}
	/** @return Df_Speed_Model_Settings */
	public function speed() {return Df_Speed_Model_Settings::s();}
	/** @return Df_Tweaks_Model_Settings */
	public function tweaks() {return Df_Tweaks_Model_Settings::s();}
	/** @return Df_Vk_Model_Settings */
	public function vk() {return Df_Vk_Model_Settings::s();}
	/** @return \Df\YandexMarket\Settings */
	public function yandexMarket() {return \Df\YandexMarket\Settings::s();}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}