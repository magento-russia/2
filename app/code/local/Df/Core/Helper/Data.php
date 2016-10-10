<?php
class Df_Core_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return Df_Core_Helper_Assert */
	public function assert() {return Df_Core_Helper_Assert::s();}
	/** @return Df_Core_Helper_Check */
	public function check() {return Df_Core_Helper_Check::s();}
	/** @return Df_Core_Helper_Config */
	public function config() {return Df_Core_Helper_Config::s();}
	/** @return Df_Core_Helper_Date */
	public function date() {return Df_Core_Helper_Date::s();}
	/** @return Df_Core_Helper_Db */
	public function db() {return Df_Core_Helper_Db::s();}
	/** @return Df_Core_Helper_File */
	public function file() {return Df_Core_Helper_File::s();}

	/** @return Df_Core_Helper_Data */
	public function forbid() {
		$this->_isUsageForbidden = true;
		return $this;
	}

	/** @return Df_Core_Helper_Fp */
	public function fp() {return Df_Core_Helper_Fp::s();}

	/** @return bool */
	public function isUsageForbidden() {return $this->_isUsageForbidden;}
	/** @var bool */
	private $_isUsageForbidden = false;

	/** @return Df_Core_Helper_Layout */
	public function layout() {return Df_Core_Helper_Layout::s();}
	/** @return Df_Core_Helper_Mail */
	public function mail() {return Df_Core_Helper_Mail::s();}
	/** @return Df_Core_Helper_Path */
	public function path() {return Df_Core_Helper_Path::s();}
	/** @return Df_Core_Model_Reflection */
	public function reflection() {return Df_Core_Model_Reflection::s();}
	/** @return Df_Dataflow_Model_Registry */
	public function registry() {return Df_Dataflow_Model_Registry::s();}
	/** @return Df_Core_Helper_RemoteControl */
	public function remoteControl() {return Df_Core_Helper_RemoteControl::s();}
	/** @return Df_Core_Helper_Request */
	public function request() {return Df_Core_Helper_Request::s();}
	/** @return Df_Core_Helper_Units */
	public function units() {return Df_Core_Helper_Units::s();}
	/** @return Df_Core_Helper_Url */
	public function url() {return Df_Core_Helper_Url::s();}
	/** @return Df_Core_Helper_Version */
	public function version() {return Df_Core_Helper_Version::s();}
	/** @return Df_Core_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}