<?php
class Df_Core_Helper_RemoteControl extends Mage_Core_Helper_Data {
	/** @return bool */
	public function isItServer() {
		return $this->_isServer || ('dfa_server' === Mage::app()->getRequest()->getRouteName());
	}
	/**
	 * Вызывается только из @see Dfa_Server_Model_Action_Front::process()
	 * @return Df_Client_Helper_Data
	 */
	public function markAsServer() {$this->_isServer = true;}
	/** @var bool */
	private $_isServer = false;

	/** @return Df_Core_Helper_RemoteControl */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}