<?php
class Df_1C_Model_Settings_General extends Df_1C_Model_Settings_Cml2 {
	/** @return array(string => string) */
	public function getCurrencyCodesMapFrom1CToMagento() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => string) $result */
			$result = array();
			/** @var string|null $mapSerialized */
			$mapSerialized = $this->getStringNullable('non_standard_currency_codes');
			if ($mapSerialized) {
				df_assert_string_not_empty($mapSerialized);
				/** @var array[] $map */
				$map = @unserialize($mapSerialized);
				if (is_array($map)) {
					foreach ($map as $mapItem) {
						/** @var string[] $mapItem */
						df_assert_array($mapItem);
						/** @var string $nonStandardCode */
						$nonStandardCode = df_nts(df_a($mapItem,
							Df_1C_Block_System_Config_Form_Field_NonStandardCurrencyCodes
								::COLUMN__NON_STANDARD
						));
						/** @var string $standardCode */
						/**
						 * Обратите внимание, что ключ COLUMN__STANDARD
						 * может отсутствовать в массиве:
						 * @link http://magento-forum.ru/topic/4893/
						 * Такое возможно даже в двух ситуациях
						 * при неряшливом заполнении таблицы
						 * «Нестандартные символьные коды валют»
						 * администратором магазина:
						 * 1) когда администратор добавил в эту таблицу пустую строку в конец.
						 * 2) когда администратор указал для строки таблицы
						 * лишь нестандартный сивольный код (первая колонка таблицы),
						 * не указав стандартный символьный код (вторая колонка таблицы).
						 */
						$standardCode = df_nts(df_a($mapItem,
							Df_1C_Block_System_Config_Form_Field_NonStandardCurrencyCodes
								::COLUMN__STANDARD
						));
						if ($nonStandardCode && $standardCode) {
							$nonStandardCode =
								df_h()->_1c()->cml2()->normalizeNonStandardCurrencyCode(
									$nonStandardCode
								)
							;
							$result[$nonStandardCode] = $standardCode;
						}
					}
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string) */
	public function getCurrencyCodesMapFromMagentoTo1C() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_flip($this->getCurrencyCodesMapFrom1CToMagento());
			/** При сбое @see array_flip может вернуть null */
			df_result_array($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getLogFileNameTemplate() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				strtr(
					df_trim(df_path()->adjustSlashes($this->getString('log_file_name_template')), DS)
					,array(
						'{store-view}' => rm_state()->getStoreProcessed()->getCode()
						,'{node}' => df_request('node')
					)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getLogFileNameTemplateBaseName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_last(explode(DS, $this->getLogFileNameTemplate()));
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

	/** @return Df_1C_Model_Settings_General */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}