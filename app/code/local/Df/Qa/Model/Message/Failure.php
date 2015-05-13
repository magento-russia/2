<?php
abstract class Df_Qa_Model_Message_Failure extends Df_Qa_Model_Message {
	/**
	 * @abstract
	 * @return string
	 */
	abstract public function getFailureMessage();

	/**
	 * @abstract
	 * @return array
	 */
	abstract protected function getTrace();

	/** @return string|null */
	public function getAdditionalMessage() {return $this->cfg(self::P__ADDITIONAL_MESSAGE);}

	/** @return string */
	public function getTraceAsText() {return implode("\n", $this->getTraceAsArray());}

	/** @return int */
	protected function getStackLevel() {return $this->cfg(self::P__STACK_LEVEL, 0);}

	/**
	 * @override
	 * @return string
	 */
	protected function getTemplate() {return 'df/qa/log/failure.phtml';}

	/** @return Df_Qa_Model_Debug_Execution_State[] */
	private function getTraceAsArray() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Qa_Model_Debug_Execution_State[] $result */
			$result = array();
			/** @var array $trace */
			$trace = array_slice($this->getTrace(), $this->getStackLevel());
			/** @var Df_Qa_Model_Debug_Execution_State|null $previousExecutionState */
			$previousExecutionState = null;
			foreach ($trace as $executionState) {
				/** @var array $executionState */
				/** @var Df_Qa_Model_Debug_Execution_State $state */
				$state = Df_Qa_Model_Debug_Execution_State::i(array_merge($executionState, array(
					Df_Qa_Model_Debug_Execution_State::P__SHOW_CODE_CONTEXT => $this->showCodeContext()
					,Df_Qa_Model_Debug_Execution_State::P__STATE_PREVIOUS => $previousExecutionState
				)));
				$result[]= $state;
				if (!is_null($previousExecutionState)) {
					$previousExecutionState->setData(
						Df_Qa_Model_Debug_Execution_State::P__STATE_NEXT, $state
					);
				}
				$previousExecutionState = $state;
			}
			df_result_array($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function showCodeContext() {return $this->cfg(self::P__SHOW_CODE_CONTEXT, true);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__ADDITIONAL_MESSAGE, self::V_STRING, false)
			->_prop(self::P__SHOW_CODE_CONTEXT, self::V_BOOL, false)
			->_prop(self::P__STACK_LEVEL, self::V_INT, false)
		;
	}
	const _CLASS = __CLASS__;
	const P__ADDITIONAL_MESSAGE = 'additional_message';
	const P__SHOW_CODE_CONTEXT = 'show_code_context';
	const P__STACK_LEVEL = 'stack_level';
}