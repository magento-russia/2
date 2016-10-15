<?php
class Df_Customer_Observer {
	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @return void
	 */
	public function controller_action_predispatch_customer_account_loginPost() {
		try {
			/**
			 * Magento CE, начиная с версии 1.8.0.0, проверяет form_key для формы авторизации,
			 * а в более старых версиях form_key у форм авторизации отсутствует и не проверяется.
			 */
			/** @var bool $ge_1_8_0_0 */
			static $ge_1_8_0_0;
			if (is_null($ge_1_8_0_0)) {
				$ge_1_8_0_0 = df_magento_version('1.8.0.0', '>=');
			}
			if ($ge_1_8_0_0 && !df_request('form_key')) {
				df_session_customer()->addError(
					'К сожалению, система авторизации дала сбой.'
					.'<br/>Пожалуйста, попробуйте войти повторно, используя форму ниже на этой странице.'
					.'<br/>Если сбой повторится, то оформите Ваш заказ по телефону.'
					.'<br/>Администрация магазина уже оповещена системой о сбое'
					.' и делает всё возможное для его скорейшего устранения.'
				);
				df_notify_admin(
					// 2015-12-04
					// Убрал слова «на странице оформления заказа»,
					// потому что заметил, что авторизация может давать сбой
					// не только на странице оформления заказа,
					// а везде, где есть форма авторизации.
					'Форма авторизации не передала на сервер параметр «form_key».'
					."\r\nПричиной дефекта формы может быть"
					.' несовместимость используемой магазином оформительский темы,'
					.' а также нестандартные правки в ней, сторонние модули,'
					.' либо некий сбой в Российской сборке Magento.'
					."\r\nРазработчик уже оповещён о проблеме."
					."\r\nПодробное объяснение проблемы и способа её устрания:"
					. " http://magento-forum.ru/topic/5093/."
				);
				df_notify_me('Форма авторизации не передала на сервер параметр «form_key».');
			}
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @see Mage_Core_Model_Resource_Db_Collection_Abstract::_afterLoad()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function core_collection_abstract_load_after(Varien_Event_Observer $o) {
		try {
			/**
			 * Для ускорения работы системы проверяем класс коллекции прямо здесь,
			 * а не в обработчике события.
			 * Это позволяет нам не создавать обработчики событий для каждой коллекции.
			 */
			if (
					df_h()->customer()->check()->formAttributeCollection($o['collection'])
				&&
					df_checkout_ergonomic()
			) {
				df_handle_event(
					Df_Customer_Model_Handler_FormAttributeCollection_AdjustApplicability::class
					,Df_Core_Model_Event_Core_Collection_Abstract_LoadAfter::class
					,$o
				);
			}
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}
}