<?php
class Df_Varien_Simplexml_Element extends Varien_Simplexml_Element {
	/** @return void */
	public function __destruct() {unset(self::$_canonicalArray[spl_object_hash($this)]);}

	/**
	 * @param array $attributes
	 * @return Df_Varien_Simplexml_Element
	 */
	public function addAttributes(array $attributes) {
		foreach ($attributes as $name => $value) {
			/** @var string $name */
			/** @var mixed $value */
			df_assert_string($name);
			// убрал strval($value) для ускорения системы
			$this->addAttribute($name, $value);
		}
		return $this;
	}

	/**
	 * @param string $name
	 * @param string|null $value [optional]
	 * @param string|null $namespace [optional]
	 * @return SimpleXMLElement
	 * @throws Exception
	 */
	public function addChild($name, $value = null, $namespace = null) {
		/** @var SimpleXMLElement $result */
		try {
			$result = parent::addChild($name, $value, $namespace);
		}
		catch (Exception $e) {
			df_error(
				'При назначении тэгу «%s» значения «%s» произошёл сбой: «%s».'
				, $name, $value, rm_ets($e)
			);
		}
		return $result;
	}

	/**
	 * @param string $tagName
	 * @param string $valueAsText
	 * @return Df_Varien_Simplexml_Element
	 */
	public function addChildText($tagName, $valueAsText) {
		/** @var Df_Varien_Simplexml_Element $result */
		$result = $this->addChild($tagName);
		/**
		 * Обратите внимание, что
		 * SimpleXMLElement::addChild создаёт и возвращает не просто SimpleXMLElement,
		 * как говорит документация, а объект класса родителя.
		 * Поэтому в нашем случае addChild создаст объект Df_Varien_Simplexml_Element.
		 */
		$result->setCData($valueAsText);
		return $result;
	}

	/**
	 * Отличия от родительского метода:
	 * 1) гарантия, что результат — массив
	 * 2) кэширование результата
	 * @override
	 * @return mixed[]
	 */
	public function asCanonicalArray() {
		/** @var string $id */
		$id = spl_object_hash($this);
		if (!isset(self::$_canonicalArray[$id])) {
			self::$_canonicalArray[$id] = parent::asCanonicalArray();
			/**
			 * @see Varien_Simplexml_Element::asCanonicalArray может возвращать строку в случае,
			 * когда структура исходных данных не соответствует массиву.
			 */
			df_result_array(self::$_canonicalArray[$id]);
		}
		return self::$_canonicalArray[$id];
	}
	/** @var array(string => mixed[]) */
	private static $_canonicalArray = array();

	/** @return string */
	public function asXMLPart() {return str_replace('<?xml version="1.0"?>', '', $this->asXML());}

	/**
	 * Этот метод отличается от родительского
	 * только возвращением null вместо false в случае отсутствия значения.
	 *
	 * Обратите внимание, что мы можем так делать,
	 * потому что родительский класс сам внутри себя не использует метод descend
	 * (и, соответственно, не полагается на возвращение значения false).
	 *
	 * Обратите внимание, что интерпретатор PHP не разрешает
	 * присваивать полям объектов класса SimpleXMLElement (и его наследников)
	 * значения сложных типов.
	 * Такое присваивание приводит к сбою:
	 * «Warning: It is not yet possible to assign complex types to attributes».
	 *
	 * По этой причине не используем кэширование результата.
	 *
	 * в комментарии к свойству @see Varien_Simplexml_Element::$_parent
	 * дана рекомендация использования функции @see spl_object_hash(),
	 * однако это слишком сложно и необчевидно, ускорит ли работу системы
	 * (также могут быть проблемы с расходом оперативной памяти).
	 *
	 * @override
	 * @param string|string[] $path
	 * @return Df_Varien_Simplexml_Element|null
	 */
	public function descend($path) {return df_ftn(parent::descend($path));}

	/**
	 * @param string|string[] $path
	 * @return Df_Varien_Simplexml_Element
	 */
	public function descendO($path) {
		$result = $this->descend($path);
		df_assert($result instanceof Df_Varien_Simplexml_Element);
		return $result;
	}

