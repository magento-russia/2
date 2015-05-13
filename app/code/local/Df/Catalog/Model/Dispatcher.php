<?php
class Df_Catalog_Model_Dispatcher {
	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function rm__config_after_save__catalog__frontend__flat_catalog_category(
		Varien_Event_Observer $observer
	) {
		try {
			/**
			 * Если администратор изменил значение наблюдаемой опции,
			 * то предшествующую команду администратора о скрытии предупреждения
			 * о проблемном значении этой опции считаем недействительной.
			 * Точно так же поступает и ядро Magento в сценарии предупреждений о настройках налогов:
			 * @see Mage_Tax_Model_Config_Notification::_resetNotificationFlag()
			 */
			$this->updateSkipStatus(Df_Catalog_Model_Admin_Notifier_Flat_Category::s(), $observer);
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function rm__config_after_save__catalog__frontend__flat_catalog_product(
		Varien_Event_Observer $observer
	) {
		try {
			/**
			 * Если администратор изменил значение наблюдаемой опции,
			 * то предшествующую команду администратора о скрытии предупреждения
			 * о проблемном значении этой опции считаем недействительной.
			 * Точно так же поступает и ядро Magento в сценарии предупреждений о настройках налогов:
			 * @see Mage_Tax_Model_Config_Notification::_resetNotificationFlag()
			 */
			$this->updateSkipStatus(Df_Catalog_Model_Admin_Notifier_Flat_Product::s(), $observer);
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function rm__magento_ce_has_just_been_installed(Varien_Event_Observer $observer) {
		try {
			/**
			 * Если установка Российской сборки Magento
			 * производится одновременно с установкой Magento CE,
			 * то Df_Catalog_Model_Setup_2_23_5 почему-то не в состоянии переименовать «Default Category».
			 * Для устранения этой проблемы мы при одновременной установке
			 * Российской сборки Magento и Magento CE
			 * повторно вручную запускаем Df_Catalog_Model_Setup_2_23_5::s()->process()
			 * сразу после завершения установки Magento CE
			 * (на событие «controller_action_postdispatch_install_wizard_end»).
			 */
			Df_Catalog_Model_Setup_2_23_5::s()->process();
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @param Df_Admin_Model_Notifier_Settings $notifier
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	private function updateSkipStatus(
		Df_Admin_Model_Notifier_Settings $notifier, Varien_Event_Observer $observer
	) {
		/** @var Mage_Core_Model_Config_Data $config */
		$config = $observer->getData('object');
		df_assert($config instanceof Mage_Core_Model_Config_Data);
		if ($config->isValueChanged()) {
			$notifier->resetSkipStatus();
		}
	}
}