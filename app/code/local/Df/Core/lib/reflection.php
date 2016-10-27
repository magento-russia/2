<?php
/**
 * 2016-08-10
 * @param int $offset [optional]
 * @return string
 */
function df_caller_f($offset = 0) {
	/** @var string $result */
	$result = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3 + $offset)[2 + $offset]['function'];
	/**
	 * 2016-09-06
	 * Порой бывают случаи, когда @see df_caller_f() ошибочно вызывается из @see \Closure.
	 * Добавил защиту от таких случаев.
	 */
	if (df_contains($result, '{closure}')) {
		df_error_html("The <b>df_caller_f()</b> function is wrongly called from the «<b>{$result}</b>» closure.");
	}
	return $result;
}

/**
 * 2016-08-10
 * @param int $offset [optional]
 * @return string
 */
function df_caller_m($offset = 0) {
	/** @var array(string => string) $bt */
	$bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3 + $offset)[2 + $offset];
	/** @var string $method */
	return $bt['class'] . '::' . $bt['function'];
}

/**
 * 2016-08-29
 * @return string
 */
function df_caller_mh() {return df_tag('b', [], df_caller_ml(1));}

/**
 * 2016-08-31
 * @used-by df_caller_mh()
 * @param int $offset [optional]
 * @return string
 */
function df_caller_ml($offset = 0) {return '\\' . df_caller_m(1 + $offset) . '()';}

/**
 * 2016-02-08
 * Применение @uses dfa_flatten() делает возможным вызовы типа:
 * df_cc_class_uc('Aa', ['Bb', 'Cb']) => Aa\Bb\Cb
 * @see df_cc_class_uc()
 * @param string[] ...$args
 * @return string
 */
function df_cc_class(...$args) {return implode('\\', dfa_flatten($args));}

/**
 * 2016-10-15
 * @param string[] ...$args
 * @return string
 */
function df_cc_class_(...$args) {return implode('_', dfa_flatten($args));}

/**
 * 2016-03-25
 * Применение @uses dfa_flatten() делает возможным вызовы типа:
 * df_cc_class_uc('aa', ['bb', 'cc']) => Aa\Bb\Cc
 * Мы используем это в модулях Stripe и Checkout.com.
 * @see df_cc_class()
 * @param string[] ...$args
 * @return string
 */
function df_cc_class_uc(...$args) {return df_cc_class(df_ucfirst(dfa_flatten($args)));}

/**
 * 2016-03-25
 * Применение @uses dfa_flatten() делает возможным вызовы типа:
 * df_cc_class_uc('aa', ['bb', 'cc']) => Aa\Bb\Cc
 * Мы используем это в модулях Stripe и Checkout.com.
 * @see df_cc_class()
 * @param string[] ...$args
 * @return string
 */
function df_cc_class_uc_(...$args) {return df_cc_class_(df_ucfirst(dfa_flatten($args)));}

/**
 * 2016-08-10
 * Если класс не указан, то вернёт название функции.
 * Поэтому в качестве $a1 можно передавать null.
 * @param string|object|null|array(object|string)|array(string = string) $a1
 * @param string|null $a2 [optional]
 * @return string
 */
function df_cc_method($a1, $a2 = null) {
	return df_ccc('::',
		$a2 ? [df_cts($a1), $a2]
			: (!isset($a1['function']) ? $a1
				: [dfa($a1, 'class'), $a1['function']]
			)
	);
}

/**
 * 2016-10-15
 * 2016-10-20
 * Нельзя делать параметр $class опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 *
 * @param string|object $class
 * @return string
 */
function df_class_delimiter($class) {
	/** @var string $s */
	$s = is_object($class) ? get_class($class) : $class;
	return df_contains($s , '\\') ? '\\' : '_';
}

/**
 * 2016-01-01
 * 2016-10-20
 * Нельзя делать параметр $class опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * @param string|object $class
 * @return string
 */
function df_class_first($class) {return df_first(df_explode_class($class));}

/**
 * 2015-12-29
 * 2016-10-20
 * Нельзя делать параметр $class опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * @param string|object $class
 * @return string
 */
function df_class_last($class) {return df_last(df_explode_class($class));}

/**
 * 2015-12-29
 * 2016-10-20
 * Нельзя делать параметр $class опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * @param string|object $class
 * @return string
 */
function df_class_last_lc($class) {return df_lcfirst(df_class_last($class));}

/**
 * 2016-01-01
 * 2016-10-20
 * Нельзя делать параметр $class опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * @param string|object $class
 * @return bool
 */
function df_class_my($class) {return in_array(df_class_first($class), ['Df', 'Dfe', 'Dfr']);}

/**
 * 2016-07-10
 * Df\Payment\R\Response => Df\Payment\R\Request
 * @param string|object $class
 * @param string $newSuffix
 * @return string
 */
