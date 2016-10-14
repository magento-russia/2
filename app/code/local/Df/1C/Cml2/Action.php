<?php
/** @method Df_1C_Cml2_InputRequest_Generic getRmRequest() */
abstract class Df_1C_Cml2_Action extends Df_Core_Model_Action {
	/** @return Df_1C_Cml2_State */
	protected function getState() {return Df_1C_Cml2_State::s();}

	/**
	 * @override
	 * @see Df_Core_Model_Action::getRmRequestClass()
	 * @used-by Df_Core_Model_Action::getRmRequest()
	 * @uses Df_1C_Cml2_InputRequest_Generic
	 * @return string
	 */
	protected function getRmRequestClass() {return 'Df_1C_Cml2_InputRequest_Generic';}

	/**
	 * @override
	 * @see Df_Core_Model_Action::isModuleEnabledByAdmin()
	 * @used-by Df_Core_Model_Action::checkAccessRights()
	 * @return bool
	 */
	protected function isModuleEnabledByAdmin() {return rm_1c_cfg()->general()->isEnabled();}

	/**
	 * @override
	 * @see Df_Core_Model_Action::processException()
	 * @used-by Df_Core_Model_Action::process()
	 * @param Exception $e
	 * @return void
	 */
	protected function processException(Exception $e) {
		/** @var string $diagnosticMessage */
		$diagnosticMessage = rm_ets($e);
		/** @var string|bool $output */
		// Используем @, чтобы избежать сбоя «Failed to delete buffer zlib output compression».
		// Такой сбой у меня возник на сервере moysklad.magento-demo.ru.
		$output = @ob_get_clean();
		if ($output) {
			Mage::log('output buffer: ' . $output);
			$diagnosticMessage = $output;
		}
		df_handle_entry_point_exception($e, false);
		$this->setResponseLines('failure', $diagnosticMessage);
	}

	/**
	 * @override
	 * @see Df_Core_Model_Action::processPrepare()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function processPrepare() {
		parent::processPrepare();
		df_h()->dataflow()->registry()->products()->addValidator(Df_1C_Validate_Product::s());
	}

	/** @return Df_1C_Cml2_Session_ByIp */
	protected function session() {return Df_1C_Cml2_Session_ByIp::s();}

	/**
	 * @used-by Df_1C_Cml2_Action_Front::checkLoggedIn()
	 * @used-by Df_1C_Cml2_Action_Login::_process()
	 * @return Df_1C_Cml2_Session_ByCookie_MagentoAPI
	 */
	protected function sessionMagentoAPI() {return Df_1C_Cml2_Session_ByCookie_MagentoAPI::s();}

	/**
	 * @used-by processException()
	 * @used-by setResponseSuccess()
	 * @used-by Df_1C_Cml2_Action_Init::_process()
	 * @used-by Df_1C_Cml2_Action_Login::_process()
	 * @used-by Df_1C_Cml2_Action_Catalog_Export_Finish::_process()
	 * @param string|string[] $lines
	 * @return void
	 */
	protected function setResponseLines($lines) {
		rm_response_content_type($this->getResponse(), 'text/plain; charset=windows-1251');
		$lines = is_array($lines) ? $this->flatResponseLines($lines) : func_get_args();
		$this->getResponse()->setBody(rm_1251_to(df_concat_n($lines)));
	}

	/**
	 * @used-by Df_1C_Cml2_Action_Catalog_Deactivate::_process()
	 * @used-by Df_1C_Cml2_Action_Catalog_Import::_process()
	 * @used-by Df_1C_Cml2_Action_Front::action_ordersExportSuccess()
	 * @used-by Df_1C_Cml2_Action_GenericImport_Upload::_process()
	 * @used-by Df_1C_Cml2_Action_Orders_Import::_process()
	 * @used-by Df_1C_Cml2_Action_Reference_Import::_process()
	 * @return void
	 */
	protected function setResponseSuccess() {$this->setResponseLines('success', '');}

	/**
	 * @override
	 * @see Df_Core_Model_Action::store()
	 * @used-by Df_Core_Model_Action::checkAccessRights()
	 * @used-by Df_Core_Model_Action::getStoreConfig()
	 * @return Df_Core_Model_StoreM
	 */
	protected function store() {return rm_state()->getStoreProcessed();}

	/**
	 * 2015-03-13
	 * Поддержка синтаксиса setResponseLines(array('paramName' => 'paramValue'))
	 * @see Df_1C_Cml2_Action_Init::_process()
	 * @see Df_1C_Cml2_Action_Catalog_Export_Finish::_process()
	 * @used-by setResponseLines()
	 * @param array(int|string => string) $lines
	 * @return string[]
	 */
	private function flatResponseLines(array $lines) {
		/** @var string[] $result */
		$result = array();
		foreach ($lines as $key => $value) {
			/** @var string|int $key */
			/** @var string $value */
			$result[]= is_int($key) ?  $value : "{$key}={$value}";
		}
		return $result;
	}
}