<?php
namespace Df\YandexMarket\Settings;
use Df_Catalog_Model_ConditionsLoader as ConditionsLoader;
class Products extends Yml {
	/** @return \Mage_CatalogRule_Model_Rule|null */
	public function getRule() {return dfc($this, function() {return
		ConditionsLoader::i(
			$this->getRuleId(), 'Яндекс.Маркет', '«Яндекс.Маркет» → «Товары» → «Условия»'
		)->getRule()
	;});}

	/** @return bool */
	public function needPublishOutOfStock() {return $this->getYesNo('publish_out_of_stock');}

	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_yandex_market/products/';}

	/** @return int */
	private function getRuleId() {return $this->nat0('conditions');}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}