<?php
class Df_Core_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return Df_Core_Helper_Mail */
	public function mail() {return Df_Core_Helper_Mail::s();}
	/** @return \Df\Core\Helper\Path */
	public function path() {return \Df\Core\Helper\Path::s();}
	/** @return Df_Dataflow_Model_Registry */
	public function registry() {return Df_Dataflow_Model_Registry::s();}
	/** @return Df_Core_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}