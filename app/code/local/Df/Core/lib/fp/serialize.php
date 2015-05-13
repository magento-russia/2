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
		/**
		 * @see Zend_Json::decode() использует json_decode при наличии расширения PHP JSON
		 * и свой внутренний кодировщик при отсутствии расширения PHP JSON.
		 * @see Zend_Json::decode
		 * @link http://stackoverflow.com/questions/4402426/json-encode-json-decode-vs-zend-jsonencode-zend-jsondecode
		 * Обратите внимание,
		 * что расширение PHP JSON не входит в системные требования Magento.
		 * @link http://www.magentocommerce.com/system-requirements
		 * Поэтому использование @see Zend_Json::decode выглядит более правильным,
		 * чем @see json_decode().
		 *
		 * Обратите внимание, что при использовании @see json_decode() напрямую
		 * параметр $assoc = true надо указывать обязательно,
		 * иначе @see json_decode() может вернуть объект даже в том случае,
		 * когда посредством @see json_encode() был кодирован массив.
		 *
		 * При использовании @see Zend_Json::decode()
		 * второй параметр $objectDecodeType имеет значение Zend_Json::TYPE_ARRAY по умолчанию,
		 * поэтому его можно не указывать.
		 *
		 * $result = Zend_Json::decode($serialized);
		 *
		 * P.S. Оно, конечно, правильнее, но @see json_decode() работает заметно быстрее,
		 * чем обёртка @see Zend_Json::decode()
		 */
		: json_decode($data, $assoc = true)
	;
}


 