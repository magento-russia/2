<?php
/**
 * @param SimpleXMLElement $e
 * @throws Df_Core_Exception
 */
function df_assert_leaf(SimpleXMLElement $e) {
	if (!df_check_leaf($e)) {
		df_error("Требуется лист XML, однако получена ветка XML:\n%s", rm_xml_report($e));
	}
}

/**
 * @param string $text
 * @return string
 */
function df_cdata($text) {return Df_Core_Sxe::markAsCData($text);}

/**
 * 2015-02-27
 * Обратите внимание,
 * что метод @see SimpleXMLElement::count() появился только в PHP 5.3,
 * поэтому мы его не используем: http://php.net/manual/simplexmlelement.count.php
 * Также обратите внимание, что count($e->children())
 * некорректно возвращает 1 для листов в PHP 5.1: http://3v4l.org/PT6Pt
 * Однако нам не нужно поддерживать PHP 5.1.
 *
 * Обратите внимание, что для несуществующего узла попытка вызова @uses count()
 * привелёт к сбою: «Warning: count(): Node no longer exists»
 * http://3v4l.org/PsIPe#v512
 *
 * Текущий алгоритм проверен на работоспособность здесь: http://3v4l.org/VldTN
 *
 * 2015-08-16
 *
 * Как ни странно, написанное выше действительно верно: http://3v4l.org/covo1
 *
 * Обратите внимение, что класс @see SimpleXMLElement не реализует интерфейс @see Iterator,
 * а реализует только интерфейс @see Traversable.
 * http://php.net/manual/class.iterator.php
 * http://php.net/manual/class.traversable.php
 * http://php.net/manual/en/simplexmlelement.count.php
 * Однако @uses count() почему-то работает для него. SimpleXMLElement — самый загадочный класс PHP.
 *
 * @param SimpleXMLElement $e
 * @return bool
 */
function df_check_leaf(SimpleXMLElement $e) {
	/** @noinspection PhpParamsInspection */
	/**
	 * 2015-08-15
	 * Нельзя здесь использовать count($child->children()),
	 * потому что класс @see SimpleXmlElement не реализует интерфейс @see Iterator,
	 * а реализует только интерфейс @see Traversable.
	 * http://php.net/manual/class.iterator.php
	 * http://php.net/manual/class.traversable.php
	 * http://php.net/manual/en/simplexmlelement.count.php
	 */
	return !rm_xml_exists($e) || !$e->children()->count();
}

/**
 * 2015-02-27
 * Обратите внимание на разницу между @see SimpleXMLElement::asXML()
 * и @see SimpleXMLElement::__toString() / оператор (string)$this.
 *
 * @see SimpleXMLElement::__toString() и (string)$this
 * возвращают непустую строку только для концевых узлов (листьев дерева XML).
 * Пример:
	<?xml version='1.0' encoding='utf-8'?>
		<menu>
			<product>
				<cms>
					<class>aaa</class>
					<weight>1</weight>
				</cms>
				<test>
					<class>bbb</class>
					<weight>2</weight>
				</test>
			</product>
		</menu>
 * Здесь для $e1 = $xml->{'product'}->{'cms'}->{'class'}
 * мы можем использовать $e1->__toString() и (string)$e1.
 * http://3v4l.org/rAq3F
 * Однако для $e2 = $xml->{'product'}->{'cms'}
 * мы не можем использовать $e2->__toString() и (string)$e2,
 * потому что узел «cms» не является концевым узлом (листом дерева XML).
 * http://3v4l.org/Pkj37
 * Более того, метод @see SimpleXMLElement::__toString()
 * отсутствует в PHP версий 5.2.17 и ниже:
 * http://3v4l.org/Wiia2#v500
 *
 * 2015-03-02
 * Обратите внимание,
 * то мы специально допускаем возможность для первого параметра $e принимать значение null:
 * это даёт нам возможность писать код типа:
 * @used-by Df_Page_Helper_Head::needSkipAsStandardCss()
	rm_leaf_b(rm_config_node(
		'df/page/skip_standard_css/', rm_state()->getController()->getFullActionName()
	))
 * без дополнительных проверок, имеется ли в наличии запрашиваемый лист дерева XML
 * (если лист отсутствует, то @see rm_config_node() вернёт null)
 *
 * @param SimpleXMLElement|null $e [optional]
 * @param string|null $default [optional]
 * @return string|null
 */
