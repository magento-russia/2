<?php
class Df_Qa_Helper_Method extends Mage_Core_Helper_Abstract {
	/**
	 * @param array $paramValue
	 * @param int $paramOrdering
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public function assertParamIsArray($paramValue, $paramOrdering, $stackLevel = 0) {
		$this->validateParam(Df_Zf_Validate_Array::s(), $paramValue, $paramOrdering, $stackLevel + 1);
	}

	/**
	 * @param mixed $paramValue
	 * @param int $paramOrdering
	 * @param int|float $min[optional]
	 * @param int|float $max[optional]
	 * @param int $stackLevel[optional]
	 * @return void
	 * @throws Exception
	 */
	public function assertParamIsBetween(
		$paramValue, $paramOrdering, $min = null, $max = null, $stackLevel = 0
	) {
		$this->validateParam(
			new Df_Zf_Validate_Between(array(
				'min' => is_null($min) ? PHP_INT_MIN : $min
				,'max' => is_null($max) ? PHP_INT_MAX : $max
				,'inclusive' => true
			))
			,$paramValue
			,$paramOrdering
			,$stackLevel + 1
		);
	}

	/**
	 * @param bool $paramValue
	 * @param int $paramOrdering
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public function assertParamIsBoolean($paramValue, $paramOrdering, $stackLevel = 0) {
		$this->validateParam(Df_Zf_Validate_Boolean::s(), $paramValue, $paramOrdering, $stackLevel + 1);
	}

	/**
	 * @param float $paramValue
	 * @param float $paramOrdering
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public function assertParamIsFloat($paramValue, $paramOrdering, $stackLevel = 0) {
		$this->validateParam(Df_Zf_Validate_Float::s(), $paramValue, $paramOrdering, $stackLevel + 1);
	}

	/**
	 * @param int $paramValue
	 * @param int $paramOrdering
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public function assertParamIsInteger($paramValue, $paramOrdering, $stackLevel = 0) {
		$this->validateParam(Df_Zf_Validate_Int::s(), $paramValue, $paramOrdering, $stackLevel + 1);
	}

	/**
	 * @param string $paramValue
	 * @param int $paramOrdering
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public function assertParamIsString($paramValue, $paramOrdering, $stackLevel = 0) {
		$this->validateParam(Df_Zf_Validate_String::s(), $paramValue, $paramOrdering, $stackLevel + 1);
	}

	/**
	 * @param array $resultValue
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public function assertResultIsArray($resultValue, $stackLevel = 0) {
		$this->validateResult(Df_Zf_Validate_Array::s(), $resultValue, $stackLevel + 1);
	}

	/**
	 * @param int|float $resultValue
	 * @param int|float $min [optional]
	 * @param int|float $max [optional]
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public function assertResultIsBetween($resultValue, $min = null, $max = null, $stackLevel = 0) {
		$this->validateResult(
			new Df_Zf_Validate_Between(
				array(
					'min' => is_null($min) ? PHP_INT_MIN : $min
					,'max' => is_null($max) ? PHP_INT_MAX : $max
					,'inclusive' => true
				)
			)
			,$resultValue
			,$stackLevel + 1
		);
	}

	/**
	 * @param bool $resultValue
	 * @param int $stackLevel[optional]
	 * @return void
	 * @throws Exception
	 */
	public function assertResultIsBoolean($resultValue, $stackLevel = 0) {
		$this->validateResult(Df_Zf_Validate_Boolean::s(), $resultValue, $stackLevel + 1);
	}

