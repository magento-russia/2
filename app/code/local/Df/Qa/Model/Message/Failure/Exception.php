<?php
class Df_Qa_Model_Message_Failure_Exception extends Df_Qa_Model_Message_Failure {
	/**
	 * @override
	 * @return string
	 */
	public function getFailureMessage() {return rm_ets($this->getException());}

	/**
	 * @override
	 * @return array
	 */
	protected function getTrace() {return $this->getException()->getTrace();}

	/** @return Exception */
	private function getException() {return $this->cfg(self::P__EXCEPTION);}

	/**
	 * @override
	 * @return int
	 */
	protected function getStackLevel() {
		/** @var int $result */
		$result = parent::getStackLevel();
		if ($this->getException() instanceof Df_Core_Exception) {
			/** @var Df_Core_Exception $exception */
			$exception = $this->getException();
			$result = $exception->getStackLevelsCountToSkip();
		}
		df_result_integer($result);
		return $result;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__EXCEPTION, 'Exception');
	}
	const _CLASS = __CLASS__;
	const P__EXCEPTION = 'exception';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Qa_Model_Message_Failure_Exception
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}