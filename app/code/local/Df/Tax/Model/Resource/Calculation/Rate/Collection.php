<?php
class Df_Tax_Model_Resource_Calculation_Rate_Collection
	extends Mage_Tax_Model_Mysql4_Calculation_Rate_Collection {
	/**
	 * @override
	 * @return Df_Tax_Model_Resource_Calculation_Rate
	 */
	public function getResource() {return Df_Tax_Model_Resource_Calculation_Rate::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_itemObjectClass = Df_Tax_Model_Calculation_Rate::_C;}
}

