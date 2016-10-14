<?php
class Df_Core_Model_Resource_Store_Collection extends Mage_Core_Model_Mysql4_Store_Collection {
	/** @return string */
	public function getNames() {
		return df_csv_pretty_quote($this->getColumnValues(Df_Core_Model_Store::P__NAME));
	}

	/**
	 * @override
	 * @return Df_Core_Model_Resource_Store
	 */
	public function getResource() {return Df_Core_Model_Resource_Store::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_itemObjectClass = Df_Core_Model_Store::_C;
	}
	const _C = __CLASS__;

	/**
	 * @param Df_Core_Model_StoreM[] $stores
	 * @return string
	 */
	public static function getNamesStatic(array $stores) {
		/** @uses Mage_Core_Model_Store::getName() */
		return df_csv_pretty_quote(df_each($stores, 'getName'));
	}
}