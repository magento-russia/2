<?php
class Df_Core_Model_Logger extends Df_Core_Model {
	/**
	 * @param string $message
	 * @return Df_Core_Model_Logger
	 */
	public function log($message) {
		/** @var mixed[] $arguments */
		$arguments = func_get_args();
		$this->getZendLog()->log(rm_sprintf($arguments), Zend_Log::DEBUG);
		return $this;
	}

	/**
	 * @param string $message
	 * @return Df_Core_Model_Logger
	 */
	public function logRaw($message) {
		/** @var mixed[] $arguments */
		$arguments = func_get_args();
		$this->getZendLog()->log(rm_sprintf($arguments), Zend_Log::DEBUG, array(self::FORMAT__RAW => true));
		return $this;
	}

	/** @return string */
	protected function getFileDir() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = $this->cfg(self::P__FILE_DIR);
			if (!$result) {
				$result = df_concat_path(Mage::getBaseDir('var'), 'log');
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	protected function getFileName() {return $this->cfg(self::P__FILE_NAME);}

	/** @return Zend_Log_Formatter_Interface */
	protected function getFormatter() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->cfg(self::P__FORMATTER);
			if (is_null($this->{__METHOD__})) {
				$this->{__METHOD__} = new Df_Zf_Log_Formatter_Simple();
			}
		}
		return $this->{__METHOD__};
	}
	
	/** @return string */
	private function getFilePath() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = df_concat_path($this->getFileDir(), $this->getFileName());
			if (!file_exists($result)) {
				file_put_contents($result, '');
				df()->file()->chmod($result, 0777);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
	
	/** @return Zend_Log_Writer_Abstract */
	private function getWriter() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->cfg(self::P__WRITER);
			if (is_null($this->{__METHOD__})) {
				$this->{__METHOD__} = new Zend_Log_Writer_Stream($this->getFilePath());
				$this->{__METHOD__}->setFormatter($this->getFormatter());
			}
		}
		return $this->{__METHOD__};
	}
	
	/** @return Zend_Log */
	private function getZendLog() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new Zend_Log($this->getWriter());
		}
		return $this->{__METHOD__};
	}

	/** @return void */
	private function prepareFileDir() {df_path()->prepareForWriting($this->getFileDir());}
	
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__FILE_DIR, self::V_STRING)
			->_prop(self::P__FILE_NAME, self::V_STRING_NE)
			->_prop(self::P__FORMATTER, 'Zend_Log_Formatter_Interface', false)
			->_prop(self::P__WRITER, 'Zend_Log_Writer_Abstract', false)
		;
		$this->prepareFileDir();
	}
	const _CLASS = __CLASS__;
	const P__FILE_DIR = 'file_dir';
	const P__FILE_NAME = 'file_name';
	const P__FORMATTER = 'formatter';
	const P__WRITER = 'writer';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Core_Model_Logger
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param string|array(string => mixed)|null $params [optional]
	 * @return Df_Core_Model_Logger
	 */
	public static function s($params = self::DEFAULT_FILE_NAME) {
		/** @var array(string => Df_Core_Model_Logger) */
		static $result = array();
		if (is_string($params)) {
			$params = array(self::P__FILE_NAME => $params);
		}
		/** @var string $fileName */
		$fileName = df_a($params, self::P__FILE_NAME);
		if (!$fileName) {
			$fileName = self::DEFAULT_FILE_NAME;
		}
		if (!isset($result[$fileName])) {
			$result[$fileName] = self::i($params);
		}
		return $result[$fileName];
	}
	const DEFAULT_FILE_NAME = 'system.log';
	const FORMAT__RAW = 'raw';
}