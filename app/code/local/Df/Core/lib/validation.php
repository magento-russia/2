<?php
if (!defined ('PHP_INT_MIN')) {
	define('PHP_INT_MIN', ~PHP_INT_MAX);
}

/**
 * @param string $method
 * @return void
 */
function df_abstract($method) {
	df_param_string($method, 0);
	Df_Qa_Method::raiseErrorAbstract($method);
}

/**
 * @param mixed $condition
 * @param string|Mage_Core_Exception $message[optional]
 * @return void
 * @throws Mage_Core_Exception
 */
function df_assert($condition, $message = null) {
	if (df_enable_assertions()) {
		if (!$condition) {
			/** @var Mage_Core_Exception $exception */
			$exception = null;
			if ($message instanceof Mage_Core_Exception) {
				$exception = $message;
			}
			else {
				/** @var Df_Core_Exception_Internal $exception */
				$exception = new Df_Core_Exception_Internal($message);
				$exception->setStackLevelsCountToSkip(0);
			}
			df_error_internal($exception);
		}
	}
}

/**
 * @param array $paramValue
 * @param int $stackLevel[optional]
 * @return void
 * @throws Exception
 */
function df_assert_array($paramValue, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Df_Qa_Method::assertValueIsArray($paramValue, $stackLevel + 1);
	}
}

/**
 * @param int|float $value
 * @param int|float $min[optional]
 * @param int|float $max[optional]
 * @param int $stackLevel[optional]
 * @return void
 * @throws Exception
 */
function df_assert_between($value, $min = null, $max = null, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Df_Qa_Method::assertValueIsBetween($value, $min, $max, $stackLevel + 1);
	}
}

/**
 * @param bool $value
 * @param int $stackLevel[optional]
 * @return void
 * @throws Exception
 */
function df_assert_boolean($value, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Df_Qa_Method::assertValueIsBoolean($value, $stackLevel + 1);
	}
}

/**
 * @param object $value
 * @param string $class
 * @param int $stackLevel[optional]
 * @return void
 * @throws Exception
 */
function df_assert_class($value, $class, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Df_Qa_Method::validateValueClass($value, $class, $stackLevel + 1);
	}
}

/**
 * @param string|int|float $expectedResult
 * @param string|int|float $valueToTest
 * @param string|Mage_Core_Exception $message[optional]
 * @return void
 * @throws Mage_Core_Exception
 */
function df_assert_eq($expectedResult, $valueToTest, $message = null) {
	if (df_enable_assertions()) {
		if ($expectedResult !== $valueToTest) {
			/** @var Mage_Core_Exception $exception */
			if (!$message) {
				$message =
					rm_sprintf(
						'Проверяющий ожидал значение «%s», однако получил значение «%s».'
						, $expectedResult
						, $valueToTest
					)
				;
			}
			if ($message instanceof Mage_Core_Exception) {
				$exception = $message;
			}
			else {
				/** @var Df_Core_Exception_Internal $exception */
				$exception = new Df_Core_Exception_Internal($message);
				$exception->setStackLevelsCountToSkip(0);
			}
			df_error_internal($exception);
		}
	}
}

/**
 * @param float $value
 * @param int $stackLevel[optional]
 * @return void
 */
function df_assert_float($value, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Df_Qa_Method::assertValueIsFloat($value, $stackLevel + 1);
	}
}

/**
 * @param int|float $lowBound
 * @param int|float $valueToTest
 * @param string|Mage_Core_Exception $message[optional]
 * @return void
 * @throws Mage_Core_Exception
 */
function df_assert_ge($lowBound, $valueToTest, $message = null) {
	if (df_enable_assertions()) {
		if ($lowBound > $valueToTest) {
			/** @var Mage_Core_Exception $exception */
			if (!$message) {
				$message =
					rm_sprintf(
						'Проверяющий ожидал значение не меньше «%s», однако получил значение «%s».'
						, $lowBound
						, $valueToTest
					)
				;
			}
			if ($message instanceof Mage_Core_Exception) {
				$exception = $message;
			}
			else {
				/** @var Df_Core_Exception_Internal $exception */
				$exception = new Df_Core_Exception_Internal($message);
				$exception->setStackLevelsCountToSkip(0);
			}
			df_error_internal($exception);
		}
	}
}

