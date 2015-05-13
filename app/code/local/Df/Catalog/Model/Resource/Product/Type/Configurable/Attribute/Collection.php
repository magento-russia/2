<?php
class Df_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection
	extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Type_Configurable_Attribute_Collection {
	/**
	 * Решает проблему перевода экранных названий опций
	 * (которые являются товарными свойствами) настраиваемых товаров.
	 * @link https://github.com/dfediuk/rm/commit/e4aecb5ebda695c40e1d004b569e2872e149a44c
	 *
	 * Смежную проблему перевода экранных названий товарных свойств решают методы:
	 * Df_Catalog_Model_Resource_Product_Attribute_Collection::translateLabels()
	 * Df_Eav_Model_Config::_save()
	 * @override
	 * @return Df_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection
	 */
	protected function _loadLabels() {
		parent::_loadLabels();
		foreach ($this->_items as $configurableAttribute) {
			/** @var Mage_Catalog_Model_Product_Type_Configurable_Attribute $configurableAttribute */
			/** @var string $label */
			$label = $configurableAttribute->getData('label');
			if ($label) {
				$configurableAttribute->setData('label', Df_Eav_Model_Translator::s()->translateLabel($label));
			}
		}
		return $this;
	}
}

 