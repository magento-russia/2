<?php
class Df_Client_Model_Dispatcher {
	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function controller_action_postdispatch(Varien_Event_Observer $observer) {
		// Иначе случится сбой «dbModel read resource does not implement Zend_Db_Adapter_Abstract»
		if (Mage::isInstalled()) {
			try {
				if (!df()->remoteControl()->isItServer()) {
					$this->resendDelayedMessages();
					$this->notifyAboutInstallation();
				}
			}
			catch(Exception $e) {
				df_handle_entry_point_exception($e);
			}
		}
	}

	/** @return void */
	private function notifyAboutInstallation() {
		// Не будем оповещать сервер об установке Российской сборки на мой локальный компьютер,
		// чтобы не засорять на сервере журнал установок.
		if (
				!df_is_it_my_local_pc()
			&&
				/** Этот флаг устанавливается методом @see Df_Client_Model_Setup_1_0_0::process() */
				rm_session_core()->getData(Df_Client_Model_Setup_1_0_0::FLAG__JUST_INSTALLED)
		) {
			Df_Client_Model_Request::sendStatic(Df_Client_Model_Message_Request_Installed::i());
		}
	}

	/** @return void */
	private function resendDelayedMessages() {
		// Нужно, чтобы система проверяла наличие неотправленных сообщений
		// не при каждой загрузке страницы, а всего лишь раз в час!
		// Проверка наличия неотправленных сообщений при каждой загрузке страницы
		// слишком уж тормозит систему.
		/** @var Zend_Cache_Core $cache */
		$cache = Mage::app()->getCache();
		/** @var string $cacheKey */
		$cacheKey = 'rm_need_check_delayed_messages';
		if (!$cache->test($cacheKey)) {
			foreach (Df_Client_Model_Resource_DelayedMessage_Collection::s() as $delayedMessage) {
				/** @var Df_Client_Model_DelayedMessage $delayedMessage */
				/** @var Df_Core_Model_RemoteControl_Message_Response $response */
				$response = Df_Client_Model_Request::sendStatic($delayedMessage->getMessage());
				if (!$response->isOk()) {
					// в случае сбоя отправки одного из сообщений не пробуем отправить остальные,
					// потому что их отправка тоже, скорей всего, завершится сбоем,
					// и поэтому попытка изх отправки станет всего лишь тормозом работы системы
					break;
				}
			}
			$cache->save('true', $cacheKey, array(), 3600);
		}
	}
}