/**
 * @param int|float $lowBound
 * @param int|float $valueToTest
 * @param string|Mage_Core_Exception $message[optional]
 * @return void
 * @throws Mage_Core_Exception
 */
function df_assert_gt($lowBound, $valueToTest, $message = null) {
	if (df_enable_assertions()) {
		if ($lowBound >= $valueToTest) {
			/** @var Mage_Core_Exception $exception */
			if (!$message) {
				$message =
					rm_sprintf(
						'Проверяющий ожидал значение больше «%s», однако получил значение «%s».'
						, $lowBound
						, $valueToTest
					)
				;
			}
			if ($message instanceof Mage_Core_Exception) {
				$exception = $message;
			}
			else {
				/** @var Df_Core_Exception_Internal $exception */
				$exception = new Df_Core_Exception_Internal($message);
				$exception->setStackLevelsCountToSkip(0);
			}
			df_error_internal($exception);
		}
	}
}

/**
 * @param int|float $valueToTest
 * @param string|Mage_Core_Exception $message[optional]
 * @return void
 * @throws Mage_Core_Exception
 */
function df_assert_gt0($valueToTest, $message = null) {
	if (df_enable_assertions()) {
		if (0 >= $valueToTest) {
			/** @var Mage_Core_Exception $exception */
			if (!$message) {
				$message =
					rm_sprintf(
						'Проверяющий ожидал положительное значение, однако получил «%s».'
						, $valueToTest
					)
				;
			}
			if ($message instanceof Mage_Core_Exception) {
				$exception = $message;
			}
			else {
				/** @var Df_Core_Exception_Internal $exception */
				$exception = new Df_Core_Exception_Internal($message);
				$exception->setStackLevelsCountToSkip(0);
			}
			df_error_internal($exception);
		}
	}
}

/**
 * @param int|float $valueToTest
 * @param mixed[] $allowedResults
 * @param string|Mage_Core_Exception $message[optional]
 * @return void
 * @throws Mage_Core_Exception
 */
function df_assert_in($valueToTest, array $allowedResults, $message = null) {
	if (df_enable_assertions()) {
		if (!in_array($valueToTest, $allowedResults, $strict = true)) {
			/** @var Mage_Core_Exception $exception */
			if (!$message) {
				$message =
					10 >= count($allowedResults)
					? rm_sprintf(
						'Проверяющий ожидал значение из множества «%s», однако получил значение «%s».'
						, df_concat_enum($allowedResults)
						, $valueToTest
					)
					: rm_sprintf(
						'Проверяющий получил значение «%s», отсутствующее в допустимом множестве значений.'
						, $valueToTest
					)
				;
			}
			if ($message instanceof Mage_Core_Exception) {
				$exception = $message;
			}
			else {
				/** @var Df_Core_Exception_Internal $exception */
				$exception = new Df_Core_Exception_Internal($message);
				$exception->setStackLevelsCountToSkip(0);
			}
			df_error_internal($exception);
		}
	}
}

/**
 * @param int $value
 * @param int $stackLevel
 * @return void
 */
function df_assert_integer($value, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Df_Qa_Method::assertValueIsInteger($value, $stackLevel + 1);
	}
}

/**
 * @param int|float $highBound
 * @param int|float $valueToTest
 * @param string|Mage_Core_Exception $message[optional]
 * @return void
 * @throws Mage_Core_Exception
 */
