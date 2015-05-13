<?php
class Df_Qa_Model_Message_Notification extends Df_Qa_Model_Message {
	/** @return string */
	public function getNotification() {return $this->cfg(self::P__NOTIFICATION);}

	/**
	 * @override
	 * @return string
	 */
	protected function getTemplate() {return 'df/qa/log/notification.phtml';}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__NOTIFICATION, self::V_STRING_NE);
	}
	const _CLASS = __CLASS__;
	const P__NOTIFICATION = 'notification';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Qa_Model_Message_Notification
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}