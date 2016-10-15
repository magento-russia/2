<?php
class Df_CatalogSearch_Model_Resource_Search_Collection
	extends Mage_CatalogSearch_Model_Resource_Search_Collection {
	/**
	 * Цель перекрытия —
	 * отключить для этой коллекции режим денормализации для устранения сбоя
	 * «Call to undefined method Mage_Catalog_Model_Resource_Product_Flat::getEntityTablePrefix()
	 * File: app/code/core/Mage/Eav/Model/Entity/Attribute/Abstract.php
	 * Line: 511»
	 * @override
	 * @return bool
	 */
	public function isEnabledFlat() {return false;}
}


 