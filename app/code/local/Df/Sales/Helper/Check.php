<?php
class Df_Sales_Helper_Check extends Mage_Core_Helper_Abstract {
	/**
	 * @var Varien_Data_Collection_Db $collection
	 * @return bool
	 */
	public function orderCollection(Varien_Data_Collection_Db $collection) {
		return rm_is($collection,
			'Mage_Sales_Model_Resource_Order_Collection'
			, 'Mage_Sales_Model_Mysql4_Order_Collection'
		);
	}

	/**
	 * @var Varien_Data_Collection_Db $collection
	 * @return bool
	 */
	public function orderGridCollection(Varien_Data_Collection_Db $collection) {
		return rm_is($collection,
			'Mage_Sales_Model_Resource_Order_Grid_Collection'
			, 'Mage_Sales_Model_Mysql4_Order_Collection'
		);
	}

	/**
	 * @var Varien_Data_Collection_Db $collection
	 * @return bool
	 */
	public function quoteAddressItemCollection(Varien_Data_Collection_Db $collection) {
		return rm_is($collection,
			'Mage_Sales_Model_Resource_Quote_Address_Item_Collection'
			, 'Mage_Sales_Model_Mysql4_Quote_Address_Item_Collection'
		);
	}

	/** @return Df_Sales_Helper_Check */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}