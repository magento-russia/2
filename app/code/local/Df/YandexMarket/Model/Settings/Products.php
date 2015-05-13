<?php
class Df_YandexMarket_Model_Settings_Products extends Df_YandexMarket_Model_Settings_Yml {
	/** @return Mage_CatalogRule_Model_Rule|null */
	public function getRule() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				Df_Catalog_Model_ConditionsLoader::i(
					$this->getRuleId(), 'Яндекс.Маркет', '«Яндекс.Маркет» → «Товары» → «Условия»'
				)->getRule()
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return bool */
	public function needPublishOutOfStock() {return $this->getYesNo('publish_out_of_stock');}

	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_yandex_market/products/';}

	/** @return int */
	private function getRuleId() {return $this->getNatural0('conditions');}

	/** @return Df_YandexMarket_Model_Settings_Products */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}