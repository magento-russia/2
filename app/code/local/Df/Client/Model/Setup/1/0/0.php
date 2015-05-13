<?php
class Df_Client_Model_Setup_1_0_0 extends Df_Core_Model_Setup {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		Df_Client_Model_Resource_DelayedMessage::s()->createTable($this->getSetup());
		/**
		 * Обратите внимание на архитектуру.
		 * Мы не оповещаем сервер Российской сборки Magento
		 * о факте установке Российской сборки Magento
		 * прямо сейчас, потому что иначе будет происходить зацикливание,
		 * ибо обращение к самому себе как к серверу запустит снова метод install_1_0_0,
		 * и так до бесконечности.
		 *
		 * Вместо этого мы устанавливаем в сессии флаг
		 * «Российская сборка Magento только что установлена»,
		 * и лишь затем, на событие controller_action_postdispatch, отсылаем серверу оповещение.
		 * Обрабатывает флаг метод @see Df_Client_Model_Dispatcher::notifyAboutInstallation()
		 */
		rm_session_core()->setData(self::FLAG__JUST_INSTALLED, true);
	}

	const FLAG__JUST_INSTALLED = 'df_client__just_installed';

	/** @return Df_Client_Model_Setup_1_0_0 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}