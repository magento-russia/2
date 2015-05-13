<?php
class Df_Core_Model_RemoteControl_Message_Response extends Df_Core_Model_RemoteControl_Message {
	/** @return string */
	public function getText() {return $this->cfg(self::P__TEXT);}

	/** @return string */
	public function isOk() {return $this->cfg(self::P__IS_OK);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__IS_OK, self::V_BOOL)
			->_prop(self::P__TEXT, self::V_STRING)
		;
	}
	const _CLASS = __CLASS__;
	const P__IS_OK = 'is_ok';
	const P__TEXT = 'text';
}