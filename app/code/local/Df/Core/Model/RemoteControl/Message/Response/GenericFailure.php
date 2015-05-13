<?php
class Df_Core_Model_RemoteControl_Message_Response_GenericFailure
	extends Df_Core_Model_RemoteControl_Message_Response {
	/**
	 * @override
	 * @return string
	 */
	public function isOk() {return false;}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param string $text
	 * @return Df_Core_Model_RemoteControl_Message_Response_GenericFailure
	 */
	public static function i($text) {return new self(array(self::P__TEXT => $text));}
}