function df_assert_le($highBound, $valueToTest, $message = null) {
	if (df_enable_assertions()) {
		if ($highBound < $valueToTest) {
			/** @var Mage_Core_Exception $exception */
			if (!$message) {
				$message =
					rm_sprintf(
						'Проверяющий ожидал значение не больше «%s», однако получил значение «%s».'
						, $highBound
						, $valueToTest
					)
				;
			}
			if ($message instanceof Mage_Core_Exception) {
				$exception = $message;
			}
			else {
				/** @var Df_Core_Exception_Internal $exception */
				$exception = new Df_Core_Exception_Internal($message);
				$exception->setStackLevelsCountToSkip(0);
			}
			df_error_internal($exception);
		}
	}
}

/**
 * @param int|float $highBound
 * @param int|float $valueToTest
 * @param string|Mage_Core_Exception $message[optional]
 * @return void
 * @throws Mage_Core_Exception
 */
function df_assert_lt($highBound, $valueToTest, $message = null) {
	if (df_enable_assertions()) {
		if ($highBound <= $valueToTest) {
			/** @var Mage_Core_Exception $exception */
			if (!$message) {
				$message =
					rm_sprintf(
						'Проверяющий ожидал значение меньше «%s», однако получил значение «%s».'
						, $highBound
						, $valueToTest
					)
				;
			}
			if ($message instanceof Mage_Core_Exception) {
				$exception = $message;
			}
			else {
				/** @var Df_Core_Exception_Internal $exception */
				$exception = new Df_Core_Exception_Internal($message);
				$exception->setStackLevelsCountToSkip(0);
			}
			df_error_internal($exception);
		}
	}
}

/**
 * @param string|int|float $notExpectedResult
 * @param string|int|float $valueToTest
 * @param string|Mage_Core_Exception $message[optional]
 * @return void
 * @throws Mage_Core_Exception
 */
function df_assert_ne($notExpectedResult, $valueToTest, $message = null) {
	if (df_enable_assertions()) {
		if ($notExpectedResult === $valueToTest) {
			/** @var Mage_Core_Exception $exception */
			if (!$message) {
				$message =
					rm_sprintf(
						'Проверяющий ожидал значение, отличное от «%s», однако получил именно его.'
						, $notExpectedResult
					)
				;
			}
			if ($message instanceof Mage_Core_Exception) {
				$exception = $message;
			}
			else {
				/** @var Df_Core_Exception_Internal $exception */
				$exception = new Df_Core_Exception_Internal($message);
				$exception->setStackLevelsCountToSkip(0);
			}
			df_error_internal($exception);
		}
	}
}

/**
 * @param string $value
 * @param int $stackLevel[optional]
 * @return void
 * @throws Exception
 */
function df_assert_string($value, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Df_Qa_Method::assertValueIsString($value, $stackLevel + 1);
	}
}

/**
 * @param string $value
 * @param int $stackLevel[optional]
 * @return void
 * @throws Exception
 */
function df_assert_string_not_empty($value, $stackLevel = 0) {
	df_assert_string($value, $stackLevel + 1);
	if (df_enable_assertions()) {
		Df_Qa_Method::assertValueIsString($value, $stackLevel + 1);
		/**
		 * Раньше тут стояло if (!$value), что тоже неправильно,
		 * ибо непустая строка '0' не проходит такую валидацию.
		 */
		if ('' === strval($value)) {
			Df_Qa_Method::raiseErrorVariable(
				$validatorClass = __FUNCTION__
				,$messages = array('Требуется непустая строка, но вместо неё получена пустая.')
				,$stackLevel + 1
			);
		}
	}
}

/**
 * @param mixed $value
 * @return bool
 */
function df_check_array($value) {return Df_Zf_Validate_Array::s()->isValid($value);}

/**
 * @param int|float  $value
 * @param int|float $min[optional]
 * @param int|float $max[optional]
 * @return bool
 */
function df_check_between($value, $min = null, $max = null) {
	$validator =
		new Df_Zf_Validate_Between(
			array(
				'min' => is_null($min) ? PHP_INT_MIN : $min
				,'max' => is_null($max) ? PHP_INT_MAX : $max
				,'inclusive' => true
			)
		)
	;
	return $validator->isValid($value);
}

