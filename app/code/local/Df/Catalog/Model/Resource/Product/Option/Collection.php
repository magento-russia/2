<?php
class Df_Catalog_Model_Resource_Product_Option_Collection
	extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Option_Collection {
	/**
	 * @override
	 * @return Df_Catalog_Model_Resource_Product_Option_Collection
	 */
	protected function _afterLoadData() {
		$this->translateLabels();
		parent::_afterLoadData();
		return $this;
	}

	/**
	 * Решает проблему перевода названий настраиваемых опций
	 * для простых (не настраиваемых!) товаров
	 * @return void
	 */
	private function translateLabels() {
		foreach ($this->_data as &$optionData) {
			/** @var array(string => mixed $optionData) */
			Df_Eav_Model_Translator::s()->translateOptionAssoc($optionData);
		}
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(
			Df_Catalog_Model_Product_Option::mf(), Df_Catalog_Model_Resource_Product_Option::mf()
		);
	}
	const _CLASS = __CLASS__;

	/** @return Df_Catalog_Model_Resource_Product_Option_Collection */
	public static function i() {return new self;}
}