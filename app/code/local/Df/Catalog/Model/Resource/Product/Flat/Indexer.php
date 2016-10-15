<?php
class Df_Catalog_Model_Resource_Product_Flat_Indexer
	extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Flat_Indexer {
	/**
	 * Цель перекрытия —
	 * кэширование товарных свойств при включенном режиме денормализации.
	 * @override
	 * @return string[]
	 */
	public function getAttributeCodes() {return df_eav_cache($this, __FUNCTION__);}

	/**
	 * @see getAttributeCodes()
	 * @return string[]
	 */
	public function getAttributeCodes_() {return parent::getAttributeCodes();}

	/**
	 * 2015-02-09
	 * Возвращаем объект-одиночку именно таким способом,
	 * потому что наш класс перекрывает посредством <rewrite> системный класс,
	 * и мы хотим, чтобы вызов @see Mage::getResourceSingleton() ядром Magento
	 * возвращал тот же объект, что и наш метод @see s(),
	 * сохраняя тем самым объект одиночкой (это важно, например, для производительности:
	 * сохраняя объект одиночкой — мы сохраняем его кэш между всеми пользователями объекта).
	 * @return Df_Catalog_Model_Resource_Product_Flat_Indexer
	 */
	public static function s() {return Mage::getResourceSingleton('catalog/product_flat_indexer');}
}


 