function rm_leaf(SimpleXMLElement $e = null, $default = null) {
	/** @var string $result */
	/**
	 * 2015-08-04
	 * Нельзя здесь использовать !$e,
	 * потому что для концевых текстовых узлов с ненулевым целым значением (например: «147»)
	 * такое выражение довольно-таки неожиданно возвращает true.
	 * @see SimpleXMLElement вообще необычный класс с нестандартным поведением.
	 * Чтобы понять, почему в данном случае !$e равно true, посморите функцию @see rm_xml_exists()
	 *
	 * Так вот, @see rm_xml_exists() для текстового узла всегда возвращает false,
	 * даже если текстовое значение не приводится к false (то же «147»).
	 *
	 * Почему так происходит — видно из реализации @see rm_xml_exists(): !empty($e)
	 * То есть, empty($e) для текстовыхз узлов возвращает true.
	 *
	 * Например:
		<Остаток>
			<Склад>
				<Ид>6f87e83f-722c-11df-b336-0011955cba6b</Ид>
				<Количество>147</Количество>
			</Склад>
		</Остаток>
	 * Если здесь сделать xpath Остаток/Склад/Количество,
	 * то для узла «147» !$e почему-то вернёт true,
	 * хотя в данном случае $e является полноценным объектом @see SimpleXMLElement
	 * и (string)$e возвращает «147».
	 */
	if (is_null($e)) {
		$result = $default;
	}
	else {
		df_assert_leaf($e);
		$result = (string)$e;
		if (df_empty_string($result)) {
			/**
			 * 2015-09-25
			 * Добавил данное условие, чтобы различать случай пустого узла и отсутствия узла.
			 * Пример пустого узла ru_RU:
			 * <term>
			 * 		<en_US>Order Total</en_US>
			 * 		<ru_RU></ru_RU>
			 * </term>
			 * Так вот, для пустого узла empty($e) вернёт false,
			 * а для отсутствующего узла — true.
			 */
			$result = empty($e) ? $default : '';
		}
	}
	return $result;
}

/**
 * @param SimpleXMLElement|null $e [optional]
 * @param bool $default [optional]
 * @return bool
 */
function rm_leaf_b(SimpleXMLElement $e = null, $default = false) {return df_bool(rm_leaf($e, $default));}

/**
 * @param SimpleXMLElement $e
 * @param string $child
 * @param string|mixed|null $default [optional]
 * @return string|mixed|null
 */
function rm_leaf_child(SimpleXMLElement $e, $child, $default = null) {
	return rm_leaf($e->{$child}, $default);
}

/**
 * 2015-08-16
 * Намеренно убрал параметр $default.
 * @param SimpleXMLElement|null $e [optional]
 * @return float
 */
function rm_leaf_f(SimpleXMLElement $e = null) {return df_float(rm_leaf($e));}

/**
 * 2015-08-16
 * Намеренно убрал параметр $default.
 * @param SimpleXMLElement|null $e [optional]
 * @return int
 */
function rm_leaf_i(SimpleXMLElement $e = null) {return df_int(rm_leaf($e));}

/**
 * @param SimpleXMLElement|null $e [optional]
 * @param string $default [optional]
 * @return string
 */
function rm_leaf_s(SimpleXMLElement $e = null, $default = '') {return (string)rm_leaf($e, $default);}

/**
 * @param SimpleXMLElement|null $e [optional]
 * @param string $default [optional]
 * @return string
 */
function rm_leaf_sne(SimpleXMLElement $e = null, $default = '') {
	/** @var string $result */
	$result = rm_leaf_s($e, $default);
	if (df_empty_string($result)) {
		df_error('Лист дерева XML должен быть непуст, однако он пуст.');
	}
	return $result;
}

/**
 * @param string $xml
 * @param bool $throw [optional]
 * @return Df_Core_Sxe|null
 * @throws Df_Core_Exception
 */
