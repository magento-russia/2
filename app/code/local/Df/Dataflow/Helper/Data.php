<?php
class Df_Dataflow_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return Df_Dataflow_Helper_Import */
	public function import() {
		return Df_Dataflow_Helper_Import::s();
	}

	/** @return Df_Dataflow_Model_Registry */
	public function registry() {return Df_Dataflow_Model_Registry::s();}

	/** @return Df_Dataflow_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}