/**
 * @param bool $value
 * @return bool
 */
function df_check_boolean($value) {return Df_Zf_Validate_Boolean::s()->isValid($value);}

/**
 * @param mixed $value
 * @return bool
 */
function df_check_float($value) {return Df_Zf_Validate_Float::s()->isValid($value);}

/**
 * @param mixed $value
 * @return bool
 */
function df_check_integer($value) {
	/**
	 * Обратите внимание, что здесь нужно именно «==», а не «===».
	 * @link http://ru2.php.net/manual/en/function.is-int.php#35820
	 */
	return is_numeric($value) && ($value == intval($value));
}

/**
 * @param string $value
 * @return bool
 */
function df_check_string($value) {return Df_Zf_Validate_String::s()->isValid($value);}

/**
 * @param mixed $value
 * @return bool
 */
function df_check_string_not_empty($value) {return Df_Zf_Validate_String_NotEmpty::s()->isValid($value);}

/** @return bool */
function df_enable_assertions() {
	/** @var bool $result */
	static $result;
	if (!isset($result)) {
		/**
		 * Нельзя вызывать @see Mage::getStoreConfig(),
		 * если текущий магазин ещё не инициализирован!
		 */
		if (Df_Core_Model_State::s()->isStoreInitialized()) {
			$result = true;
			if (df_module_enabled(Df_Core_Module::SPEED)) {
				/** @var string|null $configValue */
				$configValue = Mage::getStoreConfig('df_speed/general/enable_assertions');
				if (!is_null($configValue)) {
					$result = rm_bool($configValue);
				}
			}
		}
	}
	return isset($result) ? $result : true;
}

/**
 * @param string|string[]|Exception|null $message[optional]
 * @return void
 * @throws Exception
 * @throws Df_Core_Exception_Client
 */
function df_error($message = null) {
	/**
	 * К сожалению, мы указывать кодировку в обработчике, устанвленном @see set_exception_handler(),
	 * потому что set_exception_handler в Magento работать не будет
	 * из-за глобального try..catch в методе @see Mage::run()
	 */
	if (!headers_sent()) {
		header('Content-Type: text/html; charset=UTF-8');
	}
	if ($message instanceof Exception) {
		/** @var Exception $message */
		throw $message;
	}
	else {
		if (is_array($message)) {
			$message = implode("\r\n\r\n", $message);
		}
		else {
			/** @var string[] $arguments */
			$arguments = func_get_args();
			$message = rm_sprintf($arguments);
		}
		throw new Df_Core_Exception_Client($message);
	}
}

/**
 * @param string|Exception|null $message[optional]
 * @return void
 * @throws Exception
 * @throws Df_Core_Exception_Internal
 */
function df_error_internal($message = null) {
	/**
	 * К сожалению, мы указывать кодировку в обработчике, устанвленном @see set_exception_handler(),
	 * потому что set_exception_handler в Magento работать не будет
	 * из-за глобального try..catch в методе @see Mage::run()
	 */
	if (!headers_sent()) {
		header('Content-Type: text/html; charset=UTF-8');
	}
	if ($message instanceof Exception) {
		/** @var Exception $message */
		throw $message;
	}
	else {
		if (is_array($message)) {
			$message = implode("\r\n\r\n", $message);
		}
		else {
			/** @var string[] $arguments */
			$arguments = func_get_args();
			$message = rm_sprintf($arguments);
		}
		throw new Df_Core_Exception_Internal($message);
	}
}

/** @return bool */
function df_installed() {
	/** @var bool $result */
	static $result;
	if (!isset($result)) {
		/** @var string $timezone */
		$timezone = date_default_timezone_get();
		$result = Mage::isInstalled();
		date_default_timezone_set($timezone);
	}
		return $result;
}

