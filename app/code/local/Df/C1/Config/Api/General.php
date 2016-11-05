<?php
namespace Df\C1\Config\Api;
class General extends \Df\C1\Config\Api\Cml2 {
	/**
	 * @used-by ссMapTo1C()
	 * @used-by df_c1_currency_code_to_magento_format()
	 * @return array(string => string)
	 */
	public function ccMapFrom1C() {return dfc($this, function() {return $this->map(
		'non_standard_currency_codes'
		, \Df\C1\Config\MapItem\CurrencyCode::class
		/** @uses \Df\C1\Config\MapItem\CurrencyCode::getStandard() */
		, 'getStandard'
		/** @uses \Df\C1\Config\MapItem\CurrencyCode::getNonStandardNormalized() */
		, 'getNonStandardNormalized'
	);});}

	/**
	 * @used-by df_c1_currency_code_to_1c_format()
	 * @return array(string => string)
	 */
	public function ссMapTo1C() {return dfc($this, function() {
		/** @var array(string => string) $result */
		$result = array_flip($this->ccMapFrom1C());
		/** @uses array_flip() при сбое может вернуть null */
		df_result_array($result);
		return $result;
	});}

	/** @return string */
	public function getLogFileNameTemplate() {return dfc($this, function() {return
		strtr(df_trim_ds_left(df_path_n($this->v('log_file_name_template'))), [
			'{store-view}' => df_state()->getStoreProcessed()->getCode()
			,'{node}' => df_request('node')
		])
	;});}

	/** @return string */
	public function getLogFileNameTemplateBaseName() {return dfc($this, function() {return
		df_last(explode(DS, $this->getLogFileNameTemplate()))
	;});}

	/** @return string */
	public function getLogFileNameTemplatePath() {return dfc($this, function() {return df_trim_ds(
		str_replace($this->getLogFileNameTemplateBaseName(), '', $this->getLogFileNameTemplate())
	);});}

	/** @return boolean */
	public function isEnabled() {return $this->getYesNo('enabled');}

	/** @return bool */
	public function needLogging() {return dfc($this, function() {return
		$this->getYesNo('enable_logging')				
	;});}

	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_1c/general/';}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}