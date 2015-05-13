<?php
class Df_Seo_Helper_Product_Image_Renamer extends Mage_Core_Helper_Abstract {
	/**
	 * @param string $initialFileName
	 * @param Df_Catalog_Model_Product $product
	 * @return string
	 */
	public function getSeoFileName($initialFileName, Df_Catalog_Model_Product $product) {
		df_param_string_not_empty($initialFileName, 0);
		/** @var array $fileInfo */
		$fileInfo = pathinfo($initialFileName);
		return df_concat_path(
			df_a($fileInfo, 'dirname')
			,rm_concat_clean('.'
				,df_output()->transliterate($product->getName())
				, df_a($fileInfo, 'extension')
			)
		);
	}

	const _CLASS = __CLASS__;
	/** @return Df_Seo_Helper_Product_Image_Renamer */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}