function df_class_replace_last($class, $newSuffix) {
	/** @var string $s */
	$s = df_cts($class);
	/** @var string $d */
	$d = df_class_delimiter($s);
	/** @var string[] $a */
	$a = df_explode_class($s);
	$a[count($a) - 1] = $newSuffix;
	return implode($d, $a);
}

/**
 * 2016-02-09
 * 2016-10-20
 * Нельзя делать параметр $class опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * @param string|object $class
 * @return string
 */
function df_class_second($class) {return df_explode_class($class)[1];}

/**
 * 2016-02-09
 * 2016-10-20
 * Нельзя делать параметр $class опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * @param string|object $class
 * @return string
 */
function df_class_second_lc($class) {return df_lcfirst(df_class_second($class));}

/**
 * 2016-08-04
 * 2016-08-10
 * @uses defined() не реагирует на методы класса, в том числе на статические,
 * поэтому нам использовать эту функию безопасно: https://3v4l.org/9RBfr
 * @param string|object $class
 * @param string $name
 * @param mixed|callable $default [optional]
 * @return mixed
 */
function df_const($class, $name, $default = null) {
	/** @var string $nameFull */
	$nameFull = df_cts($class) . '::' . $name;
	return defined($nameFull) ? constant($nameFull) : df_call_if($default);
}

/**
 * 2016-02-08
 * Проверяет наличие следующих классов в указанном порядке:
 * 1) <имя конечного модуля>\<окончание класса>
 * 2) $default
 * Возвращает первый из найденных классов.
 * @param object|string $caller
 * @param string $suffix
 * @param string|null $default [optional]
 * @param bool $throw [optional]
 * @return string|null
 */
function df_con($caller, $suffix, $default = null, $throw = true) {return
	df_con_generic(function($callerC, $suffix) {
		/** @var string $del */
		$del = df_class_delimiter($callerC);
		return df_module_name($callerC, $del) . $del . $suffix;
	}, $caller, $suffix, $default, $throw)
;}

/**
 * Инструмент парадигмы «convention over configuration».
 * 2016-10-26
 * @param \Closure $handler
 * @param object|string $caller
 * @param string $suffix
 * @param string|null $def [optional]
 * @param bool $throw [optional]
 * @return string|null
 */
function df_con_generic(\Closure $handler, $caller, $suffix, $def = null, $throw = true) {return
	dfcf(function($handler, $callerC, $suffix, $def = null, $throw = true) {
		/** @var string $class */
		$class = $handler($callerC, $suffix);
		return df_class_exists($class) ? $class : (
			$def ?: (!$throw ? null : df_error("Системе требуется класс «{$class}»."))
		);
	}, [$handler, df_cts($caller), $suffix, $def, $throw])
;}

/**
 * 2016-10-26
 * @param object|string $caller
 * @param string $suffix
 * @param string|null $default [optional]
 * @param bool $throw [optional]
 * @return string|null
 */
function df_con_child($caller, $suffix, $default = null, $throw = true) {return
	df_con_generic(function($callerC, $suffix) {
		/** @var string $del */
		$del = df_class_delimiter($callerC);
		return $callerC . $del . $suffix;
	}, $caller, $suffix, $default, $throw)
;}

/**
 * 2016-08-29
 * @used-by dfp_method_call_s()
 * @param string|object $caller
 * @param string $suffix
 * @param string $method
 * @param mixed[] $params [optional]
 * @return mixed
 */
function df_con_s($caller, $suffix, $method, array $params = []) {return dfcf(
	function($caller, $suffix, $method, array $params = []) {return
		call_user_func_array([df_con($caller, $suffix), $method], $params)
	;}
, func_get_args());}

/**
 * 2016-07-10
 * @param object|string $caller
 * @param string $suffix
 * @param string|null $default [optional]
 * @param bool $throw [optional]
 * @return string|null
 */
function df_con_sibling($caller, $suffix, $default = null, $throw = true) {return
	df_con_generic(function($callerC, $suffix) {return
		df_class_replace_last($callerC, $suffix)
	;}, $caller, $suffix, $default, $throw)
;}

/**
 * 2015-08-14
 * Обратите внимание, что @uses get_class() не ставит «\» впереди имени класса:
 * http://3v4l.org/HPF9R
	namespace A;
	class B {}
	$b = new B;
	echo get_class($b);
 * => «A\B»
 *
 * 2015-09-01
 * Обратите внимание, что @uses ltrim() корректно работает с кириллицей:
 * https://3v4l.org/rrNL9
 * echo ltrim('\\Путь\\Путь\\Путь', '\\');  => Путь\Путь\Путь
 *
 * 2016-10-20
 * Нельзя делать параметр $class опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 *
 * @used-by df_explode_class()
 * @used-by df_module_name()
 * @param string|object $class
 * @param string $delimiter [optional]
 * @return string
 */
