<?php
class Df_Core_Model_Resource_Store_Collection extends Mage_Core_Model_Mysql4_Store_Collection {
	/** @return string */
	public function getNames() {
		return df_quote_and_concat($this->getColumnValues(Df_Core_Model_Store::P__NAME));
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Core_Model_Store::mf(), Df_Core_Model_Resource_Store::mf());
	}
	const _CLASS = __CLASS__;

	/**
	 * @param Mage_Core_Model_Store[] $stores
	 * @return string
	 */
	public static function getNamesStatic(array $stores) {
		/** @var string[] $names */
		$names = array();
		foreach ($stores as $store) {
			/** @var Mage_Core_Model_Store $store */
			$names[]= $store->getName();
		}
		return df_quote_and_concat($names);
	}

	/** @return Df_Core_Model_Resource_Store_Collection */
	public static function i() {return new self;}
}