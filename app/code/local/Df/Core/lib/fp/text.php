<?php
/**
 * Эта функция отличается от @see implode() тем,
 * что способна принимать переменное количество аргументов, например:
 * df_concat('aaa', 'bbb', 'ccc') вместо implode(array('aaa', 'bbb', 'ccc')).
 * То есть, эта функция даёт только сокращение синтаксиса.
 * @param string[]|mixed[] $arguments
 * @return string
 */
function df_concat($arguments) {
	// Обратите внимание, что функция func_get_args() не может быть параметром другой функции.
	$arguments = is_array($arguments) ? $arguments : func_get_args();
	/**
	 * @see implode() способна работать с одним аргументом,
	 * и тогда параметр $glue считается равным пустой строке.
	 * @link http://www.php.net//manual/function.implode.php
	 */
	return implode($arguments);
}

/**
 * @param string[]|mixed[] $arguments
 * @return string
 */
function df_concat_enum($arguments) {
	// Обратите внимание, что функция func_get_args() не может быть параметром другой функции.
	$arguments = is_array($arguments) ? $arguments : func_get_args();
	return implode(', ', $arguments);
}

/**
 * @param string[]|mixed[] $arguments
 * @return string
 */
function df_concat_path($arguments) {
	// Обратите внимание, что функция func_get_args() не может быть параметром другой функции.
	$arguments = is_array($arguments) ? $arguments : func_get_args();
	return implode(DS, $arguments);
}

/**
 * @param string[] $arguments
 * @return string
 */
function df_concat_url($arguments) {
	// Обратите внимание, что функция func_get_args() не может быть параметром другой функции.
	$arguments = is_array($arguments) ? $arguments : func_get_args();
	return implode('/', $arguments);
}

/**
 * @param string[] $arguments
 * @return string
 */
function df_concat_xpath($arguments) {
	// Обратите внимание, что функция func_get_args() не может быть параметром другой функции.
	$arguments = is_array($arguments) ? $arguments : func_get_args();
	return implode(Df_Core_Const::T_XPATH_SEPARATOR, $arguments);
}

/**
 * @param string $text
 * @return string
 */
function df_escape($text) {return df_text()->htmlspecialchars($text);}

/**
 * @param mixed|false $value
 * @return mixed|null
 */
function df_ftn($value) {return (false === $value) ? null : $value;}

/**
 * @param string $text
 * @return string
 */
function df_no_escape($text) {
	return df_text()->noEscape($text);
}

/**
 * @param string $string
 * @return string
 */
function df_lcfirst($string) {
	/** @var string $result */
	$result =
		(string)
			(
					mb_strtolower(
						mb_substr($string,0,1)
					)
				.
					mb_substr($string,1)
			)
	;
	return $result;
}

/**
 * @param mixed|null $value
 * @return mixed
 */
function df_nts($value) {return !is_null($value) ? $value : '';}

/**
 * @param string[]|mixed[] $arguments
 * @return string
 */
function df_quote_and_concat($arguments) {
	// Обратите внимание, что функция func_get_args() не может быть параметром другой функции.
	$arguments = is_array($arguments) ? $arguments : func_get_args();
	return df_concat_enum(df_quote_russian($arguments));
}

/**
 * @param string $string
 * @param string $delimiter[optional]
 * @return array
 */
function df_parse_csv($string, $delimiter = ',') {return df_output()->parseCsv($string, $delimiter);}

/**
 * @param string|string[] $text
 * @return string|string[]
 */
function df_quote_duoble($text) {return df_text()->quote($text, Df_Core_Helper_Text::QUOTE__DOUBLE);}

/**
 * @param string|string[] $text
 * @return string|string[]
 */
function df_quote_russian($text) {return df_text()->quote($text, Df_Core_Helper_Text::QUOTE__RUSSIAN);}

/**
 * @param string|string[] $text
 * @return string|string[]
 */
function df_quote_single($text) {return df_text()->quote($text, Df_Core_Helper_Text::QUOTE__SINGLE);}

