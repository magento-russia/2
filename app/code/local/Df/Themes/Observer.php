<?php
class Df_Themes_Observer {
	/**
	 * Это событие случается при использовании модуля Excellence Ajax
	 * при попытке добавления товара с опциями в корзину на странице списка товаров.
	 * Шаблон ajax/catalog/product/options.phtml модуля Excellence Ajax
	 * в оформительской теме TemplateMonster #43373
	 * (и, видимо, и в других оформительских темах)
	 * содержит фразу «Please specify the product's required option(s).»
	 * без вызова метода для перевода этой фразы.
	 * Таким образом, приходится переводит эту фразу перехватом события
	 * «controller_action_postdispatch_ajax_index_options».
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @return void
	 */
	public function controller_action_postdispatch_ajax_index_options() {
		try {
			if (@class_exists('Excellence_Ajax_Helper_Data')) {
				/** @var string $phrase */
				$phrase = "Please specify the product's required option(s).";
				/** @var Mage_Core_Controller_Response_Http $response */
				$response = rm_controller()->getResponse();
				$response->setBody(str_replace(
					$phrase, Mage::helper('ajax')->__($phrase), $response->getBody()
				));
			}
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * 2015-08-24
	 * Ves Super Store (ThemeForest 8002349)
	 * http://themeforest.net/item/ves-super-store-responsive-magento-theme-/8002349?ref=dfediuk
	 * http://demoleotheme.com/superstore/
	 * http://magento-forum.ru/forum/370/
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function controller_action_postdispatch_vesautosearch_index_ajaxgetproduct(Varien_Event_Observer $observer) {
		try {
			if (@class_exists('Ves_Autosearch_Helper_Data')) {
				/** @var string $phrase */
				$phrase = 'No products exists';
				/** @var Mage_Core_Controller_Response_Http $response */
				$response = rm_controller()->getResponse();
				$response->setBody(str_replace(
					$phrase, Mage::helper('ves_autosearch')->__($phrase), $response->getBody()
				));
			}
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}
}