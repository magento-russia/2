<?php
class Df_Downloadable_Model_Resource_Sample_Collection
	extends Mage_Downloadable_Model_Mysql4_Sample_Collection {
	/**
	 * @override
	 * @return Df_Downloadable_Model_Resource_Sample
	 */
	public function getResource() {return Df_Downloadable_Model_Resource_Sample::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_itemObjectClass = Df_Downloadable_Model_Sample::class;}

}