/**
 * Иногда я для разработки использую заплатку ядра для xDebug —
 * отключаю set_error_handler для режима разработчика.
 *
 * Так вот, xDebug при обработке фатальных сбоев (в том числе и E_RECOVERABLE_ERROR),
 * выводит на экран диагностическое сообщение, и после этого останавливает работу интерпретатора.
 *
 * Конечно, если у нас сбой типов E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING,
 * E_COMPILE_ERROR, E_COMPILE_WARNING, то и set_error_handler не поможет
 * (не обрабатывает эти типы сбоев, согласно официальной документации PHP).
 *
 * Однако сбои типа E_RECOVERABLE_ERROR обработик сбоев Magento,
 * установленный посредством set_error_handler, переводит в исключительние ситуации.
 *
 * xDebug же при E_RECOVERABLE_ERROR останавивает работу интерпретатора, что нехорошо.
 *
 * Поэтому для функций, которые могут привести к E_RECOVERABLE_ERROR,
 * пишем обёртки, которые вместо E_RECOVERABLE_ERROR возбуждают исключительную ситуацию.
 * Одна из таких функций — df_string.
 *
 * @param mixed $value
 * @return string
 */
function df_string($value) {
	if (is_object($value)) {
		if (
			/**
			 * К сожалению, нельзя здесь для проверки публичности метода
			 * использовать is_callable,
			 * потому что наличие Varien_Object::__call
			 * приводит к тому, что is_callable всегда возвращает true.
			 */
			!method_exists($value, '__toString')
		) {
			df_error(
				'Программист ошибочно пытается трактовать объект класса %s как строку.'
				,get_class($value)
			);
		}
	}
	else if (is_array($value)) {
		df_error('Программист ошибочно пытается трактовать массив как строку.');
	}
	return strval($value);
}

/**
 * @param mixed $value
 * @return string
 */
function df_string_debug($value) {
	/** @var string $result */
	$result = '';
	if (is_object($value)) {
		if (
			/**
			 * К сожалению, нельзя здесь для проверки публичности метода
			 * использовать is_callable,
			 * потому что наличие Varien_Object::__call
			 * приводит к тому, что is_callable всегда возвращает true.
			 */
			!method_exists($value, '__toString')
		) {
			$result = get_class($value);
		}
	}
	else if (is_array($value)) {
		$result = rm_sprintf('<массив из %d элементов>', count($value));
	}
	else if (is_bool($value)) {
		$result = $value ? 'логическое <да>' : 'логическое <нет>';
	}
	else {
		$result = strval($value);
	}
	return $result;
}

/**
 * @param $string1
 * @param $string2
 * @return bool
 */
function df_strings_are_equal_ci($string1, $string2) {
	return
		(
				0
			===
				strcmp(
					mb_strtolower($string1)
					,mb_strtolower($string2)
				)
		)
	;
}

/**
 * @param string $text
 * @return string
 */
function df_tab($text) {return "\t" . $text;}

/**
 * @param string $text
 * @return string
 */
function df_tab_multiline($text) {return implode("\n", array_map('df_tab', explode("\n", $text)));}

/** @return Df_Core_Helper_Text */
function df_text() {return Df_Core_Helper_Text::s();}

/**
 * Обратите внимание, что иногда вместо данной функции надо применять trim.
 * Например, df_trim не умеет отсекать нулевые байты,
 * которые могут образовываться на конце строки
 * в результате шифрации, передачи по сети прямо в двоичном формате, и затем обратной дешифрации
 * посредством Varien_Crypt_Mcrypt.
 *
 * @see Df_Core_Model_RemoteControl_Coder::decode
 * @see Df_Core_Model_RemoteControl_Coder::encode
 *
 * @param string|string[] $string
 * @param string $charlist [optional]
 * @return string|string[]
 */
function df_trim($string, $charlist = null) {
	return
		is_array($string)
		? df_map(array(df_text(), 'trim'), $string, $charlist)
		: df_text()->trim($string, $charlist)
	;
}

/**
 * Отсекает у строки $haystack подстроку $needle,
 * если она встречается в начале или в конце строки $haystack
 * @param string $haystack
 * @param string $needle
 * @return string
 */
