<?php
class Df_Qa_Message_Notification extends Df_Qa_Message {
	/**
	 * @override
	 * @see Df_Qa_Message::main()
	 * @used-by Df_Qa_Message::report()
	 * @return string
	 */
	protected function main() {return $this[self::P__NOTIFICATION];}

	/**
	 * @override
	 * @return void
	 */
	protected final function _construct() {
		parent::_construct();
		$this->_prop(self::P__NOTIFICATION, RM_V_STRING_NE);
	}
	const P__NOTIFICATION = 'notification';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Qa_Message_Notification
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}