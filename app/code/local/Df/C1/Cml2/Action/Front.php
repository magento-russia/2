<?php
class Df_C1_Cml2_Action_Front extends Df_C1_Cml2_Action {
	/**
	 * @override
	 * @see Df_Core_Model_Action::_process()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function _process() {
		if ($this->rmRequest()->isCheckAuth()) {
			/** @uses Df_C1_Cml2_Action_Login */
			$this->delegate('Login');
		}
		else {
			$this->checkLoggedIn();
			switch ($this->rmRequest()->getType()) {
				case Df_C1_Cml2_InputRequest_Generic::TYPE__GET_CATALOG:
					$this->action_catalogExport();
					break;
				case Df_C1_Cml2_InputRequest_Generic::TYPE__CATALOG:
					$this->action_catalog();
					break;
				case Df_C1_Cml2_InputRequest_Generic::TYPE__ORDERS:
					$this->action_orders();
					break;
				case Df_C1_Cml2_InputRequest_Generic::TYPE__REFERENCE:
					$this->action_reference();
					break;
			}
		}
	}

	/** @return void */
	private function action_catalog() {
		switch($this->rmRequest()->getMode()) {
			/**
			 * Этот режим имеется в версии 4.0.2.3 модуля 1С-Битрикс для обмена с сайтом:
				Процедура ДобавитьПараметрыПротоколаОбменаВСтруктуру(СтруктураПараметров)
					СтруктураПараметров.Вставить("ПараметрЗапросаHTTP_Инициализация"			, "&mode=init");
					СтруктураПараметров.Вставить("ПараметрЗапросаHTTP_ПередачаФайла"			, "&mode=file&filename=");
					СтруктураПараметров.Вставить("ПараметрЗапросаHTTP_ИмпортФайлаСервером"		, "&mode=import&filename=");
					СтруктураПараметров.Вставить("ПараметрЗапросаHTTP_ПолучитьДанные"			, "&mode=query");
					СтруктураПараметров.Вставить("ПараметрЗапросаHTTP_УспешноеЗавершениеИмпорта", "&mode=success");
					СтруктураПараметров.Вставить("ПараметрЗапросаHTTP_ДеактивацияДанныхПоДате"	, "&mode=deactivate");
					(...)
				КонецПроцедуры
			 * http://dev.1c-bitrix.ru/community/blogs/product_features/exchange-module-with-1cbitrix-40.php
			 * Что он означает — пока неясно: надо смотреть исходники последних версий 1С-Битрикс.
			 * В журнале 1С этот режим прокомментирован так:
			 * «Деактивация элементов, не попавшие в полную пакетную выгрузку.»
			 */
			case Df_C1_Cml2_InputRequest_Generic::MODE__DEACTIVATE:
				/** @uses Df_C1_Cml2_Action_Catalog_Deactivate */
				$this->delegate('Catalog_Deactivate');
				break;
			case Df_C1_Cml2_InputRequest_Generic::MODE__FILE:
				$this->action_upload();
				break;
			case Df_C1_Cml2_InputRequest_Generic::MODE__IMPORT:
				/** @uses Df_C1_Cml2_Action_Catalog_Import */
				$this->delegate('Catalog_Import');
				break;
			case Df_C1_Cml2_InputRequest_Generic::MODE__INIT:
				$this->action_init();
				break;
		}
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	private function action_catalogExport() {
		switch($this->rmRequest()->getMode()) {
			case Df_C1_Cml2_InputRequest_Generic::MODE__INIT:
				$this->action_init();
				$this->flag_catalogHasJustBeenExported(false);
				break;
			case Df_C1_Cml2_InputRequest_Generic::MODE__QUERY:
				/** @var bool $process */
				$process = !$this->flag_catalogHasJustBeenExported();
				try {
					/**
					 * @uses Df_C1_Cml2_Action_Catalog_Export_Finish
					 * @uses Df_C1_Cml2_Action_Catalog_Export_Process
					 */
					$this->delegate('Catalog_Export_' . $process ? 'Process' : 'Finish');
					$this->flag_catalogHasJustBeenExported($process);
				}
				catch (Exception $e) {
					$this->flag_catalogHasJustBeenExported($process);
					throw $e;
				}
				break;
		}
	}

	/**
	 * @uses Df_C1_Cml2_Action_Init
	 * @return void
	 */
	private function action_init() {$this->delegate('Init');}

	/** @return void */
	private function action_orders() {
		switch($this->rmRequest()->getMode()) {
			case Df_C1_Cml2_InputRequest_Generic::MODE__FILE:
				/** @uses Df_C1_Cml2_Action_Orders_Import */
				$this->delegate('Orders_Import');
				break;
			case Df_C1_Cml2_InputRequest_Generic::MODE__INIT:
				$this->action_init();
				break;
			case Df_C1_Cml2_InputRequest_Generic::MODE__QUERY:
				/** @uses Df_C1_Cml2_Action_Orders_Export */
				$this->delegate('Orders_Export');
				break;
			case Df_C1_Cml2_InputRequest_Generic::MODE__SUCCESS:
				$this->setResponseSuccess();
				break;
		}
	}

	/** @return void */
	private function action_reference() {
		switch($this->rmRequest()->getMode()) {
			case Df_C1_Cml2_InputRequest_Generic::MODE__FILE:
				$this->action_upload();
				break;
			case Df_C1_Cml2_InputRequest_Generic::MODE__IMPORT:
				/** @uses Df_C1_Cml2_Action_Reference_Import */
				$this->delegate('Reference_Import');
				break;
			case Df_C1_Cml2_InputRequest_Generic::MODE__INIT:
				$this->action_init();
				break;
		}
	}

	/**
	 * @uses Df_C1_Cml2_Action_GenericImport_Upload
	 * @return void
	 */
	private function action_upload() {$this->delegate('GenericImport_Upload');}

	/** @return void */
	private function checkLoggedIn() {
		/** @var string|null $sessionId */
		$sessionId = Df_C1_Cml2_Cookie::s()->getSessionId();
		if (!df_check_string_not_empty($sessionId)) {
			df_error(
				'1С должна была указать в запросе идентификатор сессии, однако не указала.'
				. "\nОбработка запроса невозможна."
				. "\nОбратитесь к программисту."
			);
		}
		$this->sessionMagentoAPI()->setSessionId($sessionId);
		if (!$this->sessionMagentoAPI()->isLoggedIn($sessionId)) {
			df_error(
				'Доступ к данной операции запрещён,'
				. ' потому что система не смогла распознать администратора (неверная сессия)'
			);
		}
	}

	/**
	 * @param bool|null $value [optional]
	 * @return bool|null
	 */
	private function flag_catalogHasJustBeenExported($value = null) {
		$this->session()->begin();
		/** @var bool|null $result */
		$result = $this->session()->flag_catalogHasJustBeenExported($value);
		$this->session()->end();
		return $result;
	}
}