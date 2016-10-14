<?php
/**
 * 2015-02-07
 * @see df_column() — аналог для коллекций.
 * http://php.net/manual/function.array-column.php
 * Эмуляцию для PHP версий ниже 5.5 взял отсюда:
 * https://github.com/ramsey/array_column
 * Как сказано: «It is written by PHP 5.5 array_column creator itself»
 * http://stackoverflow.com/a/20746278
 */
if (!function_exists('array_column')) {
	/**
	 * @param array(string|int => mixed) $array
	 * @param mixed $column_key
	 * @param mixed $index_key [optional]
	 * @return array(string|int => mixed)
	 */
	function array_column($array, $column_key, $index_key = null) {
		/** @var mixed[] $arguments */
		$arguments = func_get_args();
		return call_user_func_array(array(Df_Core_Helper_ArrayColumn::s(), 'process'), $arguments);
	}
}

/**
 * Раньше функция @see df_a() была универсальной:
 * она принимала в качестве аргумента $entity как массивы, так и объекты.
 * В 99.9% случаев в качестве параметра передавался массив.
 * Поэтому ради ускорения работы системы
 * вынес обработку объектов в отдельную функцию @see df_o()
 * @param array(string => mixed) $array
 * @param string $key
 * @param mixed $default
 * @return mixed|null
 */
function df_a(array $array, $key, $default = null) {
	return isset($array[$key]) ? $array[$key] : $default;
}

/**
 * @used-by Df_Core_Model_Geo_Locator_Real::loadFromCache()
 * @used-by Df_Core_Model_Geo_Locator_Real::queryArray()
 * @used-by Df_IPay_Model_Action_Abstract::getRequestParam()
 * @used-by Df_IPay_Model_Action_Abstract::getRequestParamR()
 * @used-by Df_Shipping_Model_Response::json()
 * Этот метод предназначен для извлечения некоторого значения
 * из многомерного массива посредством нотации ключ1/ключ2/ключ3
 * Например: df_a_deep(array('test' => array('eee' => 3)), 'test/eee') вернёт «3».
 * Обратите внимание, что ядро Magento реализует аналогичный алгоритм
 * в методе @see Varien_Object::getData()
 * Наша функция работает не только с объектами @see Varien_Object, но и с любыми массивами.
 * @param array(string => mixed) $array
 * @param string $path
 * @param mixed $defaultValue [optional]
 * @return mixed|null
 */
function df_a_deep(array $array, $path, $defaultValue = null) {
	df_param_string_not_empty($path, 1);
	/** @var mixed|null $result */
	$result = null;
	/**
	 * 2015-02-06
	 * Обратите внимание, что если разделитель отсутствует в строке,
	 * то @uses explode() вернёт не строку, а массив со одим элементом — строкой.
	 * Это вполне укладывается в наш универсальный алгоритм.
	 */
	/** @var string[] $pathParts */
	$pathParts = df_explode_xpath($path);
	while ($pathParts) {
		$result = df_a($array, array_shift($pathParts));
		if (is_array($result)) {
			$array = $result;
		}
		else {
			if ($pathParts) {
				// Ещё не прошли весь путь, а уже наткнулись на не-массив.
				$result = null;
			}
			break;
		}
	}
	if (is_null($result)) {
		$result = $defaultValue;
	}
	return $result;
}

/**
 * 2015-08-23
 * @used-by Df_Localization_Onetime_Processor_Db_Column::process()
 * @param mixed $node
 * @param string|string[] $path
 * @param callable $callback
 * @param mixed $params [optional]
 * @return void
 */
function df_a_deep_walk(&$node, $path, $callback, $params = null) {
	if (!is_array($node)) {
		if (!$path) {
			$node = call_user_func($callback, $node, $params);
		}
	}
	else {
		/**
		 * 2015-02-06
		 * Обратите внимание, что если разделитель отсутствует в строке,
		 * то @uses explode() вернёт не строку, а массив со одим элементом — строкой.
		 * Это вполне укладывается в наш универсальный алгоритм.
		 */
		if (!is_array($path)) {
			$path = df_explode_xpath($path);
		}
		while ($path) {
			/** @var string $step */
			$step = array_shift($path);
			if ('*' !== $step) {
				if (isset($node[$step])) {
					// Первый аргумент передаётся по ссылке
					// Проверял — работает даже для индексов массива:
					// https://3v4l.org/9f3pk
					df_a_deep_walk($node[$step], $path, $callback, $params);
				}
			}
			else if ($path) {
				// Здесь ссылки тоже работают: https://3v4l.org/damQl
				foreach ($node as &$child) {
					/** @var mixed $child */
					df_a_deep_walk($child, $path, $callback, $params);
				}
			}
		}
	}
}

