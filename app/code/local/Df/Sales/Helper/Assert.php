<?php
class Df_Sales_Helper_Assert extends Mage_Core_Helper_Abstract {
	/**
	 * @var Varien_Data_Collection_Db $collection
	 * @return Df_Sales_Helper_Assert
	 */
	public function orderCollection(Varien_Data_Collection_Db $collection) {
		df_assert(df_h()->sales()->check()->orderCollection($collection));
		return $this;
	}

	/**
	 * @var Varien_Data_Collection_Db $collection
	 * @return Df_Sales_Helper_Assert
	 */
	public function orderGridCollection(Varien_Data_Collection_Db $collection) {
		df_assert(df_h()->sales()->check()->orderGridCollection($collection));
		return $this;
	}

	/** @return Df_Sales_Helper_Assert */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}