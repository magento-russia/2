<?php
if (!defined ('PHP_INT_MIN')) {
	define('PHP_INT_MIN', ~PHP_INT_MAX);
}

define('RM_F_TRIM', 'filter-trim');
define('DF_V_ARRAY', 'array');
define('DF_V_BOOL', 'boolean');
define('DF_V_FLOAT', 'float');
define('DF_V_INT', 'int');
/**
 * 2-буквенный код страны по стандарту ISO 3166-1 alpha-2.
 * https://ru.wikipedia.org/wiki/ISO_3166-1
 */
define('DF_V_ISO2', 'iso2');
define('DF_V_NAT', 'nat');
define('DF_V_NAT0', 'nat0');
define('DF_V_STRING_NE', 'string_ne');
define('DF_V_STRING', 'string');

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
 * @param string|Mage_Core_Exception $message [optional]
 * @return void
 * @throws Mage_Core_Exception
 */
function df_assert($condition, $message = null) {
	if (df_enable_assertions()) {
		if (!$condition) {
			df_error($message);
		}
	}
}

/**
 * @param array $value
 * @param int $stackLevel [optional]
 * @return void
 * @throws Exception
 */
function df_assert_array($value, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Df_Qa_Method::assertValueIsArray($value, $stackLevel + 1);
	}
}

/**
 * @param int|float $value
 * @param int|float $min [optional]
 * @param int|float $max [optional]
 * @param int $stackLevel [optional]
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
 * @param int $stackLevel [optional]
 * @return void
 * @throws Exception
 */
function df_assert_boolean($value, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Df_Qa_Method::assertValueIsBoolean($value, $stackLevel + 1);
	}
}

/**
 * 2016-08-09
 * @used-by df_map_k()
 * @param mixed $v
 * @param string|\Exception $message [optional]
 * @return void
 * @throws Exception
 */
function df_assert_callable($v, $message = null) {
	if (df_enable_assertions()) {
		if (!is_callable($v)) {
			df_error($message ?:
				"A variable is expected to be a callable, "
				. "but actually it is a «%s».", gettype($v)
			);
		}
	}
}

/**
 * @param object $value
 * @param string $class
 * @param int $stackLevel [optional]
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
 * @param string|Mage_Core_Exception $message [optional]
 * @return void
 * @throws Mage_Core_Exception
 */
function df_assert_eq($expectedResult, $valueToTest, $message = null) {
	if (df_enable_assertions()) {
		if ($expectedResult !== $valueToTest) {
			df_error($message ? $message : df_sprintf(
				'Проверяющий ожидал значение «%s», однако получил значение «%s».'
				, $expectedResult
				, $valueToTest
			));
		}
	}
}

/**
 * @param float $value
 * @param int $stackLevel [optional]
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
 * @param string|Mage_Core_Exception $message [optional]
 * @return void
 * @throws Mage_Core_Exception
 */
function df_assert_ge($lowBound, $valueToTest, $message = null) {
	if (df_enable_assertions()) {
		if ($lowBound > $valueToTest) {
			df_error($message ? $message : df_sprintf(
				'Проверяющий ожидал значение не меньше «%s», однако получил значение «%s».'
				, $lowBound
				, $valueToTest
			));
		}
	}
}

/**
 * @param int|float $lowBound
 * @param int|float $valueToTest
 * @param string|Mage_Core_Exception $message [optional]
 * @return void
 * @throws Mage_Core_Exception
 */
function df_assert_gt($lowBound, $valueToTest, $message = null) {
	if (df_enable_assertions()) {
		if ($lowBound >= $valueToTest) {
			df_error($message ? $message : df_sprintf(
				'Проверяющий ожидал значение больше «%s», однако получил значение «%s».'
				, $lowBound
				, $valueToTest
			));
		}
	}
}

/**
 * @param int|float $valueToTest
 * @param string|Mage_Core_Exception $message [optional]
 * @return void
 * @throws Mage_Core_Exception
 */