/**
 * 2015-08-10
 * Ищет в массиве элемент в том случае, когда тип элемента точно неизвесте
 * (например, может быть или число, или строковое представление числа).
 * @used-by Df_Dataflow_Model_Importer_Product::importAttributeValues()
 * @param array(string => mixed) $array
 * @param mixed $value
 * @param mixed $default
 * @return int|string|null|mixed
 */
function df_find_fuzzy(array $array, $value, $default = null) {
	/** @var int|string|null|mixed $result */
	foreach ($array as $currentKey => $currentValue) {
		/** @var int|string $currentKey */
		/** @var mixed $currentValue */
		// Намеренно используем ==, а не === — в этом суть метода!
		if ($value == $currentValue) {
			$result = $currentKey;
			break;
		}
	}
	return isset($result) ? $result : $default;
}

/**
 * 2015-02-07
 * Аналог @see array_change_key_case() с поддержкой UTF-8.
 * Реализацию взял отсюда: http://php.net/manual/function.array-change-key-case.php#107715
 * Обратите внимание, что @see array_change_key_case() некорректно работает с UTF-8.
 * Например:
		$countries = array('Россия' => 'RU', 'Украина' => 'UA', 'Казахстан' => 'KZ');
	array_change_key_case($countries, CASE_UPPER)
 * вернёт:
	(
		[РнссШя] => RU
		[УЪраШна] => UA
		[Њазахстан] => KZ
	)
 * @used-by rm_key_uc()
 * @param array(string => mixed) $input
 * @param int $case
 * @return array(string => mixed)
 */
function df_array_change_key_case(array $input, $case = CASE_LOWER) {
	$case = ($case == CASE_LOWER) ? MB_CASE_LOWER : MB_CASE_UPPER;
	/** @var array(string => mixed) $result */
	$result = array();
	foreach ($input as $key => $value) {
		/** @var string $key */
		/** @var mixed $value */
		$result[mb_convert_case($key, $case, 'UTF-8')] = $value;
	}
	return $result;
}

/**
 * @param mixed $elements
 * @return mixed[]
 */
function df_array_clean($elements) {
	$elements = is_array($elements) ? $elements : func_get_args();
	return df_clean($elements);
}

/**
 * @uses array_combine() при использовании интерпретатора PHP версии ниже 5.4 требует,
 * чтобы оба массива содержали не менее 1 элемента:
 * http://php.net/manual/function.array-combine.php
 * «5.4.0	Previous versions issued E_WARNING and returned FALSE for empty arrays»
 * Поэтому при прямом применении @uses array_combine()
 * требуется выделять случай с пустыми массивами в отдельную ветку алгоритма,
 * что усложняет код.
 * Функция @see df_array_combine() делает то же, что и @uses array_combine(),
 * но также способна работать с пустыми массивами.
 *
 * 2015-02-08
 * Если требуется заполнить все ключи одним и тем же значнием,
 * то используйте стандартную функцию @see array_fill_keys()
 * http://php.net/manual/function.array-fill-keys.php
 *
 * @param string[]|int[] $keys
 * @param mixed[] $values
 * @return array(string|int => mixed)
 */
function df_array_combine(array $keys, array $values) {
	return !$keys ? array() : array_combine($keys, $values);
}

/**
 * Эта функция отличается от @uses array_fill() только тем,
 * что разрешает параметру $length быть равным нулю.
 * Если $length = 0, то функция возвращает пустой массив.
 * @uses array_fill() разрешает параметру $num (аналог $length)
 * быть равным нулю только начиная с PHP 5.6:
 * http://php.net/manual/function.array-fill.php
 * «5.6.0	num may now be zero. Previously, num was required to be greater than zero»
 * @param int $startIndex
 * @param int $length
 * @param mixed $value
 * @return mixed[]
 */
