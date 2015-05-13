<?php
class Df_Client_Model_Message_Response_CreateAdminAccount
	extends Df_Core_Model_RemoteControl_Message_Response {
	/** @return string */
	public function getUrlAdmin() {
		return $this->cfg(self::P__URL__ADMIN);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__URL__ADMIN, self::V_STRING_NE);
	}

	const _CLASS = __CLASS__;
	const P__URL__ADMIN = 'url_admin';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Client_Model_Message_Response_CreateAdminAccount
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}