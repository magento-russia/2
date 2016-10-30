<?php
class Df_Catalog_Observer {
	/**
	 * При необходимости добавляет в макет блок
	 * «Иллюстрированное меню товарных разделов».
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @used-by Mage_Core_Block_Abstract::toHtml()
		Mage::dispatchEvent('core_block_abstract_to_html_after', array(
			'block' => $this, 'transport' => self::$_transportObject
	 	));
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function core_block_abstract_to_html_after(Varien_Event_Observer $o) {
		try {
			/** @var bool $needProcess */
			static $needProcess;
			if (is_null($needProcess)) {
				$needProcess =
					df_cfgr()->catalog()->navigation()->getEnabled()
					&& !df_is_admin()
					&& df_handle(Df_Core_Model_Layout_Handle::CATALOG_CATEGORY_VIEW)
				;
			}
			/**
			 * При обработке текущего блока мы создаём новые блоки,
			 * и нам надо избежать бесконечной рекурсии
			 * @var bool
			 */
			static $inProcessing = false;
			/** @var bool */
			static $inserted = false;
			if ($needProcess && !$inserted && !$inProcessing) {
				$inProcessing = true;
				// при загрузке главной страницы мы сюда попадаем 55 раз
				$inserted = Df_Catalog_Model_Category_Content_Inserter::insert($o);
				$inProcessing = false;
			}
		}
		catch (Exception $e) {
			$inProcessing = false;
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function df__config_after_save__catalog__frontend__flat_catalog_category(
		Varien_Event_Observer $o
	) {
		try {
			/**
			 * Если администратор изменил значение наблюдаемой опции,
			 * то предшествующую команду администратора о скрытии предупреждения
			 * о проблемном значении этой опции считаем недействительной.
			 * Точно так же поступает и ядро Magento в сценарии предупреждений о настройках налогов:
			 * @see Mage_Tax_Model_Config_Notification::_resetNotificationFlag()
			 */
			$this->updateSkipStatus(Df_Catalog_Model_Admin_Notifier_Flat_Category::s(), $o);
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function df__config_after_save__catalog__frontend__flat_catalog_product(
		Varien_Event_Observer $o
	) {
		try {
			/**
			 * Если администратор изменил значение наблюдаемой опции,
			 * то предшествующую команду администратора о скрытии предупреждения
			 * о проблемном значении этой опции считаем недействительной.
			 * Точно так же поступает и ядро Magento в сценарии предупреждений о настройках налогов:
			 * @see Mage_Tax_Model_Config_Notification::_resetNotificationFlag()
			 */
			$this->updateSkipStatus(Df_Catalog_Model_Admin_Notifier_Flat_Product::s(), $o);
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * Если установка Российской сборки Magento
	 * производится одновременно с установкой Magento CE,
	 * то Df_Catalog_Setup_2_23_5 почему-то не в состоянии переименовать «Default Category».
	 * Для устранения этой проблемы мы при одновременной установке
	 * Российской сборки Magento и Magento CE
	 * повторно вручную запускаем @uses Df_Catalog_Setup_2_23_5::p()
	 * сразу после завершения установки Magento CE
	 * (на событие «controller_action_postdispatch_install_wizard_end»).
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @return void
	 */
	public function df__magento_ce_has_just_been_installed() {
		try {
			Df_Catalog_Setup_2_23_5::p();
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @param Df_Admin_Model_Notifier_Settings $notifier
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	private function updateSkipStatus(
		Df_Admin_Model_Notifier_Settings $notifier, Varien_Event_Observer $o
	) {
		/** @var Mage_Core_Model_Config_Data $config */
		$config = $o['object'];
		if ($config->isValueChanged()) {
			$notifier->resetSkipStatus();
		}
	}
}