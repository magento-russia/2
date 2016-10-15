<?php
class Df_SalesRule_Model_Resource_Rule_Collection extends Mage_SalesRule_Model_Mysql4_Rule_Collection {
	/**
	 * @override
	 * @return Df_SalesRule_Model_Resource_Rule
	 */
	public function getResource() {return Df_SalesRule_Model_Resource_Rule::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_itemObjectClass = Df_SalesRule_Model_Rule::class;
	}


}