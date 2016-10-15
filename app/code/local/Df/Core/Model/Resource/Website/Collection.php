<?php
class Df_Core_Model_Resource_Website_Collection extends Mage_Core_Model_Mysql4_Website_Collection {
	/**
	 * @override
	 * @return Df_Core_Model_Resource_Website
	 */
	public function getResource() {return Df_Core_Model_Resource_Website::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_itemObjectClass = Df_Core_Model_Website::class;
	}

}