<?php
class Df_Qa_Method extends Mage_Core_Helper_Abstract {
	/**
	 * @param array $paramValue
	 * @param int $paramOrdering
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public static function assertParamIsArray($paramValue, $paramOrdering, $stackLevel = 0) {
		self::validateParam(Df_Zf_Validate_Array::s(), $paramValue, $paramOrdering, $stackLevel + 1);
	}

	/**
	 * @param mixed $paramValue
	 * @param int $paramOrdering
	 * @param int|float $min [optional]
	 * @param int|float $max [optional]
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public static function assertParamIsBetween(
		$paramValue, $paramOrdering, $min = null, $max = null, $stackLevel = 0
	) {
		self::validateParam(
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
	public static function assertParamIsBoolean($paramValue, $paramOrdering, $stackLevel = 0) {
		self::validateParam(Df_Zf_Validate_Boolean::s(), $paramValue, $paramOrdering, $stackLevel + 1);
	}

	/**
	 * @param float $paramValue
	 * @param float $paramOrdering
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public static function assertParamIsFloat($paramValue, $paramOrdering, $stackLevel = 0) {
		self::validateParam(Df_Zf_Validate_Float::s(), $paramValue, $paramOrdering, $stackLevel + 1);
	}

	/**
	 * @param int $paramValue
	 * @param int $paramOrdering
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public static function assertParamIsInteger($paramValue, $paramOrdering, $stackLevel = 0) {
		self::validateParam(Df_Zf_Validate_Int::s(), $paramValue, $paramOrdering, $stackLevel + 1);
	}

	/**
	 * @param string $paramValue
	 * @param int $paramOrdering
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public static function assertParamIsString($paramValue, $paramOrdering, $stackLevel = 0) {
		self::validateParam(Df_Zf_Validate_String::s(), $paramValue, $paramOrdering, $stackLevel + 1);
	}

	/**
	 * @param array $resultValue
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public static function assertResultIsArray($resultValue, $stackLevel = 0) {
		self::validateResult(Df_Zf_Validate_Array::s(), $resultValue, $stackLevel + 1);
	}

	/**
	 * @param int|float $resultValue
	 * @param int|float $min [optional]
	 * @param int|float $max [optional]
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public static function assertResultIsBetween($resultValue, $min = null, $max = null, $stackLevel = 0) {
		self::validateResult(
			new Df_Zf_Validate_Between(array(
				'min' => is_null($min) ? PHP_INT_MIN : $min
				,'max' => is_null($max) ? PHP_INT_MAX : $max
				,'inclusive' => true
			))
			,$resultValue
			,$stackLevel + 1
		);
	}

	/**
	 * @param bool $resultValue
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public static function assertResultIsBoolean($resultValue, $stackLevel = 0) {
		self::validateResult(Df_Zf_Validate_Boolean::s(), $resultValue, $stackLevel + 1);
	}

	/**
	 * @param float $resultValue
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public static function assertResultIsFloat($resultValue, $stackLevel = 0) {
		self::validateResult(Df_Zf_Validate_Float::s(), $resultValue, $stackLevel + 1);
	}

	/**
	 * @param int $resultValue
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public static function assertResultIsInteger($resultValue, $stackLevel = 0) {
		self::validateResult(Df_Zf_Validate_Int::s(), $resultValue, $stackLevel + 1);
	}

	/**
	 * @param string $resultValue
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public static function assertResultIsString($resultValue, $stackLevel = 0) {
		self::validateResult(Df_Zf_Validate_String::s(), $resultValue, $stackLevel + 1);
	}

	/**
	 * @param array $resultValue
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public static function assertValueIsArray($resultValue, $stackLevel = 0) {
		self::validateValue(Df_Zf_Validate_Array::s(), $resultValue, $stackLevel + 1);
	}

	/**
	 * @param int|float $value
	 * @param int|float $min [optional]
	 * @param int|float $max [optional]
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public static function assertValueIsBetween($value, $min = null, $max = null, $stackLevel = 0) {
		self::validateValue(
			new Df_Zf_Validate_Between(array(
				'min' => is_null($min) ? PHP_INT_MIN : $min
				,'max' => is_null($max) ? PHP_INT_MAX : $max
				,'inclusive' => true
			))
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
	public static function assertValueIsBoolean($value, $stackLevel = 0) {
		self::validateResult(Df_Zf_Validate_Boolean::s(), $value, $stackLevel + 1);
	}

	/**
	 * @param float $value
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public static function assertValueIsFloat($value, $stackLevel = 0) {
		self::validateValue(Df_Zf_Validate_Float::s(), $value, $stackLevel + 1);
	}

	/**
	 * @param int $value
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public static function assertValueIsInteger($value, $stackLevel = 0) {
		self::validateValue(Df_Zf_Validate_Int::s(), $value, $stackLevel + 1);
	}

	/**
	 * @param string $value
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public static function assertValueIsString($value, $stackLevel = 0) {
		self::validateValue(Df_Zf_Validate_String::s(), $value, $stackLevel + 1);
	}

	/**
	 * @param string $method
	 * @return void
	 */
	public static function raiseErrorAbstract($method) {
		df_error("Метод должен быть явно определён: «{$method}».");
	}