function df_assert_gt0($valueToTest, $message = null) {
	if (df_enable_assertions()) {
		if (0 >= $valueToTest) {
			df_error($message ? $message : df_sprintf(
				'Проверяющий ожидал положительное значение, однако получил «%s».', $valueToTest
			));
		}
	}
}

/**
 * @param int|float $valueToTest
 * @param mixed[] $allowedResults
 * @param string|Mage_Core_Exception $message [optional]
 * @return void
 * @throws Mage_Core_Exception
 */
function df_assert_in($valueToTest, array $allowedResults, $message = null) {
	if (df_enable_assertions()) {
		if (!in_array($valueToTest, $allowedResults, $strict = true)) {
			df_error($message ? $message : (
				10 >= count($allowedResults)
				? df_sprintf(
					'Проверяющий ожидал значение из множества «%s», однако получил значение «%s».'
					, df_csv_pretty($allowedResults)
					, $valueToTest
				)
				: df_sprintf(
					'Проверяющий получил значение «%s», отсутствующее в допустимом множестве значений.'
					, $valueToTest
				)
			));
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
 * @param string $value
 * @param int $stackLevel [optional]
 * @return void
 * @throws Exception
 */
function df_assert_iso2($value, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Df_Qa_Method::assertValueIsIso2($value, $stackLevel + 1);
	}
}

/**
 * @param int|float $highBound
 * @param int|float $valueToTest
 * @param string|Mage_Core_Exception $message [optional]
 * @return void
 * @throws Mage_Core_Exception
 */
function df_assert_le($highBound, $valueToTest, $message = null) {
	if (df_enable_assertions()) {
		if ($highBound < $valueToTest) {
			df_error($message ? $message : df_sprintf(
				'Проверяющий ожидал значение не больше «%s», однако получил значение «%s».'
				, $highBound
				, $valueToTest
			));
		}
	}
}

/**
 * @param int|float $highBound
 * @param int|float $valueToTest
 * @param string|Mage_Core_Exception $message [optional]
 * @return void
 * @throws Mage_Core_Exception
 */
function df_assert_lt($highBound, $valueToTest, $message = null) {
	if (df_enable_assertions()) {
		if ($highBound <= $valueToTest) {
			df_error($message ? $message : df_sprintf(
				'Проверяющий ожидал значение меньше «%s», однако получил значение «%s».'
				, $highBound
				, $valueToTest
			));
		}
	}
}

/**
 * @param string|int|float $notExpectedResult
 * @param string|int|float $valueToTest
 * @param string|Mage_Core_Exception $message [optional]
 * @return void
 * @throws Mage_Core_Exception
 */
function df_assert_ne($notExpectedResult, $valueToTest, $message = null) {
	if (df_enable_assertions()) {
		if ($notExpectedResult === $valueToTest) {
			df_error($message ? $message : df_sprintf(
				'Проверяющий ожидал значение, отличное от «%s», однако получил именно его.'
				, $notExpectedResult
			));
		}
	}
}

/**
 * @param string $value
 * @param int $stackLevel [optional]
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
 * @param int $stackLevel [optional]
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
 * 2016-08-09
 * @param mixed $v
 * @param string|\Exception $message [optional]
 * @return void
 * @throws Exception
 */
function df_assert_traversable($v, $message = null) {
	if (df_enable_assertions()) {
		if (!df_check_traversable($v)) {
			df_error($message ?:
				"A variable is expected to be a traversable or an array, "
				. "but actually it is a «%s».", gettype($v)
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
 * @param int|float $min [optional]
 * @param int|float $max [optional]
 * @return bool
 */
function df_check_between($value, $min = null, $max = null) {
	/** @var Df_Zf_Validate_Between $validator */
	$validator = new Df_Zf_Validate_Between(array(
		'min' => is_null($min) ? PHP_INT_MIN : $min
		,'max' => is_null($max) ? PHP_INT_MAX : $max
		,'inclusive' => true
	));
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
	 * http://ru2.php.net/manual/en/function.is-int.php#35820
	 */
	return is_numeric($value) && ($value == (int)$value);
}

/**
 * @param mixed $value
 * @return bool
 */
function df_check_iso2($value) {return Df_Zf_Validate_String_Iso2::s()->isValid($value);}

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

/**
 * 2016-08-09
 * @see df_assert_traversable()
 * @see df_check_array()
 * @used-by df_map_k()
 * http://stackoverflow.com/questions/31701517#comment59189177_31701556
 * @param mixed $v
 * @return bool
 */
function df_check_traversable($v) {return is_array($v) || $v instanceof \Traversable;}

/** @return bool */
function df_enable_assertions() {
	/** @var bool $result */
	static $result;
	if (is_null($result)) {
		/**
		 * Нельзя вызывать @uses Mage::getStoreConfig(),
		 * если текущий магазин ещё не инициализирован!
		 */
		if (Df_Core_State::s()->isStoreInitialized()) {
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
	return is_null($result) ? true : $result;
}

/**
 * @param string|string[]|mixed|Exception|null $message [optional]
 * @return void
 * @throws Exception
 */
function df_error($message = null) {
	/**
	 * К сожалению, мы не можем указывать кодировку в обработчике,
	 * установленном @see set_exception_handler(),
	 * потому что @see set_exception_handler() в Magento работать не будет
	 * из-за глобального try..catch в методе @see Mage::run()
	 *
	 * 2015-01-28
	 * По примеру @see df_handle_entry_point_exception()
	 * добавил условие @uses Mage::getIsDeveloperMode()
	 * потому что Magento выводит диагностические сообщения на экран
	 * только при соблюдении этого условия.
	 */
	if (!headers_sent() && Mage::getIsDeveloperMode()) {
		header('Content-Type: text/html; charset=UTF-8');
	}
	if ($message instanceof Exception) {
		/** @var Exception $message */
		throw $message;
	}
	else {
		if (is_array($message)) {
			$message = implode("\n\n", $message);
		}
		else {
			/** @var mixed[] $arguments */
			$arguments = func_get_args();
			$message = df_format($arguments);
		}
		throw new Df_Core_Exception($message);
	}
}

/** @return bool */
function df_installed() {
	/** @var bool $result */
	static $result;
	if (is_null($result)) {
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
 * @param int $stackLevel [optional]
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
 * @param int|float $min [optional]
 * @param int|float $max [optional]
 * @param int $stackLevel [optional]
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
 * @param int $stackLevel [optional]
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
 * @param int $stackLevel [optional]
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
 * @param int $stackLevel [optional]
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
function df_param_iso2($paramValue, $paramOrdering, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Df_Qa_Method::assertParamIsIso2($paramValue, $paramOrdering, $stackLevel + 1);
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
						df_sprintf(
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
 * @param int $stackLevel [optional]
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
 * @param int $stackLevel [optional]
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
 * @param int $stackLevel [optional]
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
 * @param int $stackLevel [optional]
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
function df_result_iso2($resultValue, $stackLevel = 0) {
	if (df_enable_assertions()) {
		Df_Qa_Method::assertResultIsIso2($resultValue, $stackLevel + 1);
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
				,$messages = array(df_sprintf(
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
 * @param int|float $min [optional]
 * @param int|float $max [optional]
 * @param int $stackLevel [optional]
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
 * @param string|string[]|Exception|null $message [optional]
 * @return void
 */
function df_warning($message = null) {
	if ($message instanceof Exception) {
		$message = df_ets($message);
	}
	else {
		if (is_array($message)) {
			$message = implode("\n\n", $message);
		}
		else {
			/** @var mixed[] $arguments */
			$arguments = func_get_args();
			$message = df_format($arguments);
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
 * @throws Df_Core_Exception
 */
function rm_01($value) {
	/** @var int $result */
	$result = df_int($value);
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
	 * @see array_flip() + @see isset() вместо @uses in_array(),
	 * однако прямой вызов в лоб @see array_flip() приводит к предупреждению:
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
	 * для функции @uses in_array() обязательно,
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
 * @param Exception|Mage_Core_Exception|Df_Core_Exception $exception
 * @return void
 */
function rm_exception_to_session(Exception $exception) {
	/** @var string $message */
	$message = df_t()->nl2br(rm_xml_output_html(df_ets($exception)));
	/** @var bool $isMagentoCoreException */
	$isMagentoCoreException = $exception instanceof Mage_Core_Exception;
	/** @var bool $isRmException */
	$isRmException = $exception instanceof Df_Core_Exception;
	/** @var bool $needNotifyDeveloper */
	$needNotifyDeveloper = $isRmException && $exception->needNotifyDeveloper();
	/** @var bool $needShowStackTrace */
	$needShowStackTrace = $needNotifyDeveloper && (df_is_admin() || df_is_it_my_local_pc());
	if ($message) {
		rm_session()->addError($message);
	}
	else if ($isMagentoCoreException && $exception->getMessages()) {
		foreach ($exception->getMessages() as $subMessage) {
			/** @var Mage_Core_Model_Message_Abstract $subMessage */
			rm_session()->addError($subMessage->getText());
		}
	}
	else if (!$needShowStackTrace) {
		// Надо хоть какое-то сообщение показать
		rm_session()->addError('Произошёл внутренний сбой.');
	}
	if ($needShowStackTrace) {
		rm_session()->addError(df_t()->nl2br(df_exception_get_trace($exception)));
	}
	if ($needNotifyDeveloper) {
		df_notify_exception($exception);
	}
}

/**
 * @param mixed $value
 * @param bool $allowNull [optional]
 * @return float
 * @throws Df_Core_Exception
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
				 * Обратите внимание, что мы намеренно используем @uses df_error(),
				 * а не @see df_error().
				 * Например, модуль доставки «Деловые Линии»
				 * не оповещает разработчика только об исключительных ситуациях
				 * класса @see Df_Core_Exception,
				 * которые порождаются функцией @see df_error().
				 * О сбоях преобразования типов надо оповещать разработчика.
				 */
				df_error(Df_Zf_Validate_String_Float::s()->getMessage());
			}
			else {
				df_assert($valueIsString);
				/**
				 * Хотя @see Zend_Validate_Float вполне допускает строки в формате «60,15»
				 * при установке надлежащей локали (например, ru_RU),
				 * @uses floatval для строки «60,15» вернёт значение «60», обрубив дробную часть.
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
 * @param mixed $value
 * @return float
 * @throws Df_Core_Exception
 */
function rm_float_positive0($value) {return rm_float_positive($value, $allow0 = true);}

/**
 * @param mixed|mixed[] $value
 * @param bool $allowNull [optional]
 * @return int|int[]
 * @throws Df_Core_Exception
 */
function df_int($value, $allowNull = true) {
	/** @var int|int[] $result */
	if (is_array($value)) {
		$result = df_map(__FUNCTION__, $value, $allowNull);
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
					 * Обратите внимание, что мы намеренно используем @uses df_error(),
					 * а не @see df_error().
					 * Например, модуль доставки «Деловые Линии»
					 * не оповещает разработчика только об исключительных ситуациях
					 * класса @see Df_Core_Exception,
					 * которые порождаются функцией @see df_error().
					 * О сбоях преобразования типов надо оповещать разработчика.
					 */
					df_error(Df_Zf_Validate_String_Int::s()->getMessage());
				}
				else {
					$result = (int)$value;
				}
			}
		}
	}
	return $result;
}

/**
 * 2015-04-13
 * В отличие от @see df_int() функция df_int_simple():
 * 1) намеренно не проводит валидацию данных ради ускорения
 * 2) работает только с массивами
 * Ключи массива сохраняются: http://3v4l.org/NHgdK
 * @used-by df_fetch_col_int()
 * @used-by rm_products_update()
 * @used-by Df_Catalog_Model_Product_Exporter::applyRule()
 * @used-by Df_Shipping_Rate_Request::getQty()
 * @param mixed[] $values
 * @return int[]
 */
function df_int_simple(array $values) {return array_map('intval', $values);}

/**
 * 2015-03-04
 * Эта функция проверяет, принадлежит ли переменная $variable хотя бы к одному из классов $class.
 * Обратите внимание, что т.к. алгоритм функции использует стандартный оператор instanceof,
 * то переменная $variable может быть не только объектом,
 * а иметь произвольный тип: http://php.net/manual/language.operators.type.php#example-146
 * Если $variable не является объектом, то функция просто вернёт false.
 *
 * Наша функция не загружает при этом $class в память интерпретатора PHP.
 * Если $class ещё не загружен в память интерпретатора PHP, то функция вернёт false.
 * В принципе, это весьма логично!
 * Если проверяемый класс ещё не был загружен в память интерпретатора PHP,
 * то проверяемая переменная $variable гарантированно не может принадлежать данному классу!
 * http://3v4l.org/KguI5
 * Наша функция отличается по сфере применения
 * как от оператора instanceof, так и от функции @see is_a() тем, что:
 * 1) Умеет проводить проверку на приналежность не только одному конкретному классу,
 * а и хотя бы одному из нескольких.
 * 2) @is_a() приводит к предупреждению уровня E_DEPRECATED интерпретатора PHP версий ниже 5.3:
 * http://php.net/manual/function.is-a.php
 * 3) Даже при проверке на принаджежность одному классу код с @see rm_is() получается короче,
 * чем при применении instanceof в том случае, когда мы не уверены, существует ли класс
 * и загружен ли уже класс интерпретатором PHP.
 * Например, нам приходилось писать так:
		class_exists('Df_1C_Cml2Controller', $autoload = false)
	&&
		rm_state()->getController() instanceof Df_1C_Cml2Controller
 * Или так:
		$controllerClass = 'Df_1C_Cml2Controller';
		$result = rm_state()->getController() instanceof $controllerClass;
 * При этом нельзя писать
		rm_state()->getController() instanceof 'Df_1C_Cml2Controller'
 * потому что правый операнд instanceof может быть строковой переменной,
 * но не может быть просто строкой!
 * http://php.net/manual/en/language.operators.type.php#example-148
 * @param mixed $variable
 * @param string|string[] $class
 * @return bool
 * @used-by Df_1C_Observer::df_catalog__attribute_set__group_added()
 */
function rm_is($variable, $class) {
	/** @var bool $result */
	if (2 < func_num_args()) {
		/** @var mixed[] $arguments */
		$arguments = func_get_args();
		/** @var string[] $classes */
		$class = df_tail($arguments);
	}
	if (!is_array($class)) {
		$result = $variable instanceof $class;
	}
	else {
		$result = false;
		foreach ($class as $classItem) {
			/** @var string $classItem */
			if ($variable instanceof $classItem) {
				$result = true;
				break;
			}
		}
	}
	return $result;
}

/**
 * @param mixed $value
 * @param bool $allow0 [optional]
 * @return int
 * @throws Df_Core_Exception
 */
function df_nat($value, $allow0 = false) {
	/** @var int $result */
	$result = df_int($value, $allow0);
	if ($allow0) {
		df_assert_ge(0, $result);
	}
	else {
		df_assert_gt0($result);
	}
	return $result;
}

/**
 * @param mixed $value
 * @return int
 * @throws Df_Core_Exception
 */
function df_nat0($value) {return df_nat($value, $allow0 = true);}

/**
 * Эта функция используется, как правило, при отключенном режиме разработчика.
 * @see mageCoreErrorHandler():
		if (Mage::getIsDeveloperMode()) {
			throw new Exception($errorMessage);
		}
 		else {
			Mage::log($errorMessage, Zend_Log::ERR);
		}
 * @param bool $isOperationSuccessfull [optional]
 * @throws Df_Core_Exception
 */
function rm_throw_last_error($isOperationSuccessfull = false) {
	if (!$isOperationSuccessfull) {
		Df_Qa_Message_Failure_Error::throwLast();
	}
}