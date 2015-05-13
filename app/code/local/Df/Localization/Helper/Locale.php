<?php
class Df_Localization_Helper_Locale extends Mage_Core_Helper_Abstract {
	/**
	 * @param string $localeCode
	 * @return string
	 */
	public function getLanguageCodeByLocaleCode($localeCode) {
		df_param_string($localeCode, 0);
		/** @var string $result */
		$result =
			df_a(
				explode(
					self::SEPARATOR
					,$localeCode
				)
				,0
			)
		;
		df_result_string($result);
		return $result;
	}

	/** @return bool */
	public function isRussian() {
		/** @var bool $result */
		$result =
			(
					Df_Core_Const::LOCALE__RUSSIAN
				===
					Mage::app()->getLocale()->getLocaleCode()
			)
		;
		return $result;
	}

	const SEPARATOR = '_';

	/** @return Df_Localization_Helper_Locale */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}