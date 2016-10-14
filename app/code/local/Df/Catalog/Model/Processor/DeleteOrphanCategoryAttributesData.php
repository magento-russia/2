<?php
/**
 * Если база данных находится в некорректном состоянии,
 * то при денормализации таблиц товарных разделов может произойти сбой:
 * «Undefined offset (index) in Mage/Catalog/Model/Resource/Category/Flat.php»:
 * @see Df_Catalog_Model_Resource_Category_Flat::_getAttributeValues()
 * Данный класс чинит базу данных.
 */
class Df_Catalog_Model_Processor_DeleteOrphanCategoryAttributesData {
	/** @return void */
	public function process() {
		if (!$this->isProcessed()) {
			$this->_process();
			$this->setProcessed();
		}
	}

	/** @return void */
	private function _process() {
		/**
		 * 2014-12-15
		 * Между 2014-10-12 (версия 2.39.2) и 2014-12-15 (версия 2.42.0)
		 * массив идентификаторов товарных свойств по ошибке кэшировался в переменной.
		 * Однако кэшировать этот массив нельзя,
		 * потому что товарные свойства могли быть добавлены динамически
		 * (так делает, например, установщик модуля «Яндекс.Маркет» и сторонние оформительские темы).
		 * Получалось, что при установке Российской сборки Magento
		 * товарные свойства модуля «Яндекс.Маркет» сначала добавлялись, а потом тут же удалялись.
		 * А если Российская сборка Magento устанавливалась одновременно со стороней оформительской темой,
		 * то то же происходило и с товарными свойствами сторонней оформительской темы.
		 */
		/** @var int[] $attributeIds */
		$attributeIds = df_fetch_col_int('eav/attribute', 'attribute_id');;
		foreach ($this->getTablesToProcess() as $table) {
			/** @var string $table */
			df_table_delete_not($table, 'attribute_id', $attributeIds);
		}
	}

	/** @return string[] */
	private function getTablesToProcess() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $result */
			$result = array(df_table('catalog/eav_attribute'));
			foreach (Df_Catalog_Model_Resource_Category_Flat::getAttributeTypes() as $type) {
				/** @var string $type */
				$result[]= $this->resource()->getTableByType($type);
			}
			$this->{__METHOD__} = array_unique($result);
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function isProcessed() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::getStoreConfigFlag(self::$_CONFIG_PATH);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Catalog_Model_Resource_Category_Flat */
	private function resource() {return Df_Catalog_Model_Resource_Category_Flat::s();}

	/** @return void */
	private function setProcessed() {
		Mage::getConfig()->saveConfig(self::$_CONFIG_PATH, 1);
		Mage::getConfig()->reinit();
	}

	/** @var string */
	private static $_CONFIG_PATH = 'df/catalog/orhran_category_attributes_data_has_been_deleted';

	/** @return Df_Catalog_Model_Processor_DeleteOrphanCategoryAttributesData */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}