function df_array_fill($startIndex, $length, $value) {
	return !$length ? array() : array_fill($startIndex, $length, $value);
}

/**
 * Алгоритм взят отсюда:
 * http://php.net/manual/function.array-unshift.php#106570
 * @param array(string => mixed) $array
 * @param string $key
 * @param mixed $value
 */
function df_array_unshift_assoc(&$array, $key, $value)  {
	$array = array_reverse($array, $preserve_keys = true);
	$array[$key] = $value;
	$array = array_reverse($array, $preserve_keys = true);
}

/**
 * 2015-02-07
 * Обратите внимание,
 * что во многих случаях эффективней использовавать @see array_filter() вместо @see df_clean().
 * http://php.net/manual/function.array-filter.php
 * @see array_filter() с единственным параметром удалит из массива все элементы,
 * чьи значения приводятся к логическому «false».
 * Т.е., помимо наших array('', null, array()),
 * @see array_filter() будет удалять из массива также элементы со значениями «false» и «0».
 * Если это соответствует требуемому поведению в конретной точке программного кода,
 * то используйте именно @see array_filter(),
 * потому что встроенная функция @see array_filter() в силу реализации на языке С
 * будет работать на порядки быстрее, нежели @see df_clean().
 *
 * 2015-01-22
 * Теперь из исходного массива будут удаляться элементы,
 * чьим значением является пустой массив.
 * @param mixed[] $array
 * @param mixed[] $additionalValuesToClean [optional]
 * @return mixed[]
 */
function df_clean(array $array, array $additionalValuesToClean = []) {
	/** @var mixed[] $result */
	$result = [];
	// 2015-01-22
	// Теперь из исходного массива будут удаляться элементы,
	// чьим значением является пустой массив.
	/** @var mixed[] $valuesToClean */
	$valuesToClean = array_merge(array('', null, array()), $additionalValuesToClean);
	/** @var bool $isAssoc */
	$isAssoc = df_is_assoc($array);
	foreach ($array as $key => $value) {
		/** @var int|string $key */
		/** @var mixed $value */
		if (!in_array($value, $valuesToClean, true)) {
			if ($isAssoc) {
				$result[$key]= $value;
			}
			else {
				$result[]= $value;
			}
		}
	}
	return $result;
}

/**
 * Отличается от @see df_clean() дополнительным удалением их исходного массива элементов,
 * чьим значением является применение @see rm_cdata() к пустой строке.
 * Пример применения:
 * @used-by Df_1C_Cml2_Export_Processor_Catalog_Product::getElement_Производитель()
 * @param array(string => mixed) $array
 * @return array(string => mixed)
 */
function df_clean_xml(array $array) {return df_clean($array, array(rm_cdata('')));}

/**
 * 2015-02-11
 * Аналог @see array_column() для коллекций.
 * Ещё один аналог: @see Varien_Data_Collection::getColumnValues(),
 * но его результат — не ассоциативный.
 * @param Traversable|array(int|string => Varien_Object) $collection
 * @param string $methodForValue
 * @param string|null $methodForKey [optional]
 * @return array(int|string => mixed)
 */
function df_column($collection, $methodForValue, $methodForKey = null) {
	/** @var  $result */
	$result = array();
	foreach ($collection as $id => $object) {
		/** @var int|string $id */
		/** @var Varien_Object $object */
		/** @var int|string $key */
		$key = !$methodForKey ? $id : call_user_func(array($object, $methodForKey));
		$result[$key] = call_user_func(array($object, $methodForValue));
	}
	return $result;
}

/**
 * 2015-02-07
 * Эта функция аналогична методу @see Varien_Data_Collection::walk(),
 * и даже может использоваться вместо @see Varien_Data_Collection::walk(),
 * однако, в отличие от @see Varien_Data_Collection::walk(),
 * она способна работать не только с коллекцией,
 * но также с массивом объектов и объектом, поддерживающим интерфейс @see Traversable.
 * @param Traversable|array(int|string => Varien_Object) $collection
 * @param string $method
 * @param mixed $param
 * @return mixed[]
 */
