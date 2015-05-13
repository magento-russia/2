<?php
class Df_Eav_Helper_Assert extends Mage_Core_Helper_Abstract {
	/**
	 * @var Varien_Data_Collection_Db $collection
	 * @return Df_Eav_Helper_Assert
	 */
	public function entityAttributeCollection(Varien_Data_Collection_Db $collection) {
		df_assert(df_h()->eav()->check()->entityAttributeCollection($collection));
		return $this;
	}

	/**
	 * @var Varien_Data_Collection_Db $collection
	 * @return Df_Eav_Helper_Assert
	 */
	public function entityAttributeOptionCollection(Varien_Data_Collection_Db $collection) {
		df_assert(df_h()->eav()->check()->entityAttributeOptionCollection($collection));
		return $this;
	}

	/** @return Df_Eav_Helper_Assert */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}