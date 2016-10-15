<?php
class Df_Sms_Model_Message extends Df_Core_Model {
	/** @return string */
	public function getBody() {
		return $this->cfg(self::P__BODY);
	}

	/** @return string */
	public function getReceiver() {
		return $this->cfg(self::P__RECEIVER);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__BODY, DF_V_STRING_NE)
			->_prop(self::P__RECEIVER, DF_V_STRING_NE)
		;
	}
	/** @used-by Df_Sms_Model_Gate::_construct() */

	const P__BODY = 'body';
	const P__RECEIVER = 'receiver';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Sms_Model_Gate_Sms16Ru
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}