<?php
abstract class Df_Core_Model_RemoteControl_Message_Request extends Df_Core_Model_RemoteControl_Message {
	/**
	 * @abstract
	 * @return string
	 */
	abstract public function getActionClass();
	const _CLASS = __CLASS__;
}