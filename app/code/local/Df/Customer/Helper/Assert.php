<?php
class Df_Customer_Helper_Assert extends Mage_Core_Helper_Abstract {
	/**
	 * @var Varien_Data_Collection_Db $collection
	 * @return Df_Customer_Helper_Assert
	 */
	public function customerCollection(Varien_Data_Collection_Db $collection) {
		df_assert(df_h()->customer()->check()->customerCollection($collection));
		return $this;
	}

	/**
	 * @var Varien_Data_Collection_Db $collection
	 * @return Df_Customer_Helper_Assert
	 */
	public function formAttributeCollection(Varien_Data_Collection_Db $collection) {
		df_assert(df_h()->customer()->check()->formAttributeCollection($collection));
		return $this;
	}

	/** @return Df_Customer_Helper_Assert */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}