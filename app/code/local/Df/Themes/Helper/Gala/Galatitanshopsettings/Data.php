<?php
/** @noinspection PhpUndefinedClassInspection */
class Df_Themes_Helper_Gala_Galatitanshopsettings_Data extends Gala_Galatitanshopsettings_Helper_Data {
	/**
	 * @override
	 * @param Df_Catalog_Model_Product $_product
	 * @return string|int
	 */
	public function getPercentOff($_product) {
		/** @var string|int $result */
		/** @noinspection PhpUndefinedClassInspection */
		$result = parent::getPercentOff($_product);
		if (is_string($result)) {
			$result = str_replace('>off <', '>-<', $result);
		}
		return $result;
	}
}