function df_trim_text($haystack, $needle) {
	return df_trim_text_left(df_trim_text_right($haystack, $needle), $needle);
}

/**
 * Отсекает у строки $haystack заданное начало $needle
 * @param string $haystack
 * @param string $needle
 * @return string
 */
function df_trim_text_left($haystack, $needle) {
	/** @var int $length */
	$length = mb_strlen($needle);
	/** @see rm_starts_with() */
	return
		($needle === mb_substr($haystack, 0, $length))
		? mb_substr($haystack, $length)
		: $haystack
	;
}

/**
 * Отсекает у строки $haystack заданное окончание $needle
 * @param string $haystack
 * @param string $needle
 * @return string
 */
function df_trim_text_right($haystack, $needle) {
	/** @var int $length */
	$length = mb_strlen($needle);
	/** @see rm_ends_with() */
	return
		(0 !== $length) && ($needle === mb_substr($haystack, -$length))
		? mb_substr($haystack, 0, -$length)
		: $haystack
	;
}

/**
 * @param string $string
 * @param string $charlist [optional]
 * @return string
 */
function df_trim_left($string, $charlist = null) {
	// Пусть пока будет так.
	// Потом, если потребуется, добавлю дополнительную обработку спецсимволов Unicode.
	return ltrim($string, $charlist);
}

/**
 * @param string $string
 * @param string $charlist [optional]
 * @return string
 */
function df_trim_right($string, $charlist = null) {
	// Пусть пока будет так.
	// Потом, если потребуется, добавлю дополнительную обработку спецсимволов Unicode.
	return rtrim($string, $charlist);
}

/**
 * @param string $string
 * @param string $suffix
 * @return string
 */
function df_trim_suffix($string, $suffix) {
	df_param_string($string, 0);
	df_param_string($suffix, 1);
	return preg_replace(rm_sprintf('#%s$#mui', preg_quote($suffix, '#')), '', $string);
}

/**
 * @param boolean $value
 * @return string
 */
function rm_bts($value) {return df_output()->convertBooleanToString($value);}

/**
 * @param boolean $value
 * @return string
 */
function rm_bts_r($value) {return df_output()->convertBooleanToStringRussian($value);}

/**
 * @param string $text
 * @return string
 */
function rm_cdata($text) {return Df_Varien_Simplexml_Element::markAsCData($text);}

/**
 * @param string[] $keyParts
 * @return string
 */
function rm_config_key($keyParts) {
	if (!is_array($keyParts)) {
		$keyParts = func_get_args();
	}
	return implode(Df_Core_Helper_Config::PATH_SEPARATOR, $keyParts);
}

/**
 * @param string $haystack
 * @param string $needle
 * @return bool
 * Я так понимаю, здесь безопысно использовать @see strpos вместо mb_strpos даже для UTF-8.
 * @link http://stackoverflow.com/questions/13913411/mb-strpos-vs-strpos-whats-the-difference
 */
function rm_contains($haystack, $needle) {return false !== strpos($haystack, $needle);}

/**
 * 2015-02-17
 * Не используем методы ядра
 * @see Mage_Core_Helper_Abstract::escapeHtml()
 * @see Mage_Core_Helper_Abstract::htmlEscape()
 * потому что они используют @uses htmlspecialchars() со вторым параметром @see ENT_COMPAT,
 * в результате чего одиночные кавычки не экранируются.
 * Ядро Magento не использует одиночные кавычки при формировании HTML
 * (в частности, в шаблонах *.phtml), поэтому, видимо, их устраивает режим ENT_COMPAT.
 * Российская сборка Magento использует при формировании HTML одиночные кавычки,
 * поэтому нам нужен режим ENT_QUOTES.
 * Это важно, например, в методе @used-by Df_Core_Model_Format_Html_Tag::getAttributeAsText()
 * @see rm_ejs()
 * @param string|string[]|int|null $text
 * @return string|string[]
 */
function rm_e($text) {
	return
		is_array($text)
		? array_map(__FUNCTION__, $text)
		: htmlspecialchars($text, ENT_QUOTES, 'UTF-8', $double_encode = false)
	;
}

