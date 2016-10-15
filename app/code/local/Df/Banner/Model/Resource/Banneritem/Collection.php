<?php
class Df_Banner_Model_Resource_Banneritem_Collection extends Df_Core_Model_Resource_Collection {
	/**
	 * @override
	 * @return Df_Banner_Model_Resource_Banneritem
	 */
	public function getResource() {return Df_Banner_Model_Resource_Banneritem::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_itemObjectClass = Df_Banner_Model_Banneritem::class;}
}