<?php
abstract class Df_Core_Model_Action extends Df_Core_Model {
	/**
	 * @used-by process()
	 * @return void
	 */
	protected function _process() {$this->getResponse()->setBody($this->getResponseBody());}

	/**
	 * @used-by processPrepare()
	 * @return void
	 */
	protected function checkAccessRights() {
		if (!$this->isModuleEnabledByAdmin()) {
			df_error($this->getErrorMessage_moduleDisabledByAdmin());
		}
	}

	/**
	 * 2015-03-13
	 * Переадресовывает обработку запроса другому (дочернему) объекту.
	 * Это позволяет разбивать обработку сложных запросов на несколько классов-обработчиков.
	 * Широко используется модулем «1C:Управление торговлей».
	 * В качестве $class можно передавать как полный класс дочернего обработчика,
	 * так и суффикс класса.
	 * Например, если в качестве $class передан суффикс «Catalog_Deactivate»,
	 * а класс $this —  «Df_1C_Cml2_Action_Front»,
	 * то класс дочернего обработчика будет «Df_1C_Cml2_Action_Catalog_Deactivate»
	 * @param string $class
	 * @return void
	 */
	protected function delegate($class) {
		if (!df_starts_with($class, rm_module_name($this))) {
			/**
			 * 2015-08-04
			 * array('Df', '1C', 'Cml2', 'Action')
			 * @var string[] $head
			 */
			$head = df_head(rm_explode_class($this));
			$head[]= $class;
			$class = rm_concat_class($head);
		}
		self::pc($class, $this->getController());
	}

	/**
	 * @used-by getResponseBody()
	 * @return string
	 */
	protected function generateResponseBody() {return '';}

	/**
	 * @used-by getResponseBody()
	 * @return string
	 */
	protected function generateResponseBodyFake() {return '';}

	/**
	 * @used-by getResponseLogFileExtension()
	 * @used-by processPrepare()
	 * @return string
	 */
	protected function getContentType() {return 'text/plain; charset=UTF-8';}

	/**
	 * @used-by getRequest()
	 * @used-by getResponse()
	 * @used-by getRmRequest()
	 * @used-by redirect()
	 * @return Mage_Core_Controller_Varien_Action
	 */
	protected function getController() {return $this->cfg(self::P__CONTROLLER);}

	/**
	 * @used-by checkAccessRights()
	 * @return string
	 */
	protected function getErrorMessage_moduleDisabledByAdmin() {
		return strtr(
			'Модуль «{название модуля}» отключен в административной части магазина.'
			, array('{название модуля}' => $this->moduleTitle())
		);
	}

	/**
	 * @used-by processPrepare()
	 * @return int
	 */
	protected function getMemoryLimit() {return -1;}

	/**
	 * @used-by processRedirect()
	 * @return string
	 */
	protected function getRedirectLocation() {return '';}

	/**
	 * @used-by processRedirect()
	 * @return array(string => string|array())
	 */
	protected function getRedirectParams() {return array();}

	/**
	 * @used-by getRmRequest()
	 * @used-by Df_Alfabank_Model_Action_CustomerReturn::getRequest()
	 * @return Mage_Core_Controller_Request_Http
	 */
	protected function getRequest() {return $this->getController()->getRequest();}

	/**
	 * @used-by _process()
	 * @used-by processPrepare()
	 * @used-by redirectRaw()
	 * @return Mage_Core_Controller_Response_Http
	 */
	protected function getResponse() {return $this->getController()->getResponse();}

	/**
	 * @used-by _process()
	 * @used-by Df_IPay_Model_Action_Abstract::processBeforeRedirect()
	 * @param bool $real [optional]
	 * @return string
	 */
	protected function getResponseBody($real = null) {
		if (is_null($real)) {
			$real = !$this->needGenerateFakeResponse();
		}
		if (!isset($this->{__METHOD__}[$real])) {
			$this->{__METHOD__}[$real] =
				$real ? $this->generateResponseBody() : $this->generateResponseBodyFake()
			;
			df_result_string($this->{__METHOD__}[$real]);
		}
		return $this->{__METHOD__}[$real];
	}

	/**
	 * «Df_1C_Cml2_Action_Catalog_Export_Process» => «cml2.action.catalog.export.process»
	 * @used-by getResponseLogFileName()
	 * @return string
	 */
	protected function getResponseLogActionName() {return rm_model_id($this, '.');}

