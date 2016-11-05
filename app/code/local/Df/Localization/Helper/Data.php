<?php
class Df_Localization_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return array */
	public function getLanguages() {
		/** @var array $result */
		$result =
			Mage::app()->getLocale()->getLocale()
				->getTranslationList(
					self::TRANSLATION_LIST__LANGUAGE
					,Mage::app()->getLocale()->getLocale()
				)
		;
		df_result_array($result);
		return $result;
	}

	/** @return Df_Localization_Helper_Locale */
	public function locale() {return Df_Localization_Helper_Locale::s();}

	/** @return Df_Localization_Helper_Translation */
	public function translation() {return Df_Localization_Helper_Translation::s();}

	const TRANSLATION_LIST__LANGUAGE = 'language';

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}