function df_each($collection, $method, $param = null) {
	/** @var array(int|string => mixed) $result */
	$result = array();
	/** @var mixed[] $arguments */
	$arguments = func_get_args();
	foreach ($collection as $key => $object) {
		/** @var int|string $key */
		/** @var object $object */
		$result[$key] = call_user_func_array(array($object, $method), array_slice($arguments, 2));
	}
	return $result;
}

/**
 * 2015-02-11
 * Эта функция отличается от @see iterator_to_array() тем, что допускает в качестве параметра
 * не только @see Traversable, но и массив.
 * @param Traversable|array $traversable
 * @return array
 */
function df_iterator_to_array($traversable) {
	return is_array($traversable) ? $traversable : iterator_to_array($traversable);
}

define('RM_BEFORE', -1);
define('RM_AFTER', 1);
/**
 * 2015-02-11
 * Эта функция аналогична @see array_map(), но обладает 3-мя дополнительными возможностями:
 * 1) её можно применять не только к массивам, но и к @see Traversable.
 * 2) она позволяет удобным способом передавать в $callback дополнительные параметры
 * 3) позволяет передавать в $callback ключи массива
 * до и после основного параметра (элемента массива).
 * Обратите внимание, что
		df_map('Df_Cms_Model_ContentsMenu_Applicator::i', $this->getCmsRootNodes())
 * эквивалентно
		$this->getCmsRootNodes()->walk('Df_Cms_Model_ContentsMenu_Applicator::i')
 * @param callable $callback
 * @param array(int|string => mixed)|Traversable $array
 * @param mixed|mixed[] $paramsToAppend [optional]
 * @param mixed|mixed[] $paramsToPrepend [optional]
 * @param int $keyPosition [optional]
 * @return array(int|string => mixed)
 */
function df_map(
	$callback, $array, $paramsToAppend = array(), $paramsToPrepend = array(), $keyPosition = 0
) {
	$array = df_iterator_to_array($array);
	/** @var array(int|string => mixed) $result */
	if (!$paramsToAppend && !$paramsToPrepend && 0 === $keyPosition) {
		$result = array_map($callback, $array);
	}
	else {
		$paramsToAppend = rm_array($paramsToAppend);
		$paramsToPrepend = rm_array($paramsToPrepend);
		$result = array();
		foreach ($array as $key => $item) {
			/** @var int|string $key */
			/** @var mixed $item */
			/** @var mixed[] $primaryArgument */
			switch ($keyPosition) {
				case RM_BEFORE:
					$primaryArgument = array($key, $item);
					break;
				case RM_AFTER:
					$primaryArgument = array($item, $key);
					break;
				default:
					$primaryArgument = array($item);
			}
			/** @var mixed[] $arguments */
			$arguments = array_merge($paramsToPrepend, $primaryArgument, $paramsToAppend);
			$result[$key] = call_user_func_array($callback, $arguments);
		}
	}
	return $result;
}

/**
 * Оба входных массива должны быть ассоциативными
 * @param array(string => mixed) $array1
 * @param array(string => mixed) $array2
 * @return array(string => mixed)
 */
function df_merge_not_empty(array $array1, array $array2) {return array_filter($array2) + $array1;}

/**
 * 2015-02-11
 * Эта функция отличается от @see array_merge() только тем,
 * что все вместо нескольких параметров принимает массив из параметров.
 * Это бывает удобно в функциональном программировании, например:
 * @used-by Df_Dataflow_Model_Registry_MultiCollection::getEntities()
 * @used-by Df_Dellin_Model_Request_Rate::getDates()
 * @param array(array(int|string => mixed)) $arrays
 * @return array(int|string => mixed)
 */
function df_merge_single(array $arrays) {return call_user_func_array('array_merge', $arrays); }