	/**
	 * @param float $resultValue
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public function assertResultIsFloat($resultValue, $stackLevel = 0) {
		$this->validateResult(Df_Zf_Validate_Float::s(), $resultValue, $stackLevel + 1);
	}

	/**
	 * @param int $resultValue
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public function assertResultIsInteger($resultValue, $stackLevel = 0) {
		$this->validateResult(Df_Zf_Validate_Int::s(), $resultValue, $stackLevel + 1);
	}

	/**
	 * @param string $resultValue
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public function assertResultIsString($resultValue, $stackLevel = 0) {
		$this->validateResult(Df_Zf_Validate_String::s(), $resultValue, $stackLevel + 1);
	}

	/**
	 * @param array $resultValue
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public function assertValueIsArray($resultValue, $stackLevel = 0) {
		$this->validateValue(Df_Zf_Validate_Array::s(), $resultValue, $stackLevel + 1);
	}

	/**
	 * @param int|float $value
	 * @param int|float $min [optional]
	 * @param int|float $max [optional]
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public function assertValueIsBetween($value, $min = null, $max = null, $stackLevel = 0) {
		$this->validateValue(
			new Df_Zf_Validate_Between(
				array(
					'min' => is_null($min) ? PHP_INT_MIN : $min
					,'max' => is_null($max) ? PHP_INT_MAX : $max
					,'inclusive' => true
				)
			)
			,$value
			,$stackLevel + 1
		);
	}

	/**
	 * @param bool $value
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public function assertValueIsBoolean($value, $stackLevel = 0) {
		$this->validateResult(Df_Zf_Validate_Boolean::s(), $value, $stackLevel + 1);
	}

	/**
	 * @param float $value
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public function assertValueIsFloat($value, $stackLevel = 0) {
		$this->validateValue(Df_Zf_Validate_Float::s(), $value, $stackLevel + 1);
	}

	/**
	 * @param int $value
	 * @param int $stackLevel[optional]
	 * @return void
	 * @throws Exception
	 */
	public function assertValueIsInteger($value, $stackLevel = 0) {
		$this->validateValue(Df_Zf_Validate_Int::s(), $value, $stackLevel + 1);
	}

	/**
	 * @param string $value
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public function assertValueIsString($value, $stackLevel = 0) {
		$this->validateValue(Df_Zf_Validate_String::s(), $value, $stackLevel + 1);
	}

	/**
	 * @param string $method
	 * @return void
	 */
	public function raiseErrorAbstract($method) {
		df_error('Метод должен быть явно определён: «%s»', $method);
	}

	/**
	 * @param string $validatorClass
	 * @param array $messages
	 * @param int $paramOrdering
	 * @param int $stackLevel
	 * @return void
	 */
	public function raiseErrorParam($validatorClass, array $messages, $paramOrdering, $stackLevel = 1) {
		/** @var Df_Qa_Model_Debug_Execution_State $state */
		$state = rm_caller($stackLevel + 1);
		/** @var string $paramName */
		$paramName = 'Неизвестный параметр';
		if (!is_null($paramOrdering) && $state->getMethod()) {
			/** @var ReflectionParameter $methodParameter */
			$methodParameter = $state->getMethodParameter($paramOrdering);
			if ($methodParameter instanceof ReflectionParameter) {
				$paramName = $methodParameter->getName();
			}
		}
		/** @var string $errorMessage */
		$errorMessage =
			strtr(
				"[%method%]\nПараметр «%paramName%» забракован проверяющим «%validatorClass%»."
				."\nСообщения проверяющего:\n%messages%\n\n"
				,array(
					'%method%' => $state->getMethodName()
					,'%paramName%' => $paramName
					,'%validatorClass%' => $validatorClass
					,'%messages%' => implode("\n", $messages)
				)
			)
		;
		$this->throwException($errorMessage, $stackLevel);
	}

	/**
	 * @param string $validatorClass
	 * @param array $messages
	 * @param int $stackLevel
	 * @return void
	 */
	public function raiseErrorResult($validatorClass, array $messages, $stackLevel = 1) {
		/** @var string $errorMessage */
		$errorMessage =
			strtr(
				"[%method%]\nРезультат метода забракован проверяющим «%validatorClass%»."
				."\nСообщения проверяющего:\n%messages%\n\n"
				,array(
					'%method%' => rm_caller($stackLevel + 1)->getMethodName()
					,'%validatorClass%' => $validatorClass
					,'%messages%' => implode("\n", $messages)
				)
			)
		;
		$this->throwException($errorMessage, $stackLevel);
	}

	/**
	 * @param string $method
	 * @return void
	 */
	public function raiseErrorShouldNotBeHere($method) {
		df_error('Метод «%s» запрещён для вызова.', $method);
	}

