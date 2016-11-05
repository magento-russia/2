<?php
class Df_Catalog_Model_Product_Sku extends Df_Core_Model {
	/**
	 * @param string $sku
	 * @return string
	 */
	public function adapt($sku) {
		df_param_string_not_empty($sku, 0);
		if (!isset($this->{__METHOD__}[$sku])) {
			$this->{__METHOD__}[$sku] =
				(Df_Catalog_Model_Product::MAX_LENGTH__SKU >= mb_strlen($sku))
				?  $sku
				/**
				 * Заманчиво использовать 'SKU-' . md5($sku),
				 * однако нам надо сохранять совместимость модуля 1С с предыдущими версиями,
				 * где слишком длинные артикулы
				 * (которые создавались на основе внешних идентификторов)
				 * просто автоматически обрубались ядром.
				 */
				: mb_substr($sku, 0, Df_Catalog_Model_Product::MAX_LENGTH__SKU)
			;
		}
		return $this->{__METHOD__}[$sku];
	}


	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}