<?php
class Df_Dataflow_Model_Registry_MultiCollection_Categories extends
	Df_Dataflow_Model_Registry_MultiCollection {
	/**
	 * @override
	 * @param Mage_Core_Model_Store $store
	 * @return Df_Dataflow_Model_Registry_Collection_Categories
	 */
	protected function getCollectionForStore(Mage_Core_Model_Store $store) {
		return Df_Dataflow_Model_Registry_Collection_Categories::s($store);
	}

	/** @return Df_Dataflow_Model_Registry_MultiCollection_Categories */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}