/**
 * 2015-02-07
 * Функция предназначена для работы только с ассоциативными массивами!
 * Фантастически лаконичное и красивое решение!
 * Вынес его в отдельную функцию только для того, чтобы не забыть!
 * Пример применения:
 * @used-by Df_Directory_Model_Resource_Country_Collection::toOptionArrayRm()
 * Операция «+» игнорирует те элементы второго массива,
 * ключи которого присутствуют в первом массиве:
 * «The keys from the first array will be preserved.
 * If an array key exists in both arrays,
 * then the element from the first array will be used
 * and the matching key's element from the second array will be ignored.»
 * http://php.net/manual/function.array-merge.php
 * Остальные элементы второго массива (ключи которых отсутствуют в первом массиве)
 * будут добавлены к результату.
 * Например:
		$source = array(
			'RU' => 'Россия', 'KZ' => 'Казахстан', 'TJ' => 'Таджикистан','US' => 'США','CA' => 'Канада'
		);
  		$priorityItems = array('TJ' => 'Таджикистан', 'CA' => 'Канада');
		print_r($priorityItems + $source);
 * Вернёт:
		Array
		(
			[TJ] => Таджикистан
			[CA] => Канада
			[RU] => Россия
			[KZ] => Казахстан
			[US] => США
		)
 * http://3v4l.org/CFM4L
 * @param array(string => mixed) $source
 * @param array(string => mixed) $priorityItems
 * @return array(string => mixed)
 */
function df_prepend(array $source, array $priorityItems) {return $priorityItems + $source;}

/**
 * 2015-02-07
 * Функция предназначена для работы только с ассоциативными массивами!
 * Фантастически лаконичное и красивое решение!
 * Вынес его в отдельную функцию, чтобы не забыть!
 * Например:
		$source = array(
			'RU' => 'Россия', 'KZ' => 'Казахстан', 'TJ' => 'Таджикистан','US' => 'США','CA' => 'Канада'
 		);
		$priorityKeys = array('TJ', 'CA');
		print_r(df_prepend_by_keys($source, $priorityKeys));
 * Вернёт:
	 Array
	 (
		 [TJ] => Таджикистан
		 [CA] => Канада
		 [RU] => Россия
		 [KZ] => Казахстан
		 [US] => США
	 )
 * http://3v4l.org/QYffO
 * Обратите внимание, что @uses array_flip() корректно работает с пустыми массивами:
	print_r(array_flip(array()));
 * вернёт array()
 * http://3v4l.org/Kd01X
 * @uses df_prepend()
 * @used-by Df_Directory_Model_Resource_Country_Collection::toOptionArrayRm()
 * @param array(string => mixed) $source
 * @param string[] $priorityKeys
 * @return array(string => mixed)
 */
function df_prepend_by_keys(array $source, array $priorityKeys) {
	return df_prepend($source, df_select_ordered($source, $priorityKeys));
}

/**
 * 2015-02-07
 * Функция предназначена для работы только с ассоциативными массивами!
 * Фантастически лаконичное и красивое решение!
 * Вынес его в отдельную функцию, чтобы не забыть!
 * Например:
		$source = array(
			'Россия' => 'RU'
			,'Казахстан' => 'KZ'
			,'Таджикистан' => 'TJ'
			,'США' => 'US'
			,'Канада' => 'CA'
		);
		$priorityValues = array('TJ', 'CA');
		print_r(df_prepend_by_values($source, $priorityValues));
 * вернёт:
		Array
		(
			[Таджикистан] => TJ
			[Канада] => CA
			[Россия] => RU
			[Казахстан] => KZ
			[США] => US
		)
 * http://3v4l.org/tNms4
 * @uses df_prepend_by_keys()
 * @used-by Df_Directory_Model_Resource_Country_Collection::toOptionArrayRm()
 * @param array(string => mixed) $source
 * @param string[] $priorityValues
 * @return array(string => mixed)
 */
function df_prepend_by_values(array $source, array $priorityValues) {
	return array_flip(df_prepend_by_keys(array_flip($source), $priorityValues));
}

/**
 * 2015-02-11
 * Из ассоциативного массива $source выбирает элементы с ключами $keys.
 * В отличие от @see df_select_ordered() не учитывает порядок ключей $keys
 * и поэтому работает быстрее, чем @see df_select_ordered().
 * @param array(string => string)|Traversable $source
 * @param string[] $orderedKeys
 * @return array(string => string)
 */
function df_select($source, array $keys)  {
	return array_intersect_key(df_iterator_to_array($source), array_fill_keys($keys, null));
}

/**
 * 2015-02-08
 * Из ассоциативного массива $source выбирает элементы с ключами $orderedKeys
 * и возвращает их в том же порядке, в каком они перечислены в $orderedKeys.
 * Если порядок ключей не важен, но используйте более быстрый аналог @see df_select().
 * @param array(string => string)|Traversable $source
 * @param string[] $orderedKeys
 * @return array(string => string)
 */
