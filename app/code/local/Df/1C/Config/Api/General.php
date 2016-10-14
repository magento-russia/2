<?php
class Df_1C_Config_Api_General extends Df_1C_Config_Api_Cml2 {
	/**
	 * @used-by ссMapTo1C()
	 * @used-by rm_1c_currency_code_to_magento_format()
	 * @return array(string => string)
	 */
	public function ccMapFrom1C() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->map(
				'non_standard_currency_codes'
				, 'Df_1C_Config_MapItem_CurrencyCode'
				/** @uses Df_1C_Config_MapItem_CurrencyCode::getStandard() */
				, 'getStandard'
				/** @uses Df_1C_Config_MapItem_CurrencyCode::getNonStandardNormalized() */
				, 'getNonStandardNormalized'
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by rm_1c_currency_code_to_1c_format()
	 * @return array(string => string)
	 */
	public function ссMapTo1C() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_flip($this->ccMapFrom1C());
			/** @uses array_flip() при сбое может вернуть null */
			df_result_array($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getLogFileNameTemplate() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = strtr(
				df_trim(df_path()->adjustSlashes($this->getString('log_file_name_template')), DS)
				,array(
					'{store-view}' => rm_state()->getStoreProcessed()->getCode()
					,'{node}' => rm_request('node')
				)
			);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getLogFileNameTemplateBaseName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_last(explode(DS, $this->getLogFileNameTemplate()));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getLogFileNameTemplatePath() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_trim(
					str_replace(
						$this->getLogFileNameTemplateBaseName(), '', $this->getLogFileNameTemplate()
					)
					, DS
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return boolean */
	public function isEnabled() {return $this->getYesNo('enabled');}

	/** @return bool */
	public function needLogging() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getYesNo('enable_logging');
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_1c/general/';}

	/** @return Df_1C_Config_Api_General */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}