<?php
class Df_1C_Model_Settings_CatalogExport extends Df_1C_Model_Settings_Cml2 {
	/** @return Mage_CatalogRule_Model_Rule|null */
	public function getRule() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				Df_Catalog_Model_ConditionsLoader::i(
					$this->getRuleId()
					, '1С:Управление торговлей'
					, '«1С:Управление торговлей» → «Экспорт товаров в 1С» → «Условия»'
				)->getRule()
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_1c/catalog_export/';}

	/** @return int */
	private function getRuleId() {return $this->getNatural0('product_conditions');}

	/** @return Df_1C_Model_Settings_CatalogExport */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}