	/**
	 * @used-by getResponseLogFileName()
	 * @return string
	 */
	protected function getResponseLogFileDir() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_cc_path(Mage::getBaseDir('var'), 'log');
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by getResponseLogFileName()
	 * @return string
	 */
	protected function getResponseLogFileExtension() {
		return df_contains($this->getContentType(), 'xml') ? 'xml' : 'txt';
	}

	/**
	 * @used-by logResponse()
	 * @return string
	 */
	protected function getResponseLogFileName() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $prefix */
			$prefix = implode('-', array_filter(array(
				rm_module_id($this, '.'), $this->getResponseLogActionName()
			)));
			/** @var string $template */
			$template = strtr('rm-{prefix}-{date}-{time}.{extension}', array(
				'{prefix}' => $prefix
				,'{extension}' => $this->getResponseLogFileExtension()
			));
			$this->{__METHOD__} = df_file_name($this->getResponseLogFileDir(), $template, '.');
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_1C_Cml2_Action_Front::_process()
	 * @used-by Df_YandexMarket_Model_Action_Category_Suggest::getQuery()
	 * @return Df_Core_Model_InputRequest
	 */
	protected function getRmRequest() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Core_Model_InputRequest::ic(
				$this->getRmRequestClass(), $this->getController()->getRequest()
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by getRmRequest()
	 * @uses Df_Core_Model_InputRequest
	 * @return string
	 */
	protected function getRmRequestClass() {return 'Df_Core_Model_InputRequest';}

	/**
	 * @used-by Df_1C_Cml2_Action::getLastProcessedTimeAsString()
	 * @param string $key
	 * @return mixed
	 */
	protected function getStoreConfig($key) {return $this->store()->getConfig($key);}

	/**
	 * @used-by processPrepare()
	 * @return int
	 */
	protected function getTimeLimit() {return 0;}

	/**
	 * @used-by checkAccessRights()
	 * @return bool
	 */
	protected function isModuleEnabledByAdmin() {return true;}

	/**
	 * @used-by processException()
	 * @return bool
	 */
	protected function needAddExceptionToSession() {return !$this->needRethrowException();}

	/**
	 * @used-by benchmarkLog()
	 * @used-by benchmarkStart()
	 * @return bool
	 */
	protected function needBenchmark() {return false;}

	/**
	 * @used-by getResponseBody()
	 * @return bool
	 */
	protected function needGenerateFakeResponse() {return false;}

	/**
	 * @used-by processFinish()
	 * @return bool
	 */
	protected function needLogResponse() {return false;}

	/**
	 * @used-by needAddExceptionToSession()
	 * @used-by processException()
	 * @return bool
	 */
	protected function needRethrowException() {return true;}

	/**
	 * @used-by process()
	 * @return void
	 */
	protected function processBeforeRedirect() {}

	/**
	 * @used-by process()
	 * @param Exception $e
	 * @return void
	 */
	protected function processException(Exception $e) {
		/**
		 * Используем @, чтобы избежать сбоя «Failed to delete buffer zlib output compression».
		 * Такой сбой у меня возник на сервере moysklad.magento-demo.ru
		 * в другой точке программы при аналогичном вызове @uses ob_get_clean().
		 */
		/** @var string|bool $output */
		$output = @ob_get_clean();
		if ($output) {
			Mage::log(df_sprintf("output buffer:\n«%s»", $output));
		}
		if ($this->needAddExceptionToSession()) {
			rm_exception_to_session($e);
		}
		df_handle_entry_point_exception($e, $this->needRethrowException());
	}

	/**
	 * @used-by process()
	 * @return void
	 */
	protected function processFinish() {
		$this->benchmarkLog();
		if ($this->needLogResponse()) {
			$this->logResponse();
		}
		/**
		 * Используем @, чтобы избежать сбоя «Failed to delete buffer zlib output compression».
		 * Такой сбой у меня возник на сервере moysklad.magento-demo.ru
		 * в другой точке программы при аналогичном вызове @uses ob_get_clean().
		 */
		/** @var string|bool $output */
		$output = @ob_get_clean();
		if ($output) {
			ob_start();
			df_error($output);
		}
	}

	/**
	 * @used-by process()
	 * @return void
	 */
	protected function processPrepare() {
		ob_start();
		/**
		 * Обратите внимание, что в программном коде, к сожалению, нельзя
		 * дополнительно отменить ограничение на max_input_time
		 * http://www.php.net/manual/en/info.configuration.php
		 */
		set_time_limit($this->getTimeLimit());
		ini_set('memory_limit', $this->getMemoryLimit());
		rm_response_content_type($this->getResponse(), $this->getContentType());
		$this->benchmarkStart();
		$this->checkAccessRights();
	}

	/**
	 * @used-by process()
	 * @return void
	 */
	protected function processRedirect() {
		if ($this->getRedirectLocation()) {
			self::$REDIRECT_LOCATION__REFERER === $this->getRedirectLocation()
			? $this->redirectRaw(rm_referer())
			: $this->redirect($this->getRedirectLocation(), $this->getRedirectParams());
		}
	}

	/**
	 * @used-by processRedirect()
	 * @used-by Df_Core_Model_Action_Admin_Entity_Save::processRedirect()
	 * @param string $path
	 * @param array(string => mixed) $arguments [optional]
	 * @return void
	 */
	protected function redirect($path, array $arguments = array()) {
		$this->getController()->setRedirectWithCookieCheck($path, $arguments);
	}

	/**
	 * @used-by processRedirect()
	 * @used-by Df_Payment_Model_Action_Confirm::redirectToSuccess()
	 * @param string $path
	 * @return void
	 */
	protected function redirectRaw($path) {$this->getResponse()->setRedirect($path);}

	/**
	 * @used-by checkAccessRights()
	 * @used-by getStoreConfig()
	 * @used-by Df_Admin_Model_Action_DeleteDemoStore::_process()
	 * @return Df_Core_Model_StoreM
	 */
	protected function store() {return rm_store();}

	/**
	 * @used-by Df_1C_Cml2_Action_Orders_Export::getOrders()
	 * @return int
	 */
	protected function storeId() {return $this->store()->getId();}

	/**
	 * @used-by processFinish()
	 * @return void
	 */
	private function benchmarkLog() {
		if ($this->needBenchmark()) {
			/** @var float $timeSpent */
			$timeSpent = microtime($get_as_float = true) - $this->_benchmarkTimeStart;
			Mage::log(df_sprintf('%s: %.3fs', get_class($this), $timeSpent));
		}
	}

	/**
	 * @used-by processPrepare()
	 * @return void
	 */
	private function benchmarkStart() {
		/** @var float $timeStart */
		if ($this->needBenchmark()) {
			$this->_benchmarkTimeStart = microtime($get_as_float = true);
		}
	}

	/**
	 * @used-by processFinish()
	 * @return void
	 */
	private function logResponse() {
		df_file_put_contents($this->getResponseLogFileName(), $this->getResponseBody($real = true));
	}

	/**
	 * @used-by pc()
	 * @return void
	 */
	private function process() {
		try {
			$this->processPrepare();
			$this->_process();
			$this->processFinish();
		}
		catch (Exception $e) {
			$this->processException($e);
		}
		$this->processBeforeRedirect();
		$this->processRedirect();
	}

	/**
	 * @used-by processRedirect()
	 * @used-by Df_Core_Model_Action_Admin_Entity_Save::processRedirect()
	 * @param string $path
	 * @param array(string => mixed) $arguments [optional]
	 * @return void
	 */
	protected function redirect($path, array $arguments = array()) {
		$this->getController()->setRedirectWithCookieCheck($path, $arguments);
	}

	/**
	 * @used-by processRedirect()
	 * @used-by Df_Payment_Model_Action_Confirm::redirectToSuccess()
	 * @param string $path
	 * @return void
	 */
	protected function redirectRaw($path) {$this->getResponse()->setRedirect($path);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__CONTROLLER, 'Mage_Core_Controller_Varien_Action');
	}
	const P__CONTROLLER = 'controller';
	/** @var float */
	private $_benchmarkTimeStart;

	/** @var string */
	protected static $REDIRECT_LOCATION__REFERER = 'referer';
	/**
	 * @used-by Df_1C_Cml2_Action_GenericExport::getContentType()
	 * @used-by Df_YandexMarket_Model_Action_Front::getContentType()
	 * @var string
	 */
	protected static $CONTENT_TYPE__XML__UTF_8 = 'application/xml; charset=utf-8';

	/**
	 * @used-by delegate()
	 * @used-by rm_action()
	 * @param string $class
	 * @param Mage_Core_Controller_Varien_Action $c
	 * @return void
	 */
	public static function pc($class, Mage_Core_Controller_Varien_Action $c) {
		rm_ic($class, __CLASS__, array(self::P__CONTROLLER => $c))->process();
	}
}