/**
 * 2015-02-17
 * Экранирует строку для вставки её в код на JavaScript.
 * @uses json_encode() рекомендуют
 * как самый правильный способ вставки строки из PHP в JavaScript:
 * @link http://stackoverflow.com/a/169035
 * Заменяем символ одинарной ковычки его кодом Unicode,
 * чтобы результат метода можно было вставлять внутрь обрамленной одиночными кавычками строки,
 * например:
	var $name = '<?php echo rm_ejs($name); ?>';
 * @used-by rm_admin_button_location()
 * @used-by Df_Admin_Config_DynamicTable_Column::renderTemplate()
 * @used-by app/design/adminhtml/rm/default/template/df/admin/column/select.phtml
 * @used-by app/design/adminhtml/rm/default/template/df/admin/field/button.phtml
 * @used-by app/design/frontend/rm/default/template/df/checkout/onepage/shipping_method/available/js.phtml
 * @param string $text
 * @return string
 */
function rm_ejs($text) {return str_replace("'", '\u0027', df_trim(json_encode($text), '"'));}

/**
 * @param string $haystack
 * @param string $needle
 * @return bool
 * @link http://stackoverflow.com/a/10473026/254475
 * @link http://stackoverflow.com/a/834355/254475
 * @see rm_starts_with()
 */
function rm_ends_with($haystack, $needle) {
	/** @var int $length */
	$length = mb_strlen($needle);
	return (0 === $length) || ($needle === mb_substr($haystack, -$length));
}

/**
 * @used-by rm_flits()
 * @param float $value
 * @param int $precision [optional]
 * @return string
 */
function rm_flts($value, $precision = 2) {return number_format($value, $precision, '.', '');}

/**
 * @param int|float $value
 * @param int $precision [optional]
 * @return string
 */
function rm_flits($value, $precision = 2) {
	return is_int($value) ? (string)$value : rm_flts($value, $precision);
}

/**
 * @param string $text
 * @return string
 * @link http://darklaunch.com/2009/05/06/php-normalize-newlines-line-endings-crlf-cr-lf-unix-windows-mac
 */
function rm_normalize($text) {return strtr($text, array("\r\n" => "\n", "\r" => "\n"));}

/**
 * @param string $pattern
 * @param string $subject
 * @param bool $throwOnNotMatch [optional]
 * @return string|string[]|null|bool
 */
function rm_preg_match($pattern, $subject, $throwOnNotMatch = true) {
	return Df_Core_Model_Text_Regex::i(
		$pattern, $subject, $throwOnError = true, $throwOnNotMatch
	)->match();
}

/**
 * @param string $pattern
 * @param string $subject
 * @param bool $throwOnNotMatch [optional]
 * @return int|null|bool
 */
function rm_preg_match_int($pattern, $subject, $throwOnNotMatch = true) {
	return Df_Core_Model_Text_Regex::i(
		$pattern, $subject, $throwOnError = true, $throwOnNotMatch
	)->matchInt();
}

/**
 * @param string $pattern
 * @param string $subject
 * @param bool $throwOnError [optional]
 * @return bool
 * @throws Df_Core_Exception_Internal
 */
function rm_preg_test($pattern, $subject, $throwOnError = true) {
	return Df_Core_Model_Text_Regex::i(
		$pattern, $subject, $throwOnError, $throwOnNotMatch = false
	)->test();
}

/**
 * @param array(string => string) $params
 * @return string
 */
function rm_print_params(array $params) {return df_output()->printParams($params);}

/**
 * @param string|mixed[] $pattern
 * @return string
 * @throws Exception
 */
function rm_sprintf($pattern) {
	/** @var string $result */
	/** @var mixed[] $arguments */
	if (is_array($pattern)) {
		$arguments = $pattern;
		$pattern = rm_first($arguments);
	}
	else {
		$arguments = func_get_args();
	}
	try {
		$result = rm_sprintf_strict($arguments);
	}
	catch (Df_Core_Exception_Internal $e) {
		/** @var bool $inProcess */
		static $inProcess = false;
		if (!$inProcess) {
			$inProcess = true;
			df_notify_me(rm_ets($e));
			$inProcess = false;
		}
		$result = $pattern;
	}
	return $result;
}

