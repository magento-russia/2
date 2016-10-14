<?php
class Df_Dataflow_Model_Registry_MultiCollection_Categories extends
	Df_Dataflow_Model_Registry_MultiCollection {
	/**
	 * @override
	 * @param Df_Core_Model_StoreM $store
	 * @return Df_Dataflow_Model_Registry_Collection_Categories
	 */
	protected function getCollectionForStore(Df_Core_Model_StoreM $store) {
		return Df_Dataflow_Model_Registry_Collection_Categories::s($store);
	}

	/** @return Df_Dataflow_Model_Registry_MultiCollection_Categories */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}