/**
 * @param array $paramValue
 * @param int $paramOrdering
 * @param int $stackLevel[optional]
 * @return void
 * @throws Exception
 */
function df_param_array($paramValue, $paramOrdering, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Df_Qa_Method::assertParamIsArray($paramValue, $paramOrdering, $stackLevel + 1);
	}
}


/**
 * @param int|float  $resultValue
 * @param int $paramOrdering
 * @param int|float $min[optional]
 * @param int|float $max [optional]
 * @param int $stackLevel[optional]
 * @return void
 * @throws Exception
 */
function df_param_between($resultValue, $paramOrdering, $min = null, $max = null, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Df_Qa_Method::assertParamIsBetween(
			$resultValue, $paramOrdering, $min, $max, $stackLevel + 1
		);
	}
}

/**
 * @param bool $paramValue
 * @param int $paramOrdering
 * @param int $stackLevel[optional]
 * @return void
 * @throws Exception
 */
function df_param_boolean($paramValue, $paramOrdering, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Df_Qa_Method::assertParamIsBoolean($paramValue, $paramOrdering, $stackLevel + 1);
	}
}

/**
 * @param float $paramValue
 * @param float $paramOrdering
 * @param int $stackLevel[optional]
 * @return void
 * @throws Exception
 */
function df_param_float($paramValue, $paramOrdering, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Df_Qa_Method::assertParamIsFloat($paramValue, $paramOrdering, $stackLevel + 1);
	}
}

/**
 * @param int $paramValue
 * @param int $paramOrdering
 * @param int $stackLevel[optional]
 * @return void
 * @throws Exception
 */
function df_param_integer($paramValue, $paramOrdering, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Df_Qa_Method::assertParamIsInteger($paramValue, $paramOrdering, $stackLevel + 1);
	}
}

/**
 * @param string $paramValue
 * @param int $paramOrdering
 * @param int $stackLevel [optional]
 * @return void
 * @throws Exception
 */
function df_param_string($paramValue, $paramOrdering, $stackLevel = 0) {
	if (df_enable_assertions()) {
		/**
		 * Раньше тут стояло:
		 * $method->assertParamIsString($paramValue, $paramOrdering, $stackLevel + 1)
		 */
		/**
		 * 2015-02-16
		 * Раньше здесь стояло просто !is_string($value)
		 * Однако интерпретатор PHP способен неявно и вполне однозначно
		 * (без двусмысленностей, как, скажем, с вещественными числами)
		 * конвертировать целые числа и null в строки,
		 * поэтому пусть целые числа и null всегда проходят валидацию как строки.
		 */
		if (!(is_string($paramValue) || is_int($paramValue) || is_null($paramValue))) {
			Df_Qa_Method::raiseErrorParam(
				$validatorClass = __FUNCTION__
				,$messages =
					array(
						rm_sprintf(
							'Требуется строка, но вместо неё получена переменная типа «%s».'
							,gettype($paramValue)
						)
					)
				,$paramOrdering
				,$stackLevel + 1
			);
		}
	}
}

/**
 * @param string $paramValue
 * @param int $paramOrdering
 * @param int $stackLevel [optional]
 * @return void
 * @throws Exception
 */
function df_param_string_not_empty($paramValue, $paramOrdering, $stackLevel = 0) {
	df_param_string($paramValue, $paramOrdering, $stackLevel + 1);
	if (df_enable_assertions()) {
		/**
		 * Раньше тут стояло:
		 * $method->assertParamIsString($paramValue, $paramOrdering, $stackLevel + 1)
		 *
		 * При второй попытке тут стояло if (!$paramValue), что тоже неправильно,
		 * ибо непустая строка '0' не проходит такую валидацию.
		 */
		if ('' === strval($paramValue)) {
			Df_Qa_Method::raiseErrorParam(
				$validatorClass = __FUNCTION__
				,$messages = array('Требуется непустая строка, но вместо неё получена пустая.')
				,$paramOrdering
				,$stackLevel + 1
			);
		}
	}
}

