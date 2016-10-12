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
		return df_cfg()->sms()->general()->getSender($this->getStore());
	}

	/** @return Mage_Core_Model_Store */
	protected function getStore() {
		return $this->cfg(self::P__STORE);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__MESSAGE, Df_Sms_Model_Message::_CLASS)
			->_prop(self::P__STORE, 'Mage_Core_Model_Store')
		;
	}
	const _CLASS = __CLASS__;
	const P__MESSAGE = 'message';
	const P__STORE = 'store';
}