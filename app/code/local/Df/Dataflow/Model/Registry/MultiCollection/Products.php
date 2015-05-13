<?php
class Df_Dataflow_Model_Registry_MultiCollection_Products
	extends Df_Dataflow_Model_Registry_MultiCollection {
	/**
	 * @override
	 * @param Mage_Core_Model_Store $store
	 * @return Df_Dataflow_Model_Registry_Collection_Products
	 */
	protected function getCollectionForStore(Mage_Core_Model_Store $store) {
		return Df_Dataflow_Model_Registry_Collection_Products::s($store);
	}

	/** @return Df_Dataflow_Model_Registry_MultiCollection_Products */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}
