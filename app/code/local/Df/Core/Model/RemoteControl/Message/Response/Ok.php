<?php
class Df_Core_Model_RemoteControl_Message_Response_Ok extends Df_Core_Model_RemoteControl_Message_Response {
	/**
	 * @override
	 * @return string
	 */
	public function isOk() {
		return true;
	}

	const _CLASS = __CLASS__;
}