<?php
class Df_Core_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return Df_Core_Helper_Assert */
	public function assert() {return Df_Core_Helper_Assert::s();}
	/** @return Df_Core_Helper_Check */
	public function check() {return Df_Core_Helper_Check::s();}
	/** @return Df_Core_Helper_Db */
	public function db() {return Df_Core_Helper_Db::s();}
	/** @return Df_Core_Helper_File */
	public function file() {return Df_Core_Helper_File::s();}

	/** @return Df_Core_Helper_Data */
	public function forbid() {
		$this->_isUsageForbidden = true;
		return $this;
	}

	/** @return bool */
	public function isUsageForbidden() {return $this->_isUsageForbidden;}
	/** @var bool */
	private $_isUsageForbidden = false;

	/** @return Df_Core_Helper_Mail */
	public function mail() {return Df_Core_Helper_Mail::s();}
	/** @return \Df\Core\Helper\Path */
	public function path() {return \Df\Core\Helper\Path::s();}
	/** @return Df_Dataflow_Model_Registry */
	public function registry() {return Df_Dataflow_Model_Registry::s();}
	/** @return Df_Core_Helper_Request */
	public function request() {return Df_Core_Helper_Request::s();}
	/** @return Df_Core_Helper_Version */
	public function version() {return Df_Core_Helper_Version::s();}
	/** @return Df_Core_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}