function df_xml($xml, $throw = true) {
	df_param_string_not_empty($xml, 0);
	/** @var Df_Core_Sxe $result */
	$result = null;
	try {
		$result = new Df_Core_Sxe($xml);
	}
	catch (Exception $e) {
		if ($throw) {
			df_error(
				"При синтаксическом разборе документа XML произошёл сбой:\n"
				. "«%s»\n"
				. "********************\n"
				. "%s\n"
				. "********************\n"
				, df_ets($e)
				, df_trim($xml)
			);
		}
	}
	return $result;
}

/**
 * @param SimpleXMLElement $e
 * @param string $name
 * @param bool $required [optional]
 * @return SimpleXMLElement|null
 * @throws Df_Core_Exception
 */
function rm_xml_child(SimpleXMLElement $e, $name, $required = false) {
	/** @var SimpleXMLElement[] $childNodes */
	$childNodes = rm_xml_children($e, $name, $required);
	/** @var SimpleXMLElement|null $result */
	if (is_null($childNodes)) {
		$result = null;
	}
	else {
		/**
		 * Обратите внимание, что если мы имеем структуру:
			<dictionary>
				<rule/>
				<rule/>
				<rule/>
			</dictionary>
		 * то $this->e()->{'rule'} вернёт не массив, а объект (!),
		 * но при этом @see count() для этого объекта работает как для массива (!),
		 * то есть реально возвращает количество детей типа rule.
		 * Далее, оператор [] также работает, как для массива (!)
		 * http://stackoverflow.com/a/16100099
		 * Класс SimpleXMLElement — вообще один из самых необычных классов PHP.
		 */
		df_assert_eq(1, count($childNodes));
		$result = $childNodes[0];
		df_assert($result instanceof SimpleXMLElement);
	}
	return $result;
}

/**
 * @param SimpleXMLElement $e
 * @param string $name
 * @param bool $required [optional]
 * @return SimpleXMLElement|null
 * @throws Df_Core_Exception
 */
function rm_xml_children(SimpleXMLElement $e, $name, $required = false) {
	df_param_string_not_empty($name, 0);
	/** @var SimpleXMLElement|null $result */
	if (rm_xml_exists_child($e, $name)) {
		/**
		 * Обратите внимание, что если мы имеем структуру:
			<dictionary>
				<rule/>
				<rule/>
				<rule/>
			</dictionary>
		 * то $e->{'rule'} вернёт не массив, а объект (!),
		 * но при этом @see count() для этого объекта работает как для массива (!),
		 * то есть реально возвращает количество детей типа rule.
		 * Далее, оператор [] также работает, как для массива (!)
		 * http://stackoverflow.com/a/16100099
		 * Класс SimpleXMLElement — вообще один из самых необычных классов PHP.
		 */
		$result = $e->{$name};
	}
	else if (!$required) {
		$result = null;
	}
	else {
		df_error("Требуемый узел «{node}» отсутствует в документе:\n{xml}", array(
			'{node}' => $name, '{xml}' => rm_xml_report($e)
		));
	}
	return $result;
}

/**
 * 2015-02-27
 * Алгоритм взят отсюда: http://stackoverflow.com/a/5344560
 * Проверил, что он работает: http://3v4l.org/tnEIJ
 * Обратите внимание, что isset() вместо empty() не сработает: http://3v4l.org/2P5o0
 * isset, однако, работает для проверки наличия дочерних листов: @see rm_xml_exists_child()
 *
 * Обратите внимание, что оператор $e->{'тест'} всегда возвращает объект SimpleXMLElement,
 * вне зависимости от наличия узла «тест», просто для отсутствующего узла данный объект будет пуст,
 * и empty() для него вернёт true.
 *
 * 2015-08-04
 * Заметил, что empty($e) для текстовых узлов всегда возвращает true,
 * даже если узел как строка приводится к true (например: «147»).
 * Например:
 * Например:
	<Остаток>
		<Склад>
			<Ид>6f87e83f-722c-11df-b336-0011955cba6b</Ид>
			<Количество>147</Количество>
		</Склад>
	</Остаток>
 * Если здесь сделать xpath Остаток/Склад/Количество,
 * то для узла «147» @see rm_xml_exists($e) вернёт false.
 *
 * Обратите внимание, что эту особенность использует алгоритм @see df_check_leaf():
 * return !rm_xml_exists($e) || !count($e->children());
 *
 * @param SimpleXMLElement|null $e
 * @return bool
 */
