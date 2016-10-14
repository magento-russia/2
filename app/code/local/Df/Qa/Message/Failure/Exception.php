<?php
final class Df_Qa_Message_Failure_Exception extends Df_Qa_Message_Failure {
	/**
	 * @override
	 * @see Df_Qa_Message::main()
	 * @used-by Df_Qa_Message::report()
	 * @return string
	 */
	protected function main() {return $this->e()->message();}

	/**
	 * @override
	 * @see Df_Qa_Message_Failure::postface()
	 * @used-by Df_Qa_Message::report()
	 * @return string
	 */
	protected function postface() {
		return $this->sections($this->sections($this->e()->comments()), parent::postface());
	}

	/**
	 * @override
	 * @see Df_Qa_Message_Failure::stackLevel()
	 * @used-by Df_Qa_Message_Failure::states()
	 * @return int
	 */
	protected function stackLevel() {return $this->e()->getStackLevelsCountToSkip();}

	/**
	 * @override
	 * @see Df_Qa_Message_Failure::trace()
	 * @used-by Df_Qa_Message_Failure::states()
	 * @return array(array(string => string|int))
	 */
	protected function trace() {return $this->e()->getTraceRm();}

	/**
	 * @used-by stackLevel()
	 * @used-by trace()
	 * @return Df_Core_Exception
	 */
	private function e() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Core_Exception::wrap($this[self::P__EXCEPTION]);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__EXCEPTION, 'Exception');
	}
	const P__EXCEPTION = 'exception';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Qa_Message_Failure_Exception
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}