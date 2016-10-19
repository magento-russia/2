<?php
/**
 * Раньше я использовал Mage::app()->getStore()->isAdmin(),
 * однако метод ядра @see Mage_Core_Model_Store::isAdmin()
 * проверяет, является ли магазин административным,
 * более наивным способом: сравнивая идентификатор магазина с нулем
 * (подразумевая, что 0 — идентификатор административного магазина).
 * Как оказалось, у некоторых клиентов идентификатор административного магазина
 * не равен нулю (видимо, что-то не то делали с базой данных).
 * Поэтому используем более надёжную проверку — кода магазина.
 * @param Df_Core_Model_StoreM|int|string|bool|null $store
 * @return bool
 *
 * 2015-02-04
 * Раньше реализация метода была такой:
		function df_is_admin($store = null) {
			static $cachedResult;
			$forCurrentStore = is_null($store);
			if ($forCurrentStore && isset($cachedResult)) {
				$result = $cachedResult;
			}
			else {
				$result = ('admin' === df_store($store)->getCode());
				if ($forCurrentStore) {
					$cachedResult = $result;
				}
			}
			return $result;
		}
 * Однако мы не вправе кэшировать результат работы функции:
 * ведь текущий магазин может меняться. Поэтому убрал кэширование.
 *
 */
function df_is_admin($store = null) {return 'admin' === Mage::app()->getStore($store)->getCode();}

/**
 * 2016-07-08
 * Возвращает двухбуквенный код языка в нижнем регистре, например: «ru», «en», «pl».
 * @return string
 */
function df_lang() {
	static $r; return $r ? $r : $r = df_first(explode('_', Mage::app()->getLocale()->getLocaleCode()));
}

/**
 * 2015-03-31
 * @used-by Df_Cms_Model_Handler_ContentsMenu_Insert::getContentsMenus()
 * @used-by Df_Core_Block_Abstract::getCacheKeyInfo()
 * @used-by Df_Core_Block_Template::getCacheKeyInfo()
 * @used-by Df_Page_Block_Html_Head::getCssJsHtml()
 * @used-by Df_Page_Helper_Head::needSkipAsStandardCss()
 * @used-by Df_Page_Block_Html_Head::needSkipCustom()
 * @used-by Df_Review_Observer::core_session_abstract_add_message()
 * @used-by Df_Localization_Realtime_Dictionary::handleForController()
 * @return string
 */
function df_action_name() {return !df_controller() ? '' : df_controller()->getFullActionName();}

/**
 * @used-by df_action_name()
 * @used-by Df_1C_Observer::df_catalog__attribute_set__group_added()
 * @used-by Df_Chronopay_Model_Gate_Buyer::getIpAddress()
 * @used-by Df_Core_Observer::piratesCheck()
 * @used-by df_redirect_to_checkout()
 * @used-by Df_Localization_Realtime_Dictionary::handleForController()
 * @used-by Df_Payment_Model_Request_Payment::getCustomerIpAddress()
 * @used-by Df_Themes_Observer::controller_action_postdispatch_ajax_index_options()
 * @used-by Df_Chronopay_Model_Gate_Buyer::getIpAddress()
 * @return Mage_Core_Controller_Varien_Action|null
 */
function df_controller() {return df_state()->getController();}

/** @return bool */
function df_installed() {
	/** @var bool $result */
	static $result;
	if (is_null($result)) {
		/** @var string $timezone */
		$timezone = date_default_timezone_get();
		$result = Mage::isInstalled();
		date_default_timezone_set($timezone);
	}
	return $result;
}

/**
 * 2015-12-09
 * https://mage2.pro/t/299
 * @return bool
 */
function df_is_dev() {return Mage::getIsDeveloperMode();};

/** @return bool */
function df_my_local() {return dfcf(function() {return df_bool(dfa($_SERVER, 'RM_DEVELOPER'));});}

/**
 * @param string $key
 * @param string $default [optional]
 * @return string
 */
function df_request($key, $default = null) {return Mage::app()->getRequest()->getParam($key, $default);}

/**
 * 2015-08-14
 * @return string
 */
function df_ruri() {static $r; return $r ? $r : $r = Mage::app()->getRequest()->getRequestUri();}

/**
 * 2015-08-14
 * @param string $needle
 * @return bool
 */
function df_ruri_contains($needle) {return df_contains(df_ruri(), $needle);}

/**
 * @used-by df_controller()
 * @return Df_Core_State
 */
function df_state() {static $r; return $r ? $r : $r = Df_Core_State::s();}