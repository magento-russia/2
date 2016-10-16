<?php
class Df_Customer_Helper_Check extends Mage_Core_Helper_Abstract {
	/**
	 * @var Varien_Data_Collection_Db $c
	 * @return bool
	 */
	public function customerCollection(Varien_Data_Collection_Db $c) {return
		$c instanceof Mage_Customer_Model_Resource_Customer_Collection
	;}

	/**
	 * @var Varien_Data_Collection_Db $c
	 * @return bool
	 */
	public function formAttributeCollection(Varien_Data_Collection_Db $c) {return
		$c instanceof Mage_Customer_Model_Resource_Form_Attribute_Collection
	;}

	/** @return Df_Customer_Helper_Check */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}