	/**
	 * @param array $array
	 * @param array|bool $wrapInCData [optional]
	 * @return Df_Varien_Simplexml_Element
	 */
	public function importArray(array $array, $wrapInCData = array()) {
		foreach ($array as $key => $value) {
			/** @var string $key */
			/** @var mixed $value */
			if (!is_array($value)) {
				$this->importString($key, $value, $wrapInCData);
			}
			else {
				if (df_is_assoc($value)) {
					/** @var Df_Varien_Simplexml_Element $childNode */
					$childNode =
						$this->addChild(
							/**
							 * Раньше тут стояло df_string($key)
							 * Для ускорения модуля Яндекс.Маркет df_string убрал.
							 * Вроде ничего не ломается.
							 */
							$key
						)
					;
					/** @var array|null $childData */
					$childData = $value;
					// Данный программный код позволяет импортировать атрибуты тэгов
					/** @var array(string => string)|null $attributes $attributes */
					$attributes = df_a($value, self::KEY__ATTRIBUTES);
					if (!is_null($attributes)) {
						df_assert_array($attributes);
						$childNode->addAttributes($attributes);
						/**
						 * Если $value содержит атрибуты,
						 * то дочерние значения должны содержаться
						 * не непосредственно в $value, а в подмассиве с ключём self::KEY__VALUE
						 */
						$childData = df_a($value, self::KEY__VALUE);
					}
					if (!is_null($childData)) {
						/**
						 * $childData запросто может не быть массивом.
						 * Например, в такой ситуации:
							(
								[_attributes] => Array
									(
										[Код] => 796
										[НаименованиеПолное] => Штука
										[МеждународноеСокращение] => PCE
									)
								[_value] => шт
							)
						 * Здесь $childData — это «шт».
						 */
						if (is_array($childData)) {
							$childNode->importArray($childData, $wrapInCData);
						}
						else {
							$childNode
								->importString(
									/**
									 * null означает, что метод importString
									 * не должен создавать дочерний тэг $key,
									 * а должен добавить текст
									 * в качестве единственного содержимого текущего тэга
									 */
									$key = null
									,$childData
									,$wrapInCData
								)
							;
						}
					}
				}
				else {
					/**
					 * Данный код позволяет импортировать структуры с повторяющимися тегами.
					 * Например, нам надо сформировать такой документ:
						<АдресРегистрации>
							<АдресноеПоле>
								<Тип>Почтовый индекс</Тип>
								<Значение>127238</Значение>
							</АдресноеПоле>
							<АдресноеПоле>
								<Тип>Улица</Тип>
								<Значение>Красная Площадь</Значение>
							</АдресноеПоле>
						</АдресРегистрации>
					 *
					 * Для этого мы вызываем:
					 *
						$this->getDocument()
							->importArray(
								array(
					 				'АдресРегистрации' =>
										array(
											'АдресноеПоле' =>
												array(
													array(
														'Тип' => 'Почтовый индекс'
														,'Значение' => '127238'
													)
													,array(
														'Тип' => 'Улица'
														,'Значение' => 'Красная Площадь'
													)
												)
										)
								)
							)
						;
					 *
					 */
					foreach ($value as $valueItem) {
						/** @var array $valueItem */
						df_assert_array($valueItem);
						$this->importArray(array($key => $valueItem), $wrapInCData);
					}
				}
			}
		}
		return $this;
	}

	/**
	 * @link http://stackoverflow.com/a/3153704
	 * @param mixed $value
	 * @return Df_Varien_Simplexml_Element
	 */
	public function setValue($value) {
		$this->{0} = $value;
		return $this;
	}

	/**
	 * @override
	 * @param string|string[] $path
	 * @return Df_Varien_Simplexml_Element[]
	 */
	public function xpath($path) {
		/** @var mixed[] $arguments */
		$arguments = func_get_args();
		if (1 < count($arguments)) {
			$path = df_concat_xpath($arguments);
		}
		else if (is_array($path)) {
			$path = df_concat_xpath($path);
		}
		df_param_string_not_empty($path, 0);
		return parent::xpath($path);
	}

	/**
	 * @param string|string[] $path
	 * @return Df_Varien_Simplexml_Element[]
	 */
	public function xpathA($path) {
		/** @var string[] $arguments */
		$arguments = func_get_args();
		if (1 < count($arguments)) {
			$path = df_concat_xpath($arguments);
		}
		else if (is_array($path)) {
			$path = df_concat_xpath($path);
		}
		df_param_string_not_empty($path, 0);
		/** @var mixed[] $result */
		$result = parent::xpath($path);
		df_result_array($result);
		return $result;
	}

