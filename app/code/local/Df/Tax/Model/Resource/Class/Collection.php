<?php
class Df_Tax_Model_Resource_Class_Collection extends Mage_Tax_Model_Resource_Class_Collection {
	/**
	 * 2015-04-11
	 * @used-by Df_Tax_Model_Class_Source_Product::getAllOptions()
	 * @return void
	 */
	public function filterByShopCountry() {
		/** @var string|null $shopIso2 */
		$shopIso2 = df_store_iso2();
		if ($shopIso2) {
			/**
			 * Выпадающий список должен отображать не только налоговые классы страны магазина,
			 * но и налоговые классы, для которых страна не указана
			 * (ибо в Magento CE нет возможности указать для налогового класса страну,
			 * и если администратор завёл налоговые классы до установки Российской сборки Magento,
			 * то он должен видеть их в выпадающем списке
			 * несмотря на то, что они не привязаны к стране магазина).
			 */
			$this->addFieldToFilter(Df_Tax_Model_Class::P__ISO2, array(
				array('eq' => $shopIso2), array('is_null' => true)
			));
		}
	}
}


