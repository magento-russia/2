<?php
class Df_Core_Helper_Assert extends Mage_Core_Helper_Abstract {
	/**
	 * @var Varien_Data_Collection_Db $collection
	 * @return Df_Core_Helper_Assert
	 */
	public function resourceDbCollectionAbstract(Varien_Data_Collection_Db $collection) {
		df_assert(df()->check()->resourceDbCollectionAbstract($collection));
		return $this;
	}

	/**
	 * @var Df_Core_Model_StoreM|int|string|bool|null $store
	 * @return Df_Core_Helper_Assert
	 */
	public function storeAsParameterForGettingConfigValue($store) {
		df_assert(df()->check()->storeAsParameterForGettingConfigValue($store));
		return $this;
	}

	/**
	 * @var Varien_Data_Collection_Db $collection
	 * @return void
	 */
	public function storeCollection(Varien_Data_Collection_Db $collection) {
		df_assert(df()->check()->storeCollection($collection));
	}

	/**
	 * @var Varien_Data_Collection_Db $collection
	 * @return void
	 */
	public function websiteCollection(Varien_Data_Collection_Db $collection) {
		df_assert(df()->check()->websiteCollection($collection));
	}
	/** @return Df_Core_Helper_Assert */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}