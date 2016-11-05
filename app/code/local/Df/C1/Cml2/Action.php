<?php
namespace Df\C1\Cml2;
/** @method \Df\C1\Cml2\InputRequest\Generic rmRequest() */
abstract class Action extends \Df_Core_Model_Action {
	/** @return \Df\C1\Cml2\State */
	protected function getState() {return \Df\C1\Cml2\State::s();}

	/**
	 * @override
	 * @see Df_Core_Model_Action::rmRequestClass()
	 * @used-by Df_Core_Model_Action::rmRequest()
	 * @uses \Df\C1\Cml2\InputRequest\Generic
	 * @return string
	 */
	protected function rmRequestClass() {return \Df\C1\Cml2\InputRequest\Generic::class;}

	/**
	 * @override
	 * @see Df_Core_Model_Action::isModuleEnabledByAdmin()
	 * @used-by Df_Core_Model_Action::checkAccessRights()
	 * @return bool
	 */
	protected function isModuleEnabledByAdmin() {return df_c1_cfg()->general()->isEnabled();}

	/**
	 * @override
	 * @see Df_Core_Model_Action::processException()
	 * @used-by Df_Core_Model_Action::process()
	 * @param \Exception $e
	 * @return void
	 */
	protected function processException(\Exception $e) {
		/** @var string $diagnosticMessage */
		$diagnosticMessage = df_ets($e);
		/** @var string|bool $output */
		// Используем @, чтобы избежать сбоя «Failed to delete buffer zlib output compression».
		// Такой сбой у меня возник на сервере moysklad.magento-demo.ru.
		$output = @ob_get_clean();
		if ($output) {
			\Mage::log('output buffer: ' . $output);
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
		df_h()->dataflow()->registry()->products()->addValidator(\Df\C1\Validate\Product::s());
	}

	/** @return \Df\C1\Cml2\Session\ByIp */
	protected function session() {return \Df\C1\Cml2\Session\ByIp::s();}

	/**
	 * @used-by \Df\C1\Cml2\Action\Front::checkLoggedIn()
	 * @used-by \Df\C1\Cml2\Action\Login::_process()
	 * @return \Df\C1\Cml2\Session\ByCookie\MagentoAPI
	 */
	protected function sessionMagentoAPI() {return \Df\C1\Cml2\Session\ByCookie\MagentoAPI::s();}

	/**
	 * @used-by processException()
	 * @used-by setResponseSuccess()
	 * @used-by \Df\C1\Cml2\Action\Init::_process()
	 * @used-by \Df\C1\Cml2\Action\Login::_process()
	 * @used-by \Df\C1\Cml2\Action\Catalog\Export\Finish::_process()
	 * @param string|string[] $lines
	 * @return void
	 */
	protected function setResponseLines($lines) {
		df_response_content_type($this->response(), 'text/plain; charset=windows-1251');
		$lines = is_array($lines) ? $this->flatResponseLines($lines) : func_get_args();
		$this->response()->setBody(df_1251_to(df_cc_n($lines)));
	}

	/**
	 * @used-by \Df\C1\Cml2\Action\Catalog\Deactivate::_process()
	 * @used-by \Df\C1\Cml2\Action\Catalog\Import::_process()
	 * @used-by \Df\C1\Cml2\Action\Front::action_ordersExportSuccess()
	 * @used-by \Df\C1\Cml2\Action\GenericImport\Upload::_process()
	 * @used-by \Df\C1\Cml2\Action\Orders\Import::_process()
	 * @used-by \Df\C1\Cml2\Action\Reference\Import::_process()
	 * @return void
	 */
	protected function setResponseSuccess() {$this->setResponseLines('success', '');}

	/**
	 * @override
	 * @see Df_Core_Model_Action::store()
	 * @used-by Df_Core_Model_Action::checkAccessRights()
	 * @used-by Df_Core_Model_Action::getStoreConfig()
	 * @return \Df_Core_Model_StoreM
	 */
	protected function store() {return df_state()->getStoreProcessed();}

	/**
	 * 2015-03-13
	 * Поддержка синтаксиса setResponseLines(array('paramName' => 'paramValue'))
	 * @see \Df\C1\Cml2\Action\Init::_process()
	 * @see \Df\C1\Cml2\Action\Catalog\Export\Finish::_process()
	 * @used-by setResponseLines()
	 * @param array(int|string => string) $lines
	 * @return string[]
	 */
	private function flatResponseLines(array $lines) {
		/** @var string[] $result */
		$result = [];
		foreach ($lines as $key => $value) {
			/** @var string|int $key */
			/** @var string $value */
			$result[]= is_int($key) ?  $value : "{$key}={$value}";
		}
		return $result;
	}
}