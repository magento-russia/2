<?php
/**
 * @param string $xml
 * @return Df_Varien_Simplexml_Element
 * @throws Df_Core_Exception_Client
 */
function rm_xml($xml) {
	df_param_string_not_empty($xml, 0);
	/** @var Df_Varien_Simplexml_Element $result */
	$result = null;
	try {
		$result = new Df_Varien_Simplexml_Element($xml);
	}
	catch (Exception $e) {
		df_error(
			"При синтаксическом разборе документа XML произошёл сбой:\r\n"
			. "«%s»\r\n"
			. "********************\r\n"
			. "%s\r\n"
			. "********************\r\n"
			, rm_ets($e)
			, df_trim($xml)
		);
	}
	return $result;
}

/**
 * @param SimpleXMLElement $e
 * @param string $paramName
 * @return bool
 */
function rm_xml_child_exists(SimpleXMLElement $e, $paramName) {
	/** @link http://stackoverflow.com/questions/1560827/php-simplexml-check-if-a-child-exist#comment20135428_1562158 */
	return isset($e->{$paramName});
}

/**
 * @param SimpleXMLElement $e
 * @param string $paramName
 * @return string|int|float|null
 */
function rm_xml_child_simple(SimpleXMLElement $e, $paramName) {
	/** @var string|int|float|null $result */
	if (!rm_xml_child_exists($e, $paramName)) {
		$result = null;
	}
	else {
		$result = (string)$e->{$paramName};
		/**
		 * Обрабатываем случай с пустым тэгом:
		 *
		 * <ХарактеристикаТовара>
		 * <Наименование>Тип кожи (Обувь (Для характеристик))</Наименование>
		 * <Значение/>
		 * </ХарактеристикаТовара>
		 *
		 * В этом случае возвращаем значение по умолчанию.
		 */
		if (df_empty_string($result)) {
			$result = null;
		}
	}
	return $result;
}

/**
 * 2015-08-24
 * @used-by Df_Localization_Model_Dictionary::getSimpleXmlElement()
 * @param string $filename
 * @return Df_Varien_Simplexml_Element
 */
function rm_xml_load_file($filename) {
	/** @var Df_Varien_Simplexml_Element $result */
	libxml_use_internal_errors(true);
	$result = @simplexml_load_file($filename, Df_Varien_Simplexml_Element::_CLASS);
	if (!$result) {
		rm_xml_throw_last(
			"При разборе файла XML произошёл сбой.\r\nФайл: " . rm_fs_format($filename)
		);
	}
	return $result;
}

/**
 * 2015-08-24
 * @used-by rm_xml_load_file()
 * @param string $message
 * @throws Df_Core_Exception_Client
 */
function rm_xml_throw_last($message) {
	/** @var LibXMLError[] LibXMLError */
	$errors = libxml_get_errors();
	/** @var string[] $messages */
	$messages = array($message);
	foreach ($errors as $error) {
		/** @var LibXMLError $error */
		$messages[]= sprintf("(%d, %d) %s", $error->line, $error->column, $error->message);
	}
	df_error($messages);
}

/**
 * @param string $text
 * @return string
 */
function rm_xml_mark($text) {return Df_Core_Model_Output_Xml::s()->mark($text);}

 