	/**
	 * @param string $validatorClass
	 * @param array $messages
	 * @param int $paramOrdering
	 * @param int $stackLevel
	 * @return void
	 */
	public static function raiseErrorParam($validatorClass, array $messages, $paramOrdering, $stackLevel = 1) {
		/** @var Df_Qa_State $state */
		$state = self::caller($stackLevel);
		/** @var string $paramName */
		$paramName = 'Неизвестный параметр';
		if (!is_null($paramOrdering) && $state->method()) {
			/** @var ReflectionParameter $methodParameter */
			$methodParameter = $state->methodParameter($paramOrdering);
			if ($methodParameter instanceof ReflectionParameter) {
				$paramName = $methodParameter->getName();
			}
		}
		/** @var string $messagesS */
		$messagesS = df_concat_n($messages);
		self::throwException(
			"[{$state->methodName()}]"
			."\nПараметр «{$paramName}» забракован проверяющим «{$validatorClass}»."
			."\nСообщения проверяющего:\n{$messagesS}\n\n"
			, $stackLevel
		);
	}

	/**
	 * @param string $validatorClass
	 * @param array $messages
	 * @param int $stackLevel
	 * @return void
	 */
	public static function raiseErrorResult($validatorClass, array $messages, $stackLevel = 1) {
		/** @var string $messagesS */
		$messagesS = df_concat_n($messages);
		/** @var string $method */
		$method = self::caller($stackLevel)->methodName();
		self::throwException(
			"[{$method}]\nРезультат метода забракован проверяющим «{$validatorClass}»."
			."\nСообщения проверяющего:\n{$messagesS}\n\n"
			, $stackLevel
		);
	}

	/**
	 * @param mixed $paramValue
	 * @param string $className
	 * @param int $paramOrdering
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public static function validateParamClass($paramValue, $className, $paramOrdering, $stackLevel = 0) {
		self::validateParam(
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
	public static function validateResultClass($resultValue, $className, $stackLevel = 0) {
		self::validateResult(Df_Zf_Validate_Class::s($className), $resultValue, $stackLevel + 1);
	}

	/**
	 * @param mixed $value
	 * @param string $className
	 * @param int $stackLevel [optional]
	 * @return void
	 * @throws Exception
	 */
	public static function validateValueClass($value, $className, $stackLevel = 0) {
		self::validateResult(Df_Zf_Validate_Class::s($className), $value, $stackLevel + 1);
	}

	/**
	 * @param string $validatorClass
	 * @param array $messages
	 * @param int $stackLevel
	 * @return void
	 */
	public static function raiseErrorVariable($validatorClass, array $messages, $stackLevel = 1) {
		/** @var string $messagesS */
		$messagesS = df_concat_n($messages);
		/** @var string $method */
		$method = self::caller($stackLevel)->methodName();
		self::throwException(
			"[{$method}]\nПеременная забракована проверяющим «{$validatorClass}»."
			."\nСообщения проверяющего:\n{$messagesS}\n\n"
			, $stackLevel
		);
	}

	/**
	 * @param Zend_Validate_Interface $validator
	 * @param mixed $resultValue
	 * @param int $stackLevel
	 * @return void
	 * @throws Exception
	 */
	public static function validateResult(Zend_Validate_Interface $validator, $resultValue, $stackLevel = 1) {
		if (!$validator->isValid($resultValue)) {
			self::raiseErrorResult(
				$validatorClass = get_class($validator)
				,$messages = $validator->getMessages()
				,++$stackLevel
			);
		}
	}

	/**
	 * @param Zend_Validate_Interface $validator
	 * @param mixed $value
	 * @param int $stackLevel
	 * @return void
	 * @throws Exception
	 */
	public static function validateValue(Zend_Validate_Interface $validator, $value, $stackLevel = 1) {
		if (!$validator->isValid($value)) {
			/** @var string $messagesS */
			$messagesS = df_concat_n($validator->getMessages());
			/** @var string $validatorClass */
			$validatorClass = get_class($validator);
			self::throwException(
				"Значение переменной забраковано проверяющим «{$validatorClass}»."
				."\nСообщения проверяющего:\n{$messagesS}"
				, $stackLevel
			);
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
	public static function validateParam(
		Zend_Validate_Interface $validator, $paramValue, $paramOrdering, $stackLevel = 1
	) {
		if (!$validator->isValid($paramValue)) {
			self::raiseErrorParam(
				$validatorClass = get_class($validator)
				,$messages = $validator->getMessages()
				,$paramOrdering
				,++$stackLevel
			);
		}
	}

	/**
	 * Ообъект Df_Qa_State конструируется на основе $stackLevel + 2,
	 * потому что нам нужно вернуть название метода,
	 * который вызвал тот метод, который вызвал метод caller.
	 * @used-by raiseErrorParam()
	 * @used-by raiseErrorResult()
	 * @used-by raiseErrorVariable()
	 * @param int $stackLevel [optional]
	 * @return Df_Qa_State
	 */
	private static function caller($stackLevel) {return Df_Qa_State::i(df_a(debug_backtrace(), $stackLevel + 2));}

	/**
	 * @param string $message
	 * @param int $stackLevel [optional]
	 * @throws Df_Core_Exception
	 * @return void
	 */
	private static function throwException($message, $stackLevel = 0) {
		/**
		 * 2015-01-28
		 * Раньше тут стояло throw $e, что приводило к отображению на экране
		 * диагностического сообщения в неверной кодировке.
		 * @uses df_error() точнее: эта функция в режиме разработчика
		 * отсылает браузеру заголовок HTTP о требуемой кодировке.
		 */
		df_error(new Df_Core_Exception($message, $stackLevel + 1));
	}
}