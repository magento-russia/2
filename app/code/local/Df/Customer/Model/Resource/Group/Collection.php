<?php
class Df_Customer_Model_Resource_Group_Collection extends Mage_Customer_Model_Entity_Group_Collection {
	/**
	 * Цель перекрытия —
	 * нам нужно, чтобы коллекция категорий покупателей использовала наши классы:
	 * @see Df_Customer_Model_Group
	 * @see Df_Customer_Model_Resource_Group
	 * @override
	 * @return Df_Customer_Model_Resource_Group
	 */
	public function getResource() {return Df_Customer_Model_Resource_Group::s();}

	/**
	 * Цель перекрытия —
	 * нам нужно, чтобы коллекция категорий покупателей использовала наши классы:
	 * @see Df_Customer_Model_Group
	 * @see Df_Customer_Model_Resource_Group
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_itemObjectClass = Df_Customer_Model_Group::class;}


	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}