/**
 * @param array $resultValue
 * @param int $stackLevel[optional]
 * @return void
 * @throws Exception
 */
function df_result_array($resultValue, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Df_Qa_Method::assertResultIsArray($resultValue, $stackLevel + 1);
	}
}

/**
 * @param bool $resultValue
 * @param int $stackLevel[optional]
 * @return void
 * @throws Exception
 */
function df_result_boolean($resultValue, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Df_Qa_Method::assertResultIsBoolean($resultValue, $stackLevel + 1);
	}
}

/**
 * @param float $resultValue
 * @param int $stackLevel[optional]
 * @return void
 * @throws Exception
 */
function df_result_float($resultValue, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Df_Qa_Method::assertResultIsFloat($resultValue, $stackLevel + 1);
	}
}

/**
 * @param int $resultValue
 * @param int $stackLevel[optional]
 * @return void
 * @throws Exception
 */
function df_result_integer($resultValue, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Df_Qa_Method::assertResultIsInteger($resultValue, $stackLevel + 1);
	}
}

/**
 * @param string $resultValue
 * @param int $stackLevel [optional]
 * @return void
 * @throws Exception
 */
function df_result_string($resultValue, $stackLevel = 0) {
	if (df_enable_assertions()) {
		// Раньше тут стояло:
		// Df_Qa_Method::assertResultIsString($resultValue, $stackLevel + 1)
		if (!is_string($resultValue)) {
			Df_Qa_Method::raiseErrorResult(
				$validatorClass = __FUNCTION__
				,$messages = array(rm_sprintf(
					'Требуется строка, но вместо неё получена переменная типа «%s».'
					, gettype($resultValue)
				))
				,$stackLevel + 1
			);
		}
	}
}

/**
 * @param string $resultValue
 * @param int $stackLevel [optional]
 * @return void
 * @throws Exception
 */
function df_result_string_not_empty($resultValue, $stackLevel = 0) {
	df_result_string($resultValue, $stackLevel + 1);
	if (df_enable_assertions()) {
		/**
		 * Раньше тут стояло:
		 * Df_Qa_Method::assertResultIsString($resultValue, $stackLevel + 1)
		 *
		 * При второй попытке тут стояло if (!$resultValue), что тоже неправильно,
		 * ибо непустая строка '0' не проходит такую валидацию.
		 */
		if ('' === strval($resultValue)) {
			Df_Qa_Method::raiseErrorResult(
				$validatorClass = __FUNCTION__
				,$messages = array('Требуется непустая строка, но вместо неё получена пустая.')
				,$stackLevel + 1
			);
		}
	}
}

/**
 * @param int|float $resultValue
 * @param int|float $min[optional]
 * @param int|float $max[optional]
 * @param int $stackLevel[optional]
 * @return void
 * @throws Exception
 */
function df_result_between($resultValue, $min = null, $max = null, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Df_Qa_Method::assertResultIsBetween($resultValue, $min, $max, $stackLevel + 1);
	}
}

/**
 * @param string $method
 * @return void
 * @throws Exception
 */
function df_should_not_be_here($method) {df_error("Метод «{$method}» запрещён для вызова.");}

/**
 * @param string|string[]|Exception|null $message[optional]
 * @return void
 */
function df_warning($message = null) {
	if ($message instanceof Exception) {
		$message = rm_ets($message);
	}
	else {
		if (is_array($message)) {
			$message = implode("\r\n\r\n", $message);
		}
		else {
			/** @var mixed[] $arguments */
			$arguments = func_get_args();
			$message = rm_sprintf($arguments);
		}
	}
	df_notify_admin($message, $doLog = true);
	df_notify_me($message, $doLog = false);
	if (df_is_admin()) {
		rm_session()->addWarning($message);
	}
}

/**
 * @param mixed $value
 * @return int
 * @throws Df_Core_Exception_Internal
 */