function df_select_ordered($source, array $orderedKeys)  {
	/** @var array(string => null) $resultKeys */
	$resultKeys = array_fill_keys($orderedKeys, null);
	/** @var array(string => string) $resultWithGarbage */
	$resultWithGarbage = array_merge($resultKeys, df_iterator_to_array($source));
	return array_intersect_key($resultWithGarbage, $resultKeys);
}

/**
 * http://en.wikipedia.org/wiki/Tuple
 * @param array $arrays
 * @return array
 */
function df_tuple(array $arrays) {
	/** @var array $result */
	$result = array();
	/** @var int $count */
	$countItems = max(array_map('count', $arrays));
	for ($ordering = 0; $ordering < $countItems; $ordering++) {
		/** @var array $item */
		$item = array();
		foreach ($arrays as $arrayName => $array) {
			$item[$arrayName]= df_a($array, $ordering);
		}
		$result[$ordering] = $item;
	}
	return $result;
}

/**
 * 2016-10-11
 * Мои модули для Magento 2 используют такой алгоритм: http://stackoverflow.com/a/1320156
 * Однако Российская сборка Magento должна поддерживать PHP 5.2,
 * поэтому испольузем другой алгоритм, который я составил на основе этого:
 * http://stackoverflow.com/a/1320112
 * @used-by df_cc_class()
 * @used-by df_cc_class_uc()
 * @param array $a
 * @return mixed[]
 */
function dfa_flatten(array $a) {
	if (!is_array($a)) {
		// nothing to do if it's not an array
		return array($a);
	}
	/** @var mixed[] $result */
	$result = array();
	foreach ($a as $v) {
		if (!is_array($v)) {
			$result[]= $v;
		}
		else {
			$result = array_merge($result, dfa_flatten($v));
		}
	}
	return $result;
}

/**
 * @param mixed|mixed[] $value
 * @return mixed[]|string[]|float[]|int[]
 */
function rm_array($value) {return is_array($value) ? $value : array($value);}

/**
 * Работает в разы быстрее, чем @see array_unique()
 * «Just found that array_keys(array_flip($array)); is amazingly faster than array_unique();.
  * About 80% faster on 100 element array,
  * 95% faster on 1000 element array
  * and 99% faster on 10000+ element array.»
 * http://stackoverflow.com/questions/5036504/php-performance-question-faster-to-leave-duplicates-in-array-that-will-be-searc#comment19991540_5036538
 * http://www.php.net/manual/en/function.array-unique.php#70786
 * 2015-02-06
 * Обратите внимание, что т.к. алгоритм @see rm_array_unique_fast() использует @uses array_flip(),
 * то @see rm_array_unique_fast() можно применять только в тех ситуациях,
 * когда массив содержит только строки и целые числа,
 * иначе вызов @uses array_flip() завершится сбоем уровня E_WARNING:
 * «array_flip(): Can only flip STRING and INTEGER values»
 * http://magento-forum.ru/topic/4695/
 * В реальной практике сбой случается, например, когда массив содержит значение null:
 * http://3v4l.org/bat52
 * Пример кода, приводящего к сбою: rm_array_unique_fast(array(1, 2, 2, 3, null))
 * В то же время, несмотря на E_WARNING, метод всё-таки возвращает результат,
 * правда, без недопустимых значений:
 * при подавлении E_WARNING rm_array_unique_fast(array(1, 2, 2, 3, null)) вернёт:
 * array(1, 2, 3).
 * Более того, даже если сбойный элемент содержится в середине исходного массива,
 * то результат при подавлении сбоя E_WARNING будет корректным (без недопустимых элементов):
 * rm_array_unique_fast(array(1, 2, null,  2, 3)) вернёт тот же результат array(1, 2, 3).
 * http://3v4l.org/uvJoI
 * По этой причине добавил оператор @ перед @uses array_flip()
 * @param array(int|string => int|string) $array
 * @return array(int|string => int|string)
 */
function rm_array_unique_fast(array $array) {return array_keys(@array_flip($array));}

/**
 * @param string $glue
 * @param string|string[] $elements
 * @return string
 */
