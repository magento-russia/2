<?php
class Df_Directory_Helper_Assert extends Mage_Core_Helper_Abstract {
	/**
	 * @var Varien_Data_Collection_Db $collection
	 * @return void
	 */
	public function regionCollection(Varien_Data_Collection_Db $collection) {
		df_assert(df_h()->directory()->check()->regionCollection($collection));
	}

	/** @return Df_Directory_Helper_Assert */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}