/**
 * @param string|mixed[] $pattern
 * @return string
 * @throws Df_Core_Exception_Internal
 */
function rm_sprintf_strict($pattern) {
	/** @var mixed[] $arguments */
	if (is_array($pattern)) {
		$arguments = $pattern;
		$pattern = rm_first($arguments);
	}
	else {
		$arguments = func_get_args();
	}
	/** @var string $result */
	if (1 === count($arguments)) {
		$result = $pattern;
	}
	else {
		try {
			$result = vsprintf($pattern, rm_tail($arguments));
		}
		catch (Exception $e) {
			/** @var bool $inProcess */
			static $inProcess = false;
			if (!$inProcess) {
				$inProcess = true;
				df_error_internal(strtr(
					'При выполнении sprintf произошёл сбой «{message}».'
					. "\r\nШаблон: {pattern}."
					. "\r\nПараметры:\r\n{params}."
					,array(
						'{message}' => rm_ets($e)
						,'{pattern}' => $pattern
						,'{params}' => print_r(rm_tail($arguments), true)
					)
				));
				$inProcess = false;
			}
		}
	}
	return $result;
}

/**
 * @param string $haystack
 * @param string $needle
 * @return bool
 * @link http://stackoverflow.com/a/10473026/254475
 * @link http://stackoverflow.com/a/834355/254475
 * @see rm_ends_with()
 */
function rm_starts_with($haystack, $needle) {
	/**
	 * Утверждают, что код ниже работает быстрее, чем
	 * return 0 === mb_strpos($haystack, $needle);
	 * @link http://stackoverflow.com/a/10473026/254475
	 */
	/** @var int $length */
	$length = mb_strlen($needle);
	return ($needle === mb_substr($haystack, 0, $length));
}

/**
 * @param string $string
 * @param string|string[] $charactersToRemove
 * @return string
 */
function rm_string_clean($string, $charactersToRemove) {
	if (!is_array($charactersToRemove)) {
		$charactersToRemove = rm_string_split($charactersToRemove);
	}
	/** @var string $result */
	$result =
		strtr(
			$string
			,array_combine(
				$charactersToRemove
				,array_fill(0, count($charactersToRemove), '')
			)
		)
	;
	return $result;
}

/**
 * @param string $string
 * @return array
 * @link http://us3.php.net/manual/en/function.str-split.php#107658
 */
function rm_string_split($string) {return preg_split("//u", $string, -1, PREG_SPLIT_NO_EMPTY);}

/**
 * @param string $tag
 * @param array(string => string) $attributes [optional]
 * @param string $content [optional]
 * @return string
 */
function rm_tag($tag, array $attributes = array(), $content = null) {
	return Df_Core_Model_Format_Html_Tag::output($tag, $attributes, $content);
}

/**
 * @param array(string => mixed) $parameters [optional]
 * @return string
 */
function rm_tag_a(array $parameters = array()) {
	return Df_Core_Model_Output_Html_A::output($parameters);
}

/**
 * @param int|null $length [optional]
 * @return string
 */
function rm_uniqid($length = null) {
	/** @var string $result */
	$result =
		uniqid(
			$prefix = ''
			/**
			 * Важно использовать $more_entropy = true,
			 * потому что иначе на быстрых серверах
			 * (я заметил такое поведение при использовании Zend Server Enterprise и PHP 5.4)
			 * uniqid будет иногда возвращать одинаковые значения
			 * при некоторых двух последовательных вызовах.
			 */,$more_entropy = true
		)
	;
	if (!is_null($length)) {
		$result =
			substr(
				$result
				/**
				 * Обратите внимание, что уникальным является именно окончание uniqid, а не начало.
				 * Два последовательных вызова uniqid могу вернуть:
				 * 5233061890334
				 * 52330618915dd
				 * Начало у этих значений — одинаковое, а вот окончание — различное.
				 */
				, -$length
			)
		;
	}
	return $result;
}