function rm_concat_clean($glue, $elements) {
	if (!is_array($elements)) {
		/** @var string[] $arguments */
		$arguments = func_get_args();
		$elements = rm_tail($arguments);
	}
	return implode($glue, df_array_clean($elements));
}

/**
 * 2015-02-18
 * По смыслу функция @see rm_extend() аналогична методу @see Varien_Simplexml_Element::extend()
 * и предназначена для слияния настроечных опций,
 * только, в отличие от @see Varien_Simplexml_Element::extend(),
 * @see rm_extend() сливает не XML, а ассоциативные массивы.
 *
 * Обратите внимание, что вместо @see rm_extend() нельзя использовать ни
 * @see array_replace_recursive(), ни @see array_merge_recursive(),
 * ни тем более @see array_replace() и @see array_merge()
 * Нерекурсивные аналоги отметаются сразу, потому что не способны сливать вложенные структуры.
 * Но и стандартыне рекурсивные функции тоже не подходят:
 *
 * 1)
 * array_merge_recursive(array('width' => 180), array('width' => 200))
 * вернёт: array(array('width' => array(180, 200)))
 * http://php.net/manual/function.array-merge-recursive.php
 * Наша функция rm_extend(array('width' => 180), array('width' => 200))
 * вернёт array('width' => 200)
 *
 * 2)
 * array_replace_recursive(array('x' => array('A', 'B')), array('x' => 'C'))
 * вернёт: array('x' => array('С', 'B'))
 * http://php.net/manual/function.array-replace-recursive.php
 * Наша функция rm_extend(array('x' => array('A', 'B')), array('x' => 'C'))
 * вернёт array('x' => 'C')
 *
 * @param array(string => mixed) $defaults
 * @param array(string => mixed) $newValues
 * @return array(string => mixed)
 */
function rm_extend(array $defaults, array $newValues) {
	/** @var array(string => mixed) $result */
	// Здесь ошибочно было бы $result = array(),
	// потому что если ключ отсутствует в $newValues,
	// то тогда он не попадёт в $result.
	$result = $defaults;
	foreach ($newValues as $key => $newValue) {
		/** @var int|string $key */
		/** @var mixed $newValue */
		/** @var mixed $defaultValue */
		$defaultValue = df_a($defaults, $key);
		if (!is_array($defaultValue)) {
			$result[$key] = $newValue;
		}
		else {
			if (is_array($newValue)) {
				$result[$key] = rm_extend($defaultValue, $newValue);
			}
			else {
				if (is_null($newValue)) {
					unset($result[$key]);
				}
				else {
					// Если значение по умолчанию является массивом,
					// а новое значение не является массивом,
					// то это наверняка говорит об ошибке программиста.
					df_error(
						'rm_extend: значением по умолчанию ключа «{key}» является массив {defaultValue},'
						. "\nоднако программист ошибочно пытается заместить его"
						. ' значением {newValue} типа «{newType}», что недопустимо:'
						. "\nзамещаемое значение для массива должно быть либо массивом, либо «null»."
						,array(
							'{defaultValue}' => df_t()->singleLine(rm_dump($defaultValue))
							,'{key}' => $key
							,'{newType}' => gettype($newValue)
							,'{newValue}' => rm_dump($newValue)
						)
					);
				}
			}
		}
	}
	return $result;
}

/**
 * Функция возвращает null, если массив пуст.
 * Обратите внимание, что неверен код
	$result = reset($array);
	return (false === $result) ? null : $result;
 * потому что если @uses reset() вернуло false, это не всегда означает сбой метода:
 * ведь первый элемент массива может быть равен false.
 * @see rm_last()
 * @see rm_tail()
 * @param array $array
 * @return mixed|null
 */
function rm_first(array $array) {return !$array ? null : reset($array);}

/**
 * 2015-03-13
 * Отсекает последний элемент массива и возвращает «голову» (массив оставшихся элементов).
 * Похожая системная функция @see array_pop() возвращает отсечённый последний элемент.
 * Противоположная системная функция @see rm_tail() отсекает первый элемент массива.
 * @used-by Df_Core_Model_Action::delegate()
 * @used-by Portal_Page_Block_Frontend::portalRenderChild()
 * @param mixed[] $array
 * @return mixed[]|string[]
 */
