<?php
// 2016-10-09
// Экспорт товаров из интернет-магазина в МойСклад.
class Df_MoySklad_Settings_Export_Products extends Df_Core_Model_Settings {
	/**
	 * 2016-10-11
	 * «Приставка для кодов товаров в МойСклад»
	 * @return string
	 */
	public function codePrefix() {return $this->getString(__FUNCTION__);}
	/**  
	 * 2016-10-09
	 * @return Mage_CatalogRule_Model_Rule|null 
	 */
	public function rule() {if (!isset($this->{__METHOD__})) {$this->{__METHOD__} = df_n_set(
		Df_Catalog_Model_ConditionsLoader::i(
			$this->getNatural0('conditions'), 'МойСклад', '«МойСклад» → «Экспорт товаров» → «Условия»'
		)->getRule()
	);}return df_n_get($this->{__METHOD__});}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_moysklad/export_products/';}
	/** @return Df_MoySklad_Settings_Export_Products */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}