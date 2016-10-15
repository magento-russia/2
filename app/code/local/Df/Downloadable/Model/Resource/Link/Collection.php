<?php
class Df_Downloadable_Model_Resource_Link_Collection
	extends Mage_Downloadable_Model_Resource_Link_Collection {
	/**
	 * @override
	 * @return Df_Downloadable_Model_Resource_Link
	 */
	public function getResource() {return Df_Downloadable_Model_Resource_Link::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_itemObjectClass = Df_Downloadable_Model_Link::class;}

}