function rm_xml_exists(SimpleXMLElement $e = null) {return !empty($e);}

/**
 * http://stackoverflow.com/questions/1560827/php-simplexml-check-if-a-child-exist#comment20135428_1562158
 * @param SimpleXMLElement $e
 * @param string $child
 * @return bool
 */
function rm_xml_exists_child(SimpleXMLElement $e, $child) {return isset($e->{$child});}

/**
 * 2015-08-24
 * @used-by Df_Localization_Dictionary::e()
 * @param string $filename
 * @return Df_Core_Sxe
 */
function rm_xml_load_file($filename) {
	/** @var Df_Core_Sxe $result */
	libxml_use_internal_errors(true);
	$result = @simplexml_load_file($filename, Df_Core_Sxe::_C);
	if (!$result) {
		df_xml_throw_last(
			"При разборе файла XML произошёл сбой.\nФайл: " . df_path_relative($filename)
		);
	}
	return $result;
}

/**
 * 2015-08-24
 * @used-by rm_xml_load_file()
 * @param string $message
 * @throws Df_Core_Exception
 */
function df_xml_throw_last($message) {
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
 * @param string $tag
 * @param array(string => string) $attributes [optional]
 * @return Df_Core_Sxe
 */
function rm_xml_node($tag, array $attributes = array()) {
	/** @var Df_Core_Sxe $result */
	$result = df_xml(df_sprintf('<%s/>', $tag));
	foreach ($attributes as $name => $value) {
		/** @var string $name */
		/** @var string $value */
		$result->addAttribute($name, $value);
	}
	return $result;
}

/**
 * @used-by rm_xml_output_html()
 * @used-by rm_xml_output_plain()
 * @used-by rm_xml_report()
 */
define('RM_XML_BEGIN', '{rm-xml}');
define('RM_XML_END', '{/rm-xml}');

/**
 * @used-by df_exception_to_session()
 * @param string|string[] $text
 * @return string|string[]
 */
function rm_xml_output_html($text) {
	return
		is_array($text)
		? array_map(__FUNCTION__, $text)
		: (
			!df_contains($text, RM_XML_BEGIN)
			? $text
			: preg_replace_callback(
				strtr(
					'#{tag-begin}([\s\S]*){tag-end}#mui'
					,array('{tag-begin}' => RM_XML_BEGIN, '{tag-end}' => RM_XML_END)
				)
				/** @uses rm_xml_output_html_callback() */
				, 'rm_xml_output_html_callback'
				, $text
			)
		)
	;
}

/**
 * @used-by rm_xml_output_html()
 * Обратите внимание, что тег должен быть именно <pre>, именно в нижнем регистре
 * и только с атрибутом class, потому что этот тег разбирается регулярным выражением
 * в методе @see Df_Core_Helper_Text::nl2br()
 * @param string[] $matches
 * @return string
 */
function rm_xml_output_html_callback(array $matches) {
	return strtr('<pre class="rm-xml">{contents}</div>', array(
		'{contents}' => df_e(df_normalize(dfa($matches, 1, ''))))
	);
}

/**
 * @used-by Df_Qa_Message::sections()
 * @param string|string[] $text
 * @return string|string[]
 */
function rm_xml_output_plain($text) {
	return
		is_array($text)
		? array_map(__FUNCTION__, $text)
		: str_replace(array(RM_XML_BEGIN, RM_XML_END), null, $text)
	;
}

/**
 * @param SimpleXMLElement|Varien_Simplexml_Element $e
 * @return string
 */
function rm_xml_report(SimpleXMLElement $e) {
	$xml = $e instanceof Varien_Simplexml_Element ? $e->asNiceXml() : $e->asXml();
	return RM_XML_BEGIN . $xml . RM_XML_END;
}
