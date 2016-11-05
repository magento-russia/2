<?php
class Df_Core_Helper_Mage extends Mage_Core_Helper_Abstract {
	/** @return Mage_Admin_Helper_Data */
	public function adminHelper() {return Mage::helper('admin');}
	/** @return Df_Core_Helper_Mage_Adminhtml */
	public function adminhtml() {return Df_Core_Helper_Mage_Adminhtml::s();}
	/** @return Mage_Adminhtml_Helper_Data */
	public function adminhtmlHelper() {return Mage::helper('adminhtml');}
	/** @return Mage_AdminNotification_Helper_Data */
	public function adminNotificationHelper() {return Mage::helper('adminnotification');}
	/** @return Df_Core_Helper_Mage_Api */
	public function api() {return Df_Core_Helper_Mage_Api::s();}
	/** @return Mage_Api_Helper_Data */
	public function apiHelper() {return Mage::helper('api');}
	/** @return Mage_Authorizenet_Helper_Data */
	public function authorizenetHelper() {return Mage::helper('authorizenet');}
	/** @return Mage_Backup_Helper_Data */
	public function backupHelper() {return Mage::helper('backup');}
	/** @return Mage_Bundle_Helper_Data */
	public function bundleHelper() {return Mage::helper('bundle');}
	/** @return Df_Core_Helper_Mage_Catalog */
	public function catalog() {return Df_Core_Helper_Mage_Catalog::s();}
	/** @return Mage_Catalog_Helper_Data */
	public function catalogHelper() {return Mage::helper('catalog');}
	/** @return Df_Core_Helper_Mage_CatalogInventory */
	public function catalogInventory() {return Df_Core_Helper_Mage_CatalogInventory::s();}
	/** @return Mage_CatalogInventory_Helper_Data */
	public function catalogInventoryHelper() {return Mage::helper('cataloginventory');}
	/** @return Mage_Catalog_Helper_Image */
	public function catalogImageHelper() {return Mage::helper('catalog/image');}
	/** @return Mage_CatalogRule_Helper_Data */
	public function catalogRuleHelper() {return Mage::helper('catalogrule');}
	/** @return Mage_CatalogSearch_Helper_Data */
	public function catalogSearchHelper() {return Mage::helper('catalogsearch');}
	/** @return Df_Core_Helper_Mage_Checkout */
	public function checkout() {return Df_Core_Helper_Mage_Checkout::s();}
	/** @return Mage_Checkout_Helper_Data */
	public function checkoutHelper() {return Mage::helper('checkout');}
	/** @return Mage_Cms_Helper_Data */
	public function cmsHelper() {return Mage::helper('cms');}
	/** @return Mage_Cms_Helper_Wysiwyg_Images */
	public function cmsWysiwygImagesHelper() {return Mage::helper('cms/wysiwyg_images');}
	/** @return Mage_Compiler_Helper_Data */
	public function compilerHelper() {return Mage::helper('compiler');}
	/** @return Mage_Connect_Helper_Data */
	public function connectHelper() {return Mage::helper('connect');}
	/** @return Mage_Contacts_Helper_Data */
	public function contactsHelper() {return Mage::helper('contacts');}
	/** @return Df_Core_Helper_Mage_Core */
	public function core() {return Df_Core_Helper_Mage_Core::s();}
	/** @return Mage_Core_Helper_Data */
	public function coreHelper() {return Mage::helper('core');}
	/** @return Mage_Cron_Helper_Data */
	public function cronHelper() {return Mage::helper('cron');}
	/** @return Df_Core_Helper_Mage_Dataflow */
	public function dataflow() {return Df_Core_Helper_Mage_Dataflow::s();}
	/** @return Mage_Dataflow_Helper_Data */
	public function dataflowHelper() {return Mage::helper('dataflow');}
	/** @return Mage_Directory_Helper_Data */
	public function directoryHelper() {return Mage::helper('directory');}
	/** @return Mage_Downloadable_Helper_Data */
	public function downloadableHelper() {return Mage::helper('downloadable');}
	/** @return Df_Core_Helper_Mage_Eav */
	public function eav() {return Df_Core_Helper_Mage_Eav::s();}
	/** @return Mage_Eav_Helper_Data */
	public function eavHelper() {return Mage::helper('eav');}
	/** @return Mage_GiftMessage_Helper_Data */
	public function giftMessageHelper() {return Mage::helper('giftmessage');}
	/** @return Mage_GoogleAnalytics_Helper_Data */
	public function googleAnalyticsHelper() {return Mage::helper('googleanalytics');}
	/** @return Mage_ImportExport_Helper_Data */
	public function importExportHelper() {return Mage::helper('importexport');}
	/** @return Df_Core_Helper_Mage_Index */
	public function index() {return Df_Core_Helper_Mage_Index::s();}
	/** @return Mage_Index_Helper_Data */
	public function indexHelper() {return Mage::helper('index');}
	/** @return Mage_Install_Helper_Data */
	public function installHelper() {return Mage::helper('install');}
	/** @return Mage_Log_Helper_Data */
	public function logHelper() {return Mage::helper('log');}
	/** @return Mage_Media_Helper_Data */
	public function mediaHelper() {return Mage::helper('media');}
	/** @return Mage_Newsletter_Helper_Data */
	public function newsletterHelper() {return Mage::helper('newsletter');}
	/** @return Mage_Page_Helper_Data */
	public function pageHelper() {return Mage::helper('page');}
	/** @return Mage_PageCache_Helper_Data */
	public function pageCacheHelper() {return Mage::helper('pagecache');}
	/** @return Mage_Payment_Helper_Data */
	public function paymentHelper() {return Mage::helper('payment');}
	/** @return Mage_Paypal_Helper_Data */
	public function paypalHelper() {return Mage::helper('paypal');}
	/** @return Mage_Persistent_Helper_Data */
	public function persistentHelper() {return Mage::helper('persistent');}
	/** @return Mage_Poll_Helper_Data */
	public function pollHelper() {return Mage::helper('poll');}
	/** @return Mage_ProductAlert_Helper_Data */
	public function productAlertHelper() {return Mage::helper('productalert');}
	/** @return Mage_Rating_Helper_Data */
	public function ratingHelper() {return Mage::helper('rating');}
	/** @return Mage_Reports_Helper_Data */
	public function reportsHelper() {return Mage::helper('reports');}
	/** @return Mage_Review_Helper_Data */
	public function reviewHelper() {return Mage::helper('review');}
	/** @return Mage_Rss_Helper_Data */
	public function rssHelper() {return Mage::helper('rss');}
	/** @return Mage_Rule_Helper_Data */
	public function ruleHelper() {return Mage::helper('rule');}
	/** @return Mage_Sales_Helper_Data */
	public function salesHelper() {return Mage::helper('sales');}
	/** @return Mage_SalesRule_Helper_Data */
	public function salesRuleHelper() {return Mage::helper('salesrule');}
	/** @return Mage_Sendfriend_Helper_Data */
	public function sendfriendHelper() {return Mage::helper('sendfriend');}
	/** @return Mage_Shipping_Helper_Data */
	public function shippingHelper() {return Mage::helper('shipping');}
	/** @return Mage_Shipping_Model_Shipping */
	public function shippingSingleton() {return Mage::getSingleton('shipping/shipping');}
	/** @return Mage_Sitemap_Helper_Data */
	public function sitemapHelper() {return Mage::helper('sitemap');}
	/** @return Mage_Tag_Helper_Data */
	public function tagHelper() {return Mage::helper('tag');}
	/** @return Mage_Tax_Helper_Data */
	public function taxHelper() {return Mage::helper('tax');}
	/** @return Mage_Weee_Helper_Data */
	public function weeeHelper() {return Mage::helper('weee');}
	/** @return Mage_Widget_Helper_Data */
	public function widgetHelper() {return Mage::helper('widget');}
	/** @return Mage_Wishlist_Helper_Data */
	public function wishlistHelper() {return Mage::helper('wishlist');}
	/** @return Mage_XmlConnect_Helper_Data */
	public function xmlConnectHelper() {return Mage::helper('xmlconnect');}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}