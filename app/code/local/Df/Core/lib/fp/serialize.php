<?php
if (false) {
	/**
	 * @param mixed $data
	 * @return string
	 */
	function igbinary_serialize($data) {df_should_not_be_here(__FUNCTION__);}

	/**
	 * @param string $data
	 * @return mixed|bool
	 */
	function igbinary_unserialize($data) {df_should_not_be_here(__FUNCTION__);}
}

/**
 * 2017-01-05
 * "Надо портировать функцию `df_json_decode` из `mage2pro/core`,
 * потому что за 2.5 года она ушла далеко вперёд
 * по сравнению с одноимённой функцией Российской сборки Magento":
 * https://github.com/magento-russia/2/issues/12
 * @param $s|null $string
 * @param bool $throw [optional]
 * @return array|mixed|bool|null
 * @throws Exception
 * Returns the value encoded in json in appropriate PHP type.
 * Values true, false and null are returned as TRUE, FALSE and NULL respectively.
 * NULL is returned if the json cannot be decoded
 * or if the encoded data is deeper than the recursion limit.
 * http://php.net/manual/function.json-decode.php
 */
function df_json_decode($s, $throw = true) {
	/** @var mixed|bool|null $r */
	// 2015-12-19
	// У PHP 7.0.1 декодировании пустой строки почему-то приводит к сбою: «Decoding failed: Syntax error».
	if ('' === $s || is_null($s)) {
		$r = $s;
	}
	else {
		/**                                                                                         
		 * 2016-10-30
		 * json_decode('7700000000000000000000000') возвращает 7.7E+24
		 * https://3v4l.org/NnUhk
		 * http://stackoverflow.com/questions/28109419
		 * Такие длинные числоподобные строки используются как идентификаторы КЛАДР
		 * (модулем доставки «Деловые Линии»), и поэтому их нельзя так корёжить.
		 * Поэтому используем константу JSON_BIGINT_AS_STRING
		 * https://3v4l.org/vvFaF
		 * 2018-01-05
		 * 3-й параметр появился в 5.3.0:
		 * 		«Added the optional depth. The default recursion depth was increased from 128 to 512».
		 * 4-й параметр появился в 5.4.0:
		 * 		«The options parameter was added».
		 * http://php.net/manual/en/function.json-decode.php#refsect1-function.json-decode-changelog
		 */
		$r = version_compare(phpversion(), '5.4', '<')
			? json_decode($s, true)
			: json_decode($s, true, 512, JSON_BIGINT_AS_STRING)
		;
		// 2016-10-28
		// json_encode(null) возвращает строку 'null',
		// а json_decode('null') возвращает null.
		// Добавил проверку для этой ситуации, чтобы не считать её сбоем.
		if (is_null($r) && 'null' !== $s && $throw) {
			df_assert_ne(JSON_ERROR_NONE, json_last_error());
			df_error(
				"Parsing a JSON document failed with the message «%s».\nThe document:\n{$s}"
				,json_last_error_msg()
			);
		}
	}
	return $r;
}

/** @return bool */
function rm_igbinary_available() {
	/** bool $result */
	static $result;
	if (!isset($result)) {
		$result = function_exists('igbinary_serialize');
	}
	return $result;
}

/**
 * @param mixed|Df_Core_Serializable $data
 * @return string
 */
function rm_serialize($data) {
	/** @var bool $supportsSerializableInterface */
	$supportsSerializableInterface = is_object($data) && ($data instanceof Df_Core_Serializable);
	/** @var array(string => mixed) $container */
	if ($supportsSerializableInterface) {
		$container = $data->serializeBefore();
	}
	/** @var string $result */
	$result = rm_igbinary_available() ? igbinary_serialize($data) : serialize($data);
	if ($supportsSerializableInterface) {
		$data->serializeAfter($container);
	}
	return $result;
}

/**
 * @param mixed $data
 * @return string
 */
function rm_serialize_simple($data) {
	return
		false && rm_igbinary_available()
		? igbinary_serialize($data)
		/**
		 * Zend_Json::encode использует json_encode при наличии расширения PHP JSON
		 * и свой внутренний кодировщик при отсутствии расширения PHP JSON.
		 * @see Zend_Json::encode
		 * @link http://stackoverflow.com/questions/4402426/json-encode-json-decode-vs-zend-jsonencode-zend-jsondecode
		 * Обратите внимание,
		 * что расширение PHP JSON не входит в системные требования Magento.
		 * @link http://www.magentocommerce.com/system-requirements
		 * Поэтому использование Zend_Json::encode выглядит более правильным, чем json_encode.
		 *
		 * $this->saveData($key, Zend_Json::encode($value));
		 *
		 * P.S. Оно, конечно, правильнее, но @see json_encode() работает заметно быстрее,
		 * чем обёртка @see Zend_Json::encode()
		 */
		: json_encode($data)
	;
}

/**
 * @param string $data
 * @return mixed|Df_Core_Serializable|bool
 */
function rm_unserialize($data) {
	/** @var mixed|Df_Core_Serializable|bool $result */
	$result = rm_igbinary_available() ? @igbinary_unserialize($data) : @unserialize($data);
	if (is_object($result) && ($result instanceof Df_Core_Serializable)) {
		$result->unserializeAfter();
	}
	return $result;
}

/**
 * @param string $data
 * @return mixed|bool
 */
function rm_unserialize_simple($data) {
	return
		false && rm_igbinary_available()
		? @igbinary_unserialize($data)
		: df_json_decode($data)
	;
}


 