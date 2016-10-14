<?php
class Df_Rating_Model_Resource_Rating_Collection extends Mage_Rating_Model_Mysql4_Rating_Collection {
	/**
	 * @override
	 * @return Df_Rating_Model_Resource_Rating
	 */
	public function getResource() {return Df_Rating_Model_Resource_Rating::s();}

	/**
	 * Родительский метод по дурости объявлен публичным в Magento CE 1.4.0.1
	 * (@see Mage_Rating_Model_Mysql4_Rating_Collection::_construct()),
	 * поэтому и нам приходится делать его публичным.
	 * @override
	 * @return void
	 */
	public function _construct() {$this->_itemObjectClass = Df_Rating_Model_Rating::_C;}
	const _C = __CLASS__;
}