	/**
	 * @param mixed $paramValue
	 * @param string $className
	 * @param int $paramOrdering
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public function validateParamClass($paramValue, $className, $paramOrdering, $stackLevel = 0) {
		$this->validateParam(
			Df_Zf_Validate_Class::s($className), $paramValue, $paramOrdering, $stackLevel + 1
		);
	}

	/**
	 * @param mixed $resultValue
	 * @param string $className
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public function validateResultClass($resultValue, $className, $stackLevel = 0) {
		$this->validateResult(Df_Zf_Validate_Class::s($className), $resultValue, $stackLevel + 1);
	}

	/**
	 * @param mixed $value
	 * @param string $className
	 * @param int $stackLevel[optional]
	 * @return void
	 * @throws Exception
	 */
	public function validateValueClass($value, $className, $stackLevel = 0) {
		$this->validateResult(Df_Zf_Validate_Class::s($className), $value, $stackLevel + 1);
	}

	/**
	 * @param string $validatorClass
	 * @param array $messages
	 * @param int $stackLevel
	 * @return Df_Qa_Helper_Method
	 */
	public function raiseErrorVariable($validatorClass, array $messages, $stackLevel = 1) {
		/** @var string $errorMessage */
		$errorMessage =
			strtr(
				"[%method%]\nПеременная забракована проверяющим «%validatorClass%»."
				."\nСообщения проверяющего:\n%messages%\n\n"
				,array(
					'%method%' => rm_caller($stackLevel + 1)->getMethodName()
					,'%validatorClass%' => $validatorClass
					,'%messages%' => implode("\n", $messages)
				)
			)
		;
		$this->throwException($errorMessage, $stackLevel);
		return $this;
	}

	/**
	 * @param Zend_Validate_Interface $validator
	 * @param mixed $resultValue
	 * @param int $stackLevel
	 * @return void
	 * @throws Exception
	 */
	public function validateResult(Zend_Validate_Interface $validator, $resultValue, $stackLevel = 1) {
		if (!$validator->isValid($resultValue)) {
			$this
				->raiseErrorResult(
					$validatorClass = get_class($validator)
					,$messages = $validator->getMessages()
					,++$stackLevel
				)
			;
		}
	}

	/**
	 * @param Zend_Validate_Interface $validator
	 * @param mixed $value
	 * @param int $stackLevel
	 * @return void
	 * @throws Exception
	 */
	public function validateValue(Zend_Validate_Interface $validator, $value, $stackLevel = 1) {
		if (!$validator->isValid($value)) {
			/** @var string $errorMessage */
			$errorMessage =
				strtr(
					"Значение переменной забраковано проверяющим «%validatorClass%»."
					."\nСообщения проверяющего:\n%messages%"
					,array(
						'%validatorClass%' => get_class($validator)
						,'%messages%' => implode("\n", $validator->getMessages())
					)
				)
			;
			$this->throwException($errorMessage, $stackLevel);
		}
	}

	/**
	 * @param Zend_Validate_Interface $validator
	 * @param mixed $paramValue
	 * @param int $paramOrdering
	 * @param int $stackLevel
	 * @return void
	 * @throws Exception
	 */
	public function validateParam(Zend_Validate_Interface $validator, $paramValue, $paramOrdering, $stackLevel = 1) {
		if (!$validator->isValid($paramValue)) {
			$this
				->raiseErrorParam(
					$validatorClass = get_class($validator)
					,$messages = $validator->getMessages()
					,$paramOrdering
					,++$stackLevel
				)
			;
		}
	}

	/**
	 * @param string $message
	 * @param int $stackLevel[optional]
	 * @throws Df_Core_Exception_Internal
	 * @return void
	 */
	private function throwException($message, $stackLevel = 0) {
		/** @var Df_Core_Exception_Internal $exception */
		$exception = new Df_Core_Exception_Internal($message);
		$exception->setStackLevelsCountToSkip($stackLevel + 1);
		throw $exception;
	}

	/** @return Df_Qa_Helper_Method */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}