	/**
	 * @param string|null $key
	 * @param mixed $value
	 * @param array|bool $wrapInCData [optional]
	 * @return Df_Varien_Simplexml_Element
	 */
	private function importString($key, $value, $wrapInCData = array()) {
		/** @var bool $wrapInCDataAll */
		$wrapInCDataAll = is_array($wrapInCData) ? false : !!$wrapInCData;
		$wrapInCData = is_array($wrapInCData) ? $wrapInCData : array();
		/**
		 * null означает, что метод importString
		 * не должен создавать дочерний тэг $key,
		 * а должен добавить текст
		 * в качестве единственного содержимого текущего тэга
		 */
		if (!is_null($key)) {
			df_param_string($key, 0);
		}
		/** @var string $keyAsString */
		$keyAsString =
			is_null($key)
			? $this->getName()
			:
				/**
				 * Раньше тут стояло df_string($key).
				 * Убрал df_string для ускорения модуля Яндекс.Маркет.
				 * Более того, выше стоит проверка df_param_string,
				 * так что если $key не null, то $key гарантированно строка
				 */
				$key
		;
		/**
		 * @var bool $valueIsString
		 */
		$valueIsString = is_string($value);
		/** @var string $valueAsString */
		$valueAsString = null;
		try {
			$valueAsString = $valueIsString ? $value : df_string($value);
		}
		catch(Exception $e) {
			df_error(
				"Не могу сконвертировать значение ключа «%s» в строку.\r\n%s"
				, $keyAsString
				, rm_ets($e)
			);
		}
		/** @var bool $needWrapInCData */
		$needWrapInCData = $wrapInCDataAll;
		if ($valueIsString && $valueAsString) {
			/**
			 * Поддержка синтаксиса
				 array(
					'Представление' =>
						rm_cdata(
							$this->getAddress()->format(
								Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_TEXT
							)
						)
				 )
			 * Обратите внимание, что проверка на синтаксис[[]] должна предшествовать
			 * проверке на принадлежность ключа $keyAsString в массиве $wrapInCData,
			 * потому что при соответствии синтаксису[[]] нам надо удалить из значения символы[[]].
			 * Обратите внимание, что нам нужно выполнить проверку на синтаксис rm_cdata ([[]])
			 * даже при $wrapInCDataAll = true, потому что маркеры [[ и ]] из данных надо удалять.
			 */
			/**
			 * Перед вызовом медленной функции @see preg_match
			 * выполняем более быструю и простую проверку @see rm_contains
			 */
			if (rm_contains($valueAsString, '[[') && rm_contains($valueAsString, ']]')) {
				/** @var string $pattern */
				$pattern = "#\[\[([\s\S]*)\]\]#mu";
				/** @var string[] $matches */
				$matches = array();
				if (1 === preg_match($pattern, $valueAsString, $matches)) {
					$valueAsString = $matches[1];
					$needWrapInCData = true;
				}
			}
			$needWrapInCData = $needWrapInCData || in_array($keyAsString, $wrapInCData);
		}
		/** @var Df_Varien_Simplexml_Element $result */
		$result =
				$needWrapInCData
			?
				(
					is_null($key)
					? $this->setCData($valueAsString)
					: $this->addChildText($keyAsString, $valueAsString)
				)
			:
				(
						is_null($key)
					?
						$this->setValue($valueAsString)
					:
						$this->addChild(
							$keyAsString
							/**
							 * Обратите внимание, что мы намеренно не добавляем htmlspecialchars:
							 * пусть вместо этого источник данных помечает те даннные, которые
							 * могут содержать неразрешённые в качестве содержимого тегов XML
							 * значения посредством @see rm_cdata()
							 */
							,$valueAsString
						)
				)
		;
		df_assert($result instanceof Df_Varien_Simplexml_Element);
		return $result;
	}

	/**
	 * @link http://stackoverflow.com/a/6260295
	 * @param string $text
	 * @return Df_Varien_Simplexml_Element
	 */
	public function setCData($text) {
		/** @var DOMElement $domElement */
		$domElement = dom_import_simplexml($this);
		$domElement->appendChild($domElement->ownerDocument->createCDATASection($text));
		return $this;
	}

	const _CLASS = __CLASS__;
	const KEY__ATTRIBUTES = '_attributes';
	const KEY__VALUE = '_value';

