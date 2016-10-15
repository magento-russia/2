<?php
class Df_Poll_Model_Resource_Poll_Answer_Collection 
	extends Mage_Poll_Model_Resource_Poll_Answer_Collection {
	/**
	 * @override
	 * @see Mage_Core_Model_Resource_Db_Collection_Abstract::getResource()
	 * @return Df_Poll_Model_Resource_Poll_Answer
	 */
	public function getResource() {return Df_Poll_Model_Resource_Poll_Answer::s();}

	/**
	 * Вынуждены сделать метод публичным, потому что публичен родительский.
	 * @override
	 * @return void
	 */
	public function _construct() {$this->_itemObjectClass = Df_Poll_Model_Poll_Answer::class;}

} 