function rm_head(array $array) {return array_slice($array, 0, -1);}

/**
 * 2015-02-07
 * Обратите внимание, что алгоритмов проверки массива на ассоциативность найдено очень много:
 * http://stackoverflow.com/questions/173400/how-to-check-if-php-array-is-associative-or-sequential
 * Я уже давно (несоколько лет) использую приведённый ниже.
 * Пока он меня устраивает, да и сама задача такой проверки
 * возникает у меня в Российской сборке Magento редко
 * и не замечал её особого влияния на производительность системы.
 * Возможно, другие алгоритмы лучше, лень разбираться.
 * @param array(int|string => mixed) $array
 * @return bool
 */
function rm_is_assoc(array $array) {
	$result = false;
	foreach (array_keys($array) as $key => $value) {
		/**
		 * Согласно спецификации PHP, ключами массива могут быть целые числа, либо строки.
		 * Третьего не дано.
		 * http://php.net/manual/language.types.array.php
		 */
		if (
			/**
			 * Раньше тут стояло !is_int($key)
			 * Способ проверки $key !== $value нашёл по ссылке ниже:
			 * http://www.php.net/manual/en/function.is-array.php#84488
			 */
			$key !== $value
		) {
			$result = true;
			break;
		}
	}
	return $result;
}

/**
 * 2015-04-17
 * Проверяет, является ли массив многомерным.
 * http://stackoverflow.com/a/145348
 * Пока никем не используется.
 * @param array(int|string => mixed) $array
 * @return bool
 */
function rm_is_multi(array $array) {
	/** @var bool $result */
	$result = false;
	foreach ($array as $value) {
		/** @var mixed $value */
		if (is_array($value)) {
			$result = true;
			break;
		}
	}
	return $result;
}

/**
 * @param array(string => mixed) $array
 * @return array(string => mixed)
 */
function rm_key_uc(array $array) {return df_array_change_key_case($array, CASE_UPPER);}

/**
 * Функция возвращает null, если массив пуст.
 * Если использовать @see end() вместо @see rm_last(),
 * то указатель массива после вызова end сместится к последнему элементу.
 * При использовании @see rm_last() смещения указателя не происходит,
 * потому что в @see rm_last() попадает лишь копия массива.
 *
 * Обратите внимание, что неверен код
 	$result = end($array);
 	return (false === $result) ? null : $result;
 * потому что если @uses end() вернуло false, это не всегда означает сбой метода:
 * ведь последний элемент массива может быть равен false.
 * http://www.php.net/manual/en/function.end.php#107733
 * @see rm_first()
 * @see rm_tail()
 * @param mixed[] $array
 * @return mixed|null
 */
function rm_last(array $array) {return !$array ? null : end($array);}

/**
 * @used-by Df_InTime_Api::call()
 * http://stackoverflow.com/a/18576902
 * @param mixed $value
 * @return array
 */
function rm_stdclass_to_array($value) {return json_decode(json_encode($value), $assoc = true);}

/**
 * Отсекает первый элемент массива и возвращает хвост (аналог CDR в Lisp).
 * Обратите внимание, что если исходный массив содержит меньше 2 элементов,
 * то функция вернёт пустой массив.
 * @see rm_first()
 * @see rm_last()
 * @param mixed[] $array
 * @return mixed[]|string[]
 */
function rm_tail(array $array) {return array_slice($array, 1);}

/**
 * 2015-02-26
 * Складывает 2 числовых массива как векторы.
 * Второй аргумент может быть также числом:
 * тогда считается, что все координаты этого вектора равны данному числу.
 * @param int[]|float[] $a
 * @param int|float|int[]|float[] $b
 * @return int[]|float[]
 */
function rm_vector_sum(array $a, $b) {
	/** @var int $length */
	$length = count($a);
	if (!is_array($b)) {
		$b = df_array_fill(0, $length, $b);
	}
	else {
		df_assert($length === count($b));
		$b = array_values($b);
	}
	$a = array_values($a);
	/** @var int[]|float[] $result */
	$result = array();
	for ($i = 0; $i < $length; $i++) {
		$result[]= $a[$i] + $b[$i];
	}
	return $result;
}