function rm_01($value) {
	/** @var int $result */
	$result = rm_int($value);
	df_assert_in($result, array(0, 1));
	return $result;
}

/**
 * @param mixed $value
 * @return bool
 */
function rm_bool($value) {
	/**
	 * Хотелось бы ради оптимизации использовать
	 * @see array_flip + @see isset вместо @see in_array,
	 * однако прямой вызов в лоб @see array_flip приводит к предупреждению:
	 * «Warning: array_flip(): Can only flip STRING and INTEGER values!».
	 * Более того, следующий тест не проходит:
		$a = array(null => 3, 0 => 4, false => 5);
		$this->assertNotEquals($a[0], $a[false]);
	 * Хотя эти тесты проходят:
	 * $this->assertNotEquals($a[null], $a[0]);
	 * $this->assertNotEquals($a[null], $a[false]);
	 */
	/** @var mixed[] $allowedValuesForNo */
	static $allowedVariantsForNo = array(0, '0', 'false', false, null, 'нет', 'no', 'off', '');
	/** @var mixed[] $allowedVariantsForYes */
	static $allowedVariantsForYes = array(1, '1', 'true', true, 'да', 'yes', 'on');
	/**
	 * Обратите внимание, что здесь использование $strict = true
	 * для функции @see in_array обязательно,
	 * иначе любое значение, приводимое к true (например, любая непустая строка),
	 * будет удовлетворять условию.
	 */
	/** @var bool $result */
	if (in_array($value, $allowedVariantsForNo, $strict = true)) {
		$result = false;
	}
	else if (in_array($value, $allowedVariantsForYes, $strict = true)) {
		$result = true;
	}
	else {
		df_error('Система не может распознать «%s» как значение логического типа.', $value);
	}
	return $result;
}

/**
 * @param Exception|Df_Core_Exception $exception
 * @return void
 */
function rm_exception_to_session(Exception $exception) {
	/** @var string $message */
	$message = df_text()->nl2br(Df_Core_Model_Output_Xml::s()->outputHtml(rm_ets($exception)));
	if (
			!($exception instanceof Mage_Core_Exception)
		||
			($exception instanceof Df_Core_Exception_Internal)
	) {
		rm_session()->addError($message);
		rm_session()->addError(nl2br(df_exception_get_trace($exception)));
		df_notify_exception($exception);
	}
	else {
		/** @var Mage_Core_Exception $exception */
		if ($message) {
			rm_session()->addError($message);
		}
		else if (0 === count($exception->getMessages())) {
			// Надо хоть какое-то сообщение показать
			rm_session()->addError(nl2br(df_exception_get_trace($exception)));
		}
		else {
			foreach ($exception->getMessages() as $subMessage) {
				/** @var Mage_Core_Model_Message_Abstract $subMessage */
				rm_session()->addError($subMessage->getText());
			}
		}
	}
}

/**
 * @param mixed $value
 * @param bool $allowNull [optional]
 * @return float
 * @throws Df_Core_Exception_Internal
 */
function rm_float($value, $allowNull = true) {
	/** @var float $result */
	if (is_float($value)) {
		$result = $value;
	}
	else if (is_int($value)) {
		$result = floatval($value);
	}
	else if ($allowNull && (is_null($value) || ('' === $value))) {
		$result = 0.0;
	}
	else {
		/** @var bool $valueIsString */
		$valueIsString = is_string($value);
		static $cache = array();
		/** @var array(string => float) $cache */
		if ($valueIsString && isset($cache[$value])) {
			$result = $cache[$value];
		}
		else {
			if (!Df_Zf_Validate_String_Float::s()->isValid($value)) {
				/**
				 * Обратите внимание, что мы намеренно используем @see df_error_internal(),
				 * а не @see df_error().
				 * Например, модуль доставки «Деловые Линии»
				 * не оповещает разработчика только об исключительных ситуациях
				 * класса @see Df_Core_Exception_Client,
				 * которые порождаются функцией @see df_error().
				 * О сбоях преобразования типов надо оповещать разработчика.
				 */
				df_error_internal(Df_Zf_Validate_String_Float::s()->getMessage());
			}
			else {
				df_assert($valueIsString);
				/**
				 * Хотя @see Zend_Validate_Float вполне допускает строки в формате «60,15»
				 * при установке надлежащей локали (например, ru_RU),
				 * @see floatval для строки «60,15» вернёт значение «60», обрубив дробную часть.
				 * Поэтому заменяем десятичный разделитель на точку.
				 */
				// Обратите внимание, что 368.0 === floatval('368.')
				$result = floatval(str_replace(',', '.', $value));
				$cache[$value] = $result;
			}
		}
	}
	return $result;
}

