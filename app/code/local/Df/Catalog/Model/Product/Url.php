<?php
class Df_Catalog_Model_Product_Url extends Mage_Catalog_Model_Product_Url {
	/**
	 * Цель перекрытия —
	 * улучшение транслитерации русских букв в адресах товаров.
	 * @param string $str
	 * @return string
	 */
	public function formatUrlKey($str) {
		/** @var bool $needFormat */
		static $needFormat;
		if (!isset($needFormat)) {
			$needFormat =
					df_enabled(Df_Core_Feature::SEO)
				&&
					df_cfg()->seo()->common()->getEnhancedRussianTransliteration()
			;
		}
		return
			$needFormat
			? Df_Catalog_Helper_Product_Url::s()->extendedFormat($str)
			: parent::formatUrlKey($str)
		;
	}
}