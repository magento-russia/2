<?php
/**
 * В Magento CE 1.4 класс @see Mage_Review_Model_Mysql4_Review_Collection
 * унаследован напрямую от класса @see Varien_Data_Collection_Db
 * и не содержит методов @see Mage_Review_Model_Mysql4_Review_Collection::_construct()
 * и @see Mage_Review_Model_Mysql4_Review_Collection::_init().
 * Поэтому реализуем логику отсутствующих методов своим способом.
 * своим способом.
 *
 * 2016-10-16
 * Magento CE 1.4 отныне не поддерживаем.
 */
class Df_Review_Model_Resource_Review_Collection extends Mage_Review_Model_Resource_Review_Collection {
	/**
	 * @override
	 * @return Df_Review_Model_Resource_Review
	 */
	public function getResource() {return Df_Review_Model_Resource_Review::s();}

	/** @return Df_Review_Model_Resource_Review_Collection */
	public function limitLast() {
		$this->setDateOrder('DESC');
		$this->getSelect()->limit(1);
		return $this;
	}

	/**
	 * @override
	 * @see Mage_Review_Model_Resource_Review_Collection::_construct()
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_itemObjectClass = Df_Review_Model_Review::class;
	}
}