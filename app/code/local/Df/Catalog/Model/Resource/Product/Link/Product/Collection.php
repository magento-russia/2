<?php
class Df_Catalog_Model_Resource_Product_Link_Product_Collection
	extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Link_Product_Collection {
	/**
	 * Некоторые сторонние оформительские темы не поддерживают режим денормализации
	 * для данного типа коллекций.
	 * Встретил глюк в Gala TitanShop:
	 * Column not found: 1054 Unknown column 'cat_index_position' in 'order clause'
	 * @override
	 * @return bool
	 */
	public function isEnabledFlat() {return false;}
}


 