function df_cts($class, $delimiter = '\\') {
	/** @var string $result */
	$result = is_object($class) ? get_class($class) : ltrim($class, '\\');
	// 2016-01-29
	$result = df_trim_text_right($result, '\Interceptor');
	return '\\' === $delimiter ?  $result : str_replace('\\', $delimiter, $result);
}

/**
 * 2016-01-29
 * @param string $class
 * @param string $delimiter
 * @return string
 */
function df_cts_lc($class, $delimiter) {return implode($delimiter, df_explode_class_lc($class));}

/**
 * 2016-04-11
 * Dfe_CheckoutCom => dfe_checkout_com
 * @param string $class
 * @param string $delimiter
 * @return string
 */
function df_cts_lc_camel($class, $delimiter) {
	return implode($delimiter, df_explode_class_lc_camel($class));
}

/**
 * @param string|object $class
 * @return string[]
 * 2016-10-20
 * Нельзя делать параметр $class опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 */
function df_explode_class($class) {return df_explode_multiple(['\\', '_'], df_cts($class));}

/**
 * 2016-04-11
 * Dfe_CheckoutCom => [Dfe, Checkout, Com]
 * 2016-10-20
 * Нельзя делать параметр $class опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * @param string|object $class
 * @return string[]
 */
function df_explode_class_camel($class) {return
	dfa_flatten(df_explode_camel(explode('\\', df_cts($class))))
;}

/**
 * 2016-01-14
 * 2016-10-20
 * Нельзя делать параметр $class опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * @param string|object $class
 * @return string[]
 */
function df_explode_class_lc($class) {return df_lcfirst(df_explode_class($class));}

/**
 * 2016-04-11
 * 2016-10-20
 * Нельзя делать параметр $class опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * Dfe_CheckoutCom => [dfe, checkout, com]
 * @param string|object $class
 * @return string[]
 */
function df_explode_class_lc_camel($class) {return df_lcfirst(df_explode_class_camel($class));}

/**
 * «Df_YandexMarket_Yml_Document» => «yandex.market»
 * «Df_1C_Cml2_Export_Document_Catalog» => «1c»
 * @param Varien_Object $object
 * @param string $separator
 * @return string
 */
function df_module_id(Varien_Object $object, $separator) {
	/** @var string $className */
	$className = get_class($object);
	/** @var string $key */
	$key = $className . $separator;
	/** @var array(string => string) */
	static $cache;
	if (!isset($cache[$key])) {
		// «yandex.market»
		$cache[$key] = mb_strtolower(
			// «Yandex.Market»
			implode($separator, df_explode_camel(
				// «YandexMarket»
				dfa(df_explode_class($className), 1)
			)
		));
	}
	return $cache[$key];
}

/**
 * «Dfe\AllPay\Response» => «Dfe_AllPay»
 * 2016-10-20
 * Нельзя делать параметр $class опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * 2016-10-26
 * Функция успешно работает с короткими именами классов:
 * «A\B\C» => «A_B»
 * «A» => A»
 * https://3v4l.org/Jstvc
 * @param string|object $class [optional]
 * @param string $delimiter [optional]
 * @return string
 */
function df_module_name($class, $delimiter = '_') {return dfcf(
	function($class, $delimiter) {return
		implode($delimiter, array_slice(df_explode_class($class), 0, 2))
	;}
, [df_cts($class), $delimiter]);}

/**
 * 2016-08-28
 * «Dfe\AllPay\Response» => «AllPay»
 * 2016-10-20
 * Нельзя делать параметр $class опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * @param string|object $class
 * @return string
 */
function df_module_name_short($class) {return dfcf(function($class) {return
	df_explode_class($class)[1]
;}, [df_cts($class)]);}

/**
 * 2016-02-16
 * «Dfe\CheckoutCom\Method» => «dfe_checkout_com»
 * 2016-10-20
 * Нельзя делать параметр $class опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * @param string|object $class
 * @param string $delimiter [optional]
 * @return string
 */
function df_module_name_lc($class, $delimiter = '_') {return
	implode($delimiter, df_explode_class_lc_camel(df_module_name($class, '\\')))
;}

/**
 * Намеренно добавили к названию метода окончание «ByClass»,
 * чтобы название метода не конфликтовало с родительским методом
 * @see Df_Core_Model::moduleTitle()
 * «Df_1C_Cml2_Export_Document_Catalog» => «1C:Управление торговлей»
 * 2016-10-20
 * Нельзя делать параметр $class опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * @param string|object $class
 * @return string
 */
function df_module_title($class) {
	/** @var string $moduleName */
	$moduleName = df_module_name($class);
	return df_leaf_s(df_config_node('modules', $moduleName,  'title'), $moduleName);
}


