<?php
abstract class Df_Qa_Message_Failure extends Df_Qa_Message {
	/**
	 * @abstract
	 * @used-by states()
	 * @return array(array(string => string|int))
	 */
	abstract protected function trace();

	/**
	 * @used-by df_exception_get_trace()
	 * @used-by postface()
	 * @return string
	 */
	public final function traceS() {return $this->sections($this->states());}

	/**
	 * @override
	 * @see Df_Qa_Message::postface()
	 * @used-by Df_Qa_Message::report()
	 * @return string
	 */
	protected function postface() {return $this->traceS();}

	/**
	 * @override
	 * @see Df_Qa_Message::preface()
	 * @used-by Df_Qa_Message::report()
	 * @return string
	 */
	protected function preface() {return $this[self::P__ADDITIONAL_MESSAGE];}

	/**
	 * @used-by states()
	 * @see Df_Qa_Message_Failure_Exception::stackLevel()
	 * @see Df_Qa_Message_Failure_Error::stackLevel()
	 * @return int
	 */
	protected function stackLevel() {return 0;}

	/** @return Df_Qa_State[] */
	private function states() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Qa_State[] $result */
			$result = array();
			/** @var array(array(string => string|int)) $trace */
			$trace = array_slice($this->trace(), $this->stackLevel());
			/** @var Df_Qa_State|null $state */
			$state = null;
			foreach ($trace as $stateA) {
				/** @var array(string => string|int) $stateA */
				$state = Df_Qa_State::i($stateA, $state, $this->cfg(self::P__SHOW_CODE_CONTEXT, true));
				$result[]= $state;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__ADDITIONAL_MESSAGE, DF_V_STRING, false)
			->_prop(self::P__SHOW_CODE_CONTEXT, DF_V_BOOL, false)
		;
	}
	const P__ADDITIONAL_MESSAGE = 'additional_message';
	const P__SHOW_CODE_CONTEXT = 'show_code_context';
}