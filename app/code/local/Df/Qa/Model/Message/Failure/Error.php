<?php
class Df_Qa_Model_Message_Failure_Error extends Df_Qa_Model_Message_Failure {
	/**
	 * @override
	 * @return string
	 */
	public function getFailureMessage() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				implode(Df_Core_Const::T_NEW_LINE, array(
					rm_sprintf(
						'[%s] %s'
						,$this->getErrorTypeAsString()
						,$this->getErrorMessage()
					)
					,rm_sprintf('File: %s', $this->getErrorFile())
					,rm_sprintf('Line: %s', $this->getErrorLine())
				))
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @return bool
	 * @see Df_Qa_Model_Shutdown::process()
	 */
	public function isFatal() {return in_array($this->getErrorType(), $this->getFatalErrorTypes());}

	/**
	 * @override
	 * @return array(string => mixed)
	 */
	protected function getTrace() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * debug_backtrace не работает в функции-обработчике register_shutdown_function.
			 * Однако xdebug_get_function_stack — работает.
			 */
			$this->{__METHOD__} =
				(extension_loaded('xdebug'))
				? array_reverse(xdebug_get_function_stack())
				: array()
			;
			df_result_array($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param int $errorType
	 * @return string
	 */
	private function convertErrorTypeToString($errorType) {
		/** @var string $result */
		$result = df_a($this->getMapFromErrorTypeToLabel(), $errorType);
		df_result_string($result);
		return $result;
	}

	/** @return string */
	private function getErrorFile() {
		/** @var string $result */
		$result = df_a($this->getErrorLast(), self::ERROR___FILE);
		df_result_string($result);
		return $result;
	}

	/** @return array(string => string|int) */
	private function getErrorLast() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => string|int) $result */
			$result = error_get_last();
			if (is_null($result)) {
				$result = array();
			}
			df_result_array($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function getErrorLine() {return rm_nat0(df_a($this->getErrorLast(), self::ERROR___LINE));}

	/** @return string */
	private function getErrorMessage() {
		/** @var string $result */
		$result = df_a($this->getErrorLast(), self::ERROR___MESSAGE);
		df_result_string($result);
		return $result;
	}

	/** @return int */
	private function getErrorType() {return rm_int(df_a($this->getErrorLast(), self::ERROR___TYPE));}

	/** @return string */
	private function getErrorTypeAsString() {
		/** @var string $result */
		$result = $this->convertErrorTypeToString($this->getErrorType());
		df_result_string($result);
		return $result;
	}

	/** @return int[] */
	private function getFatalErrorTypes() {
		if (!isset($this->{__METHOD__})) {
			/** @var int[] $result */
			$result =
				array(
					E_ERROR
					,E_PARSE
					,E_CORE_ERROR
					,E_CORE_WARNING
					,E_COMPILE_ERROR
					,E_COMPILE_WARNING
				)
			;
			// xDebug при E_RECOVERABLE_ERROR останавивает работу интерпретатора
			if (extension_loaded ('xdebug')) {
				$result[]= E_RECOVERABLE_ERROR;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	private function getMapFromErrorTypeToLabel() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $result */
			$result =
				array(
					E_ERROR => 'E_ERROR'
					,E_WARNING => 'E_WARNING'
					,E_PARSE => 'E_PARSE'
					,E_NOTICE => 'E_NOTICE'
					,E_CORE_ERROR => 'E_CORE_ERROR'
					,E_CORE_WARNING => 'E_CORE_WARNING'
					,E_COMPILE_ERROR => 'E_COMPILE_ERROR'
					,E_COMPILE_WARNING => 'E_COMPILE_WARNING'
					,E_USER_ERROR => 'E_USER_ERROR'
					,E_USER_WARNING => 'E_USER_WARNING'
					,E_USER_NOTICE => 'E_USER_NOTICE'
					,E_STRICT => 'E_STRICT'
					,E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR'
				)
			;
			if (defined ('E_DEPRECATED')) {
				$result[E_DEPRECATED] = 'E_DEPRECATED';
			}
			if (defined ('E_USER_DEPRECATED')) {
				$result[E_USER_DEPRECATED] = 'E_USER_DEPRECATED';
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return int
	 */
	protected function getStackLevel() {
		return $this->cfg(self::P__STACK_LEVEL, 13);
	}
	const _CLASS = __CLASS__;
	const ERROR___FILE = 'file';
	const ERROR___LINE = 'line';
	const ERROR___MESSAGE = 'message';
	const ERROR___TYPE = 'type';
	/**
	 * @param array $parameters
	 * @return Df_Qa_Model_Message_Failure_Error
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}