	/**
	 * @todo Этот метод разработал сам, но не тестировал,
	 * потому что после разработки только заметил,
	 * что применять его к стандартным файлам XML (@see Mage::getConfig()) всё равно нельзя:
	 * в стандартном мега-файле, возвращаемом Mage::getConfig(),
	 * одноимённые дочерние узлы уже отсутствуют (перетёрты друг другом).
	 *
	 * Отличие от стандартного метода @see asArray():
	 * если дерево XML содержит несколько одноимённых дочерних узлов,
	 * то родительский метод при конвертации дерева XML в массив
	 * перетирает содержимое дочерних узлов друг другом:
	 * @see Varien_Simplexml_Element::_asArray():
	 * $result[$childName] = $child->_asArray($isCanonical);
	 * Например, дерево XML
		<url>
			<demo>http://fortis.magento-demo.ru/default/</demo>
			<demo>http://fortis.magento-demo.ru/second/</demo>
			<demo>http://fortis.magento-demo.ru/third/</demo>
			<demo>http://fortis.magento-demo.ru/fourth/</demo>
			<demo>http://fortis.magento-demo.ru/fifth/</demo>
			<demo_images_base>http://fortis.infortis-themes.com/demo/</demo_images_base>
			<forum>http://magento-forum.ru/forum/350/</forum>
			<official_site>http://themeforest.net/item/fortis-responsive-magento-theme/1744309?ref=dfediuk</official_site>
		</url>
	 * будет сконвертировано в такой массив:
		[url] => Array
		 (
			 [demo] => http://fortis.magento-demo.ru/fifth/
			 [demo_images_base] => http://fortis.infortis-themes.com/demo/
			 [forum] => http://magento-forum.ru/forum/350/
			 [official_site] => http://themeforest.net/item/fortis-responsive-magento-theme/1744309?ref=dfediuk
		 )
	 * Обратите внимание, что содержимым ключа «demo» массива
	 * стало содержимое последнего (по порядку следования) дочернего узла исходного дерева XML:
	 	 <demo>http://fortis.magento-demo.ru/fifth/</demo>
	 *
	 * Наш метод @see asMultiArray()
	 * при наличии в исходном дереве XML нескольких одноимённых дочерних узлов
	 * добавляет их все в массив, создавая подмассив:
		[url] => Array
		 (
			 [demo] => Array
			  (
				[0] => http://fortis.magento-demo.ru/default/
				[1] => http://fortis.magento-demo.ru/second/
				[2] => http://fortis.magento-demo.ru/third/
				[3] => http://fortis.magento-demo.ru/fourth/
				[4] => http://fortis.magento-demo.ru/fifth/
	 		  )
			 [demo_images_base] => http://fortis.infortis-themes.com/demo/
			 [forum] => http://magento-forum.ru/forum/350/
			 [official_site] => http://themeforest.net/item/fortis-responsive-magento-theme/1744309?ref=dfediuk
		 )
	 *
	 * @param Varien_Simplexml_Element $e
	 * @param bool $isCanonical [optional]
	 * @return array(string => string|array())
	 */
	public static function asMultiArray(Varien_Simplexml_Element $e, $isCanonical = true) {
		/** @var array(string => string|array()) $result */
		$result = array();
		if (!$e->hasChildren()) {
			/** Просто повторяем алгоритм метода @see Varien_Simplexml_Element::_asArray() */
			$result = $e->_asArray($isCanonical);
		}
		else {
			if (!$isCanonical) {
				/** Просто повторяем алгоритм метода @see Varien_Simplexml_Element::_asArray() */
				foreach ($e->attributes() as $attributeName => $attribute) {
					/** @var string $attributeName */
					/** @var Varien_Simplexml_Element $attribute */
					if ($attribute) {
						$result['@'][$attributeName] = (string)$attribute;
					}
				}
			}
			else {
				/**
				 * Обратите внимание, что,
				 * в отличие от метода @see Varien_Simplexml_Element::_asArray(),
				 * мы не можем использовать синтаксис
				 * foreach ($e->children() as $childName => $child) {
				 * потому что при таком синтаксисе мы не сможем получить доступ
				 * ко всем одноимённым дочерним узлам.
				 */
				foreach ($e->children() as $child) {
					/** @var Varien_Simplexml_Element $child */
					/** @var string $childName */
					$childName = $child->getName();
					/** @var array(string => string|array()) $childAsArray */
					$childAsArray = self::asMultiArray($child, $isCanonical);
					if (!isset($result[$childName])) {
						/**
						 * Просто повторяем алгоритм метода
						 * @see Varien_Simplexml_Element::_asArray()
						 */
						$result[$childName] = $childAsArray;
					}
					else {
						// у нас уже есть дочерний узел с данным именем
						if (!is_array($result[$childName])) {
							// преобразуем узел в массив
							$result[$childName] = array($result[$childName]);
						}
						$result[$childName][] = $childAsArray;
					}
				}
			}
		}
		return $result;
	}

	/**
	 * @static
	 * @param string $tag
	 * @param array $attributes[optional]
	 * @return Df_Varien_Simplexml_Element
	 */
	public static function createNode($tag, array $attributes = array()) {
		/** @var Df_Varien_Simplexml_Element $result */
		$result = rm_xml(rm_sprintf('<%s/>', $tag));
		foreach ($attributes as $name => $value) {
			/** @var string $name */
			/** @var string $value */
			$result->addAttribute($name, $value);
		}
		return $result;
	}

	/**
	 * Убрал df_param_string и df_result_string для ускорения работы модуля Яндекс.Маркет
	 * @static
	 * @param string|null $text
	 * @return string
	 */
	public static function markAsCData($text) {return '[[' . $text . ']]';}
}