<?php
class Df_Rating_Model_Resource_Rating_Collection extends Mage_Rating_Model_Mysql4_Rating_Collection {
	/**
	 * Родительский метод по дурости объявлен публичным в Magento CE 1.4.0.1
	 * (@see Mage_Rating_Model_Mysql4_Rating_Collection::_construct()),
	 * поэтому и нам приходится делать его публичным.
	 * @override
	 * @return void
	 */
	public function _construct() {
		parent::_construct();
		$this->_init(Df_Rating_Model_Rating::mf(), Df_Rating_Model_Resource_Rating::mf());
	}
	const _CLASS = __CLASS__;

	/** @return Df_Rating_Model_Resource_Rating_Collection */
	public static function i() {return new self;}
}