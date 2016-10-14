<?php
class Df_Localization_Helper_Locale extends Mage_Core_Helper_Abstract {
	/**
	 * @param string $localeCode
	 * @return string
	 */
	public function getLanguageCodeByLocaleCode($localeCode) {
		df_param_string_not_empty($localeCode, 0);
		/** @var string $result */
		$result = df_a(explode(self::SEPARATOR, $localeCode), 0);
		df_result_string_not_empty($result);
		return $result;
	}

	/** @return bool */
	public function isRussian() {return 'ru_RU' === rm_locale();}

	const SEPARATOR = '_';

	/** @return Df_Localization_Helper_Locale */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}