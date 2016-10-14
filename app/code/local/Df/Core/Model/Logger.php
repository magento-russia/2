<?php
final class Df_Core_Model_Logger extends Df_Core_Model {
	/**
	 * @param string $message
	 * @return void
	 */
	public function log($message) {
		/** @var mixed[] $args */
		$args = func_get_args();
		$this->_logger()->log(df_format($args), Zend_Log::DEBUG);
	}

	/**
	 * @param string $message
	 * @return void
	 */
	public function logRaw($message) {
		/** @var mixed[] $args */
		$args = func_get_args();
		$this->_logger()->log(
			df_format($args)
			, Zend_Log::DEBUG
			, array(Df_Zf_Log_Formatter_Benchmark::FORMAT__RAW => true)
		);
	}

	/**
	 * @used-by log()
	 * @used-by logRaw()
	 * @return Zend_Log
	 */
	private function _logger() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $filePath */
			$filePath = $this[self::$P__FILE_PATH];
			if (!file_exists($filePath)) {
				rm_file_put_contents($filePath, '');
			}
			/** @var Zend_Log_Writer_Stream $writer */
			$writer = new Zend_Log_Writer_Stream($filePath);
			$writer->setFormatter(new Df_Zf_Log_Formatter_Benchmark());
			$this->{__METHOD__} = new Zend_Log($writer);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__FILE_PATH, RM_V_STRING_NE);
	}
	/** @var string */
	private static $P__FILE_PATH = 'file_path';

	/**
	 * @static
	 * @param string $filePath
	 * @return Df_Core_Model_Logger
	 */
	public static function s($filePath) {
		/** @var array(string => Df_Core_Model_Logger) $cache */
		static $cache;
		if (!isset($cache[$filePath])) {
			$cache[$filePath] = new self(array(self::$P__FILE_PATH => $filePath));
		}
		return $cache[$filePath];
	}
}