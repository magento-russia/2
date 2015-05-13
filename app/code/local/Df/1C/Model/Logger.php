<?php
class Df_1C_Model_Logger extends Df_Core_Model_Logger {
	/**
	 * @override
	 * @return string
	 */
	protected function getFileDir() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_concat_path(
					Mage::getBaseDir('var'), 'log'
					, df_cfg()->_1c()->general()->getLogFileNameTemplatePath()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getFileName() {
		/** @var string $result */
		$result = Df_1C_Model_Cml2_Session_ByCookie_1C::s()->getFileName_Log();
		if (!$result) {
			$result =
				basename(Df_Core_Model_Fs_GetNotUsedFileName::i(
					$this->getFileDir(), df_cfg()->_1c()->general()->getLogFileNameTemplateBaseName()
				)->getResult())
			;
			Df_1C_Model_Cml2_Session_ByCookie_1C::s()->setFileName_Log($result);
		}
		return $result;
	}

	/**
	 * @override
	 * @return Df_Zf_Log_Formatter_Benchmark
	 */
	protected function getFormatter() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new Df_Zf_Log_Formatter_Benchmark();
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Model_Logger */
	public static function s2() {static $r; return $r ? $r : $r = new self;}
}