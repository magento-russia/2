<?php
class Df_Catalog_Model_Resource_Product_Attribute_Collection
	extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection {
	/**
	 * Цель перекрытия —
	 * перевод экранного названия «Special Price» товарного свойства «special_price»,
	 * а также экранных названий некоторых других товарных свойств.
	 * @link https://github.com/dfediuk/rm/commit/7ef0885000282541764964a45f27691e0f2639e3
	 * @override
	 * @return Df_Catalog_Model_Resource_Product_Attribute_Collection
	 */
	protected function _afterLoadData() {
		$this->translateLabels();
		parent::_afterLoadData();
		return $this;
	}

	/**
	 * Решает проблему перевода
	 * экранного названия «Special Price» товарного свойства «special_price»,
	 * а также экранных названий некоторых других товарных свойств.
	 * @link https://github.com/dfediuk/rm/commit/7ef0885000282541764964a45f27691e0f2639e3
	 *
	 * Обратите внимание, что этот метод решает проблему не полностью а лишь часть её.
	 * Другую часть решает метод Df_Eav_Model_Config::_save().
	 * Разница между методом
	 * Df_Catalog_Model_Resource_Product_Attribute_Collection::translateLabels()
	 * и методом Df_Eav_Model_Config::_save() состоит в том,
	 * что первый работает не с объектами-свойствами, а с ассоциативными массивами
	 * (так уж задумано в ядре).
	 *
	 * Есть ещё смежная проблема: перевод экранных названий опций
	 * (которые тоже являются товарными свойствами) настраиваемых товаров.
	 * Её решает метод
	 * @see Df_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection::_loadLabels()
	 * @return void
	 */
	private function translateLabels() {
		foreach ($this->_data as &$attributeData) {
			/** @var array(string => mixed $attributeData) */
			Df_Eav_Model_Translator::s()->translateAttributeAssoc($attributeData);
		}
	}

	/**
	 * Вынуждены делать данный метод публичным,
	 * потому что родительский метод
	 * @see Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection::_construct()
	 * публичен в Magento CE 1.4.0.1
	 * @override
	 * @return void
	 */
	public function _construct() {
		parent::_construct();
		$this->_init(
			Df_Catalog_Model_Resource_Eav_Attribute::mf(), Df_Catalog_Model_Resource_Attribute::mf()
		);
	}
	const _CLASS = __CLASS__;

	/** @return Df_Catalog_Model_Resource_Product_Attribute_Collection */
	public static function i() {return new self;}
}