/**
 * @param mixed $value
 * @param bool $allow0 [optional]
 * @param bool $throw [optional]
 * @return float|null
 * @throws Df_Core_Exception
 */
function rm_float_positive($value, $allow0 = false, $throw = true) {
	/** @var float|null $result */
	if (!$throw) {
		try {
			$result = rm_float_positive($value, $allow0, true);
		}
		catch (Exception $e) {
			$result = null;
		}
	}
	else {
		/** @var float $result */
		$result = rm_float($value, $allow0);
		if ($allow0) {
			df_assert_ge(0.0, $result);
		}
		else {
			df_assert_gt0($result);
		}
	}
	return $result;
}

/**
 * @param mixed|mixed[] $value
 * @param bool $allowNull [optional]
 * @return int|int[]
 * @throws Df_Core_Exception_Internal
 */
function rm_int($value, $allowNull = true) {
	/** @var int|int[] $result */
	if (is_array($value)) {
		$result = df_map('rm_int', $value, $allowNull);
	}
	else {
		if (is_int($value)) {
			$result = $value;
		}
		else if (is_bool($value)) {
			$result = $value ? 1 : 0;
		}
		else {
			if ($allowNull && (is_null($value) || ('' === $value))) {
				$result = 0;
			}
			else {
				if (!Df_Zf_Validate_String_Int::s()->isValid($value)) {
					/**
					 * Обратите внимание, что мы намеренно используем @see df_error_internal(),
					 * а не @see df_error().
					 * Например, модуль доставки «Деловые Линии»
					 * не оповещает разработчика только об исключительных ситуациях
					 * класса @see Df_Core_Exception_Client,
					 * которые порождаются функцией @see df_error().
					 * О сбоях преобразования типов надо оповещать разработчика.
					 */
					df_error_internal(Df_Zf_Validate_String_Int::s()->getMessage());
				}
				else {
					$result = intval($value);
				}
			}
		}
	}
	return $result;
}

/**
 * 2015-04-13
 * В отличие от @see rm_int() функция rm_int_simple():
 * 1) намеренно не проводит валидацию данных ради ускорения
 * 2) работает только с массивами
 * Ключи массива сохраняются: http://3v4l.org/NHgdK
 * @used-by rm_fetch_col_int()
 * @used-by rm_products_update()
 * @used-by Df_Catalog_Model_Product_Exporter::applyRule()
 * @used-by Df_Shipping_Rate_Request::getQty()
 * @param mixed[] $values
 * @return int[]
 */
function rm_int_simple(array $values) {return array_map('intval', $values);}

/**
 * @param mixed $value
 * @param bool $allow0 [optional]
 * @return int
 * @throws Df_Core_Exception_Internal
 */
function rm_nat($value, $allow0 = false) {
	/** @var int $result */
	$result = rm_int($value, $allow0);
	if ($allow0) {
		df_assert_gt(-1, $result);
	}
	else {
		df_assert_gt0($result);
	}
	return $result;
}

/**
 * @param mixed $value
 * @return int
 * @throws Df_Core_Exception_Internal
 */
function rm_nat0($value) {return rm_nat($value, $allow0 = true);}