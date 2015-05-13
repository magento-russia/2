<?php
abstract class Df_Core_Model_Controller_Action extends Df_Core_Model_Abstract {
	/**
	 * @override
	 * @return Df_Core_Model_Controller_Action
	 */
	public function process() {
		try {
			/** @var string|bool $output */
			$output = null;
			ob_start();
			/**
			 * Обратите внимание, что в программном коде, к сожалению, нельзя
			 * дополнительно отменить ограничение на max_input_time
			 * @link http://www.php.net/manual/en/info.configuration.php
			 */
			set_time_limit($this->getTimeLimit());
			ini_set('memory_limit', $this->getMemoryLimit());
			rm_response_content_type($this->getResponse(), $this->getContentType());
			/** @var float $timeStart */
			$timeStart = microtime($get_as_float = true);
			$this->checkAccessRights();
			$this->getResponse()->setBody($this->generateResponseBody());
			if ($this->needLogResponse()) {
				$this->logResponse();
			}
			/** @var float $timeCurrent */
			$timeCurrent = microtime($get_as_float = true);
			if (df_is_it_my_local_pc() && $this->needBenchmark()) {
				Mage::log(rm_sprintf('%s: %.3fs', get_class($this), $timeCurrent - $timeStart));
			}
			/**
			 * Используем @, чтобы избежать сбоя «Failed to delete buffer zlib output compression».
			 * Такой сбой у меня возник на сервере moysklad.magento-demo.ru
			 * в другой точке программы при аналогичном вызове @see ob_get_clean.
			 */
			$output = @ob_get_clean();
			if ($output) {
				ob_start();
				df_error($output);
			}
		}
		catch(Exception $e) {
			Mage::log('exception');
			/**
			 * Используем @, чтобы избежать сбоя «Failed to delete buffer zlib output compression».
			 * Такой сбой у меня возник на сервере moysklad.magento-demo.ru
			 * в другой точке программы при аналогичном вызове @see ob_get_clean.
			 */
			$output = @ob_get_clean();
			if ($output) {
				Mage::log(rm_sprintf("output buffer:\r\n«%s»", $output));
			}
			df_handle_entry_point_exception($e, $rethrow = true);
		}
		return $this;
	}

	/** @return void */
	private function logResponse() {
		rm_file_put_contents(
			df_concat_path(Mage::getBaseDir('var'), 'log', $this->getResponseLogFileName())
			,$this->getResponse()->getBody()
		);
	}

	/**
	 * @param string $path
	 * @param array $arguments[optional]
	 * @return Df_Core_Model_Controller_Action
	 */
	public function redirect($path, $arguments = array()) {
		$this->getController()->setRedirectWithCookieCheck($path, $arguments);
		return $this;
	}

	/** @return Df_Core_Model_Controller_Action */
	protected function checkAccessRights() {return $this;}

	/** @return string */
	protected function generateResponseBody() {return '';}

	/** @return string */
	protected function getContentType() {return Df_Core_Const::CONTENT_TYPE__TEXT__UTF_8;}

	/** @return Mage_Core_Controller_Varien_Action */
	protected function getController() {return $this->cfg(self::P__CONTROLLER);}

	/** @return int */
	protected function getMemoryLimit() {return -1;}

	/** @return Mage_Core_Controller_Request_Http */
	protected function getRequest() {return $this->getController()->getRequest();}

	/** @return Mage_Core_Controller_Response_Http */
	protected function getResponse() {return $this->getController()->getResponse();}

	/** @return string */
	protected function getResponseLogFileName() {return rm_sprintf('%s.txt', get_class($this));}

	/** @return Df_Core_Model_InputRequest */
	protected function getRmRequest() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $class */
			$class = $this->getRmRequestClass();
			df_assert_string_not_empty($class);
			$this->{__METHOD__} = new $class(array(
				Df_Core_Model_InputRequest::P__REQUEST => $this->getController()->getRequest()
			));
			df_assert($this->{__METHOD__} instanceof Df_Core_Model_InputRequest);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	protected function getRmRequestClass() {return Df_Core_Model_InputRequest::_CLASS;}

	/** @return int */
	protected function getTimeLimit() {return 0;}

	/** @return bool */
	protected function needBenchmark() {return false;}

	/** @return bool */
	protected function needLogResponse() {return false;}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__CONTROLLER, 'Mage_Core_Controller_Varien_Action');
	}
	const _CLASS = __CLASS__;
	const P__CONTROLLER = 'controller';
}