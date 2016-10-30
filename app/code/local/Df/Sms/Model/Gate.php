<?php
abstract class Df_Sms_Model_Gate extends Df_Core_Model {
	/** @return Df_Sms_Model_Gate */
	abstract public function send();

	/** @return Df_Sms_Model_Message */
	protected function getMessage() {
		return $this->cfg(self::P__MESSAGE);
	}

	/** @return string */
	protected function getSenderName() {
		return df_cfgr()->sms()->general()->getSender($this->store());
	}

	/**
	 * @used-by getSenderName()
	 * @used-by Df_Sms_Model_Gate_Sms16Ru::getToken()
	 * @return Df_Core_Model_StoreM
	 */
	protected function store() {return $this->cfg(self::P__STORE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__MESSAGE, Df_Sms_Model_Message::class)
			->_prop(self::P__STORE, Df_Core_Model_StoreM::class)
		;
	}

	const P__MESSAGE = 'message';
	const P__STORE = 'store';
}