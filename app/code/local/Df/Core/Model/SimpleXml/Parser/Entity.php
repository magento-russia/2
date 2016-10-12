<?php
class Df_Core_Model_SimpleXml_Parser_Entity extends Df_Core_Model {
	/**
	 * Я так понимаю, этот метод нужен для 1С:Управление торговлей?
	 * В то же время, есть ситуации, когда данный класс вовсе не является абстрактным
	 * (@see Df_Kkb_Model_Response_Payment), просто методы
	 * @see Df_Core_Model_SimpleXml_Parser_Entity::getId() и
	 * @see Df_Core_Model_SimpleXml_Parser_Entity::getName() не используются.
	 * @return string|null
	 */
	public function getName() {return $this->getId();}

	/**
	 * @param string $path
	 * @param bool $throw [optional]
	 * @return float|null
	 */
	public function descendF($path, $throw = false) {
		return $this->descendWithCast($path, 'rm_float', 'вещественное', $throw);
	}

	/**
	 * @param string $path
	 * @param bool $throw [optional]
	 * @return int|null
	 */
	public function descendI($path, $throw = false) {
		return $this->descendWithCast($path, 'rm_int', 'целое', $throw);
	}

	/**
	 * @param string $path
	 * @param bool $throw [optional]
	 * @return string|null
	 * @throws Df_Core_Exception_Client
	 */
	public function descendS($path, $throw = false) {
		if (!isset($this->{__METHOD__}[$path])) {
			/** @var Df_Varien_Simplexml_Element|bool $element */
			$element = $this->e()->descend($path);
			/** @var bool $found */
			$found = !is_null($element);
			if (!$found && $throw) {
				df_error('В документе XML отсутствует путь «%s».', $path);
			}
			$this->{__METHOD__}[$path] = rm_n_set($found ? (string)$element : null);
		}
		return rm_n_get($this->{__METHOD__}[$path]);
	}

	/** @return Df_Varien_Simplexml_Element */
	public function e() {return $this->getSimpleXmlElement();}

	/**
	 * @param string $name
	 * @param string|int|array|float|null $defaultValue[optional]
	 * @return mixed
	 */
	public function getAttribute($name, $defaultValue = null) {
		df_param_string_not_empty($name, 0);
		/** @var string|int|array|float|null $result */
		$result = $this->getAttributeInternal($name);
		return !is_null($result) ? $result : $defaultValue;
	}

	/**
	 * @param string $childName
	 * @return bool
	 */
	public function isChildComplex($childName) {
		df_param_string_not_empty($childName, 0);
		if (!isset($this->{__METHOD__}[$childName])) {
			/** @var Df_Varien_Simplexml_Element|null $child */
			$child = $this->getChildSingleton($childName, $isRequired = false);
			/**
			 * 2015-08-15
			 * Нельзя здесь использовать count($child->children()),
			 * потому что класс @see SimpleXmlElement не реализует интерфейс @see Iterator,
			 * а реализует только интерфейс @see Traversable.
			 * http://php.net/manual/class.iterator.php
			 * http://php.net/manual/class.traversable.php
			 * http://php.net/manual/en/simplexmlelement.count.php
			 */
			$this->{__METHOD__}[$childName] = $child && $child->children()->count();
		}
		return $this->{__METHOD__}[$childName];
	}

	/**
	 * Возвращает единственного ребёнка с указанными именем.
	 * Контролирует отсутствие других детей с указанными именем.
	 * @param string $childName
	 * @param bool $isRequired [optional]
	 * @return Df_Varien_Simplexml_Element|null
	 */
	public function getChildSingleton($childName, $isRequired = false) {
		if (!isset($this->{__METHOD__}[$childName])) {
			df_param_string_not_empty($childName, 0);
			/** @var Df_Varien_Simplexml_Element|null $result */
			if (!rm_xml_child_exists($this->e(), $childName)) {
				if ($isRequired) {
					df_error_internal(
						"Требуемый узел «%s» отсутствует в документе:\r\n%s"
						, $childName
						, rm_xml_mark($this->e()->asNiceXml())
					);
				}
				else {
					$result = null;
				}
			}
			else {
				/** @var Df_Varien_Simplexml_Element[] $childNodes */
				$childNodes = $this->e()->{$childName};
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
				 * @link http://stackoverflow.com/a/16100099
				 * Класс SimplexmlElement — вообще один из самых необычных классов PHP.
				 */
				df_assert_eq(1, count($childNodes));
				$result = $childNodes[0];
				df_assert($result instanceof Df_Varien_Simplexml_Element);
			}
			$this->{__METHOD__}[$childName] = rm_n_set($result);
		}
		return rm_n_get($this->{__METHOD__}[$childName]);
	}

	/**
	 * @param string $paramName
	 * @param string|int|float|null $defaultValue[optional]
	 * @return string|int|float|null
	 */
	public function getEntityParam($paramName, $defaultValue = null) {
		/** @var string|int|float|null $result */
		$result = $this->getEntityParamInternal($paramName);
		return is_null($result) ? $defaultValue : $result;
	}

	/**
	 * @param string $paramName
	 * @param string|int|array|float|null $defaultValue[optional]
	 * @return mixed
	 */
	public function getEntityParamArray($paramName, $defaultValue = null) {
		return df_a($this->getAsCanonicalArray(), $paramName, $defaultValue);
	}

	/**
	 * @param string $childName
	 * @return bool
	 */
	public function isChildExist($childName) {
		if (!isset($this->{__METHOD__}[$childName])) {
			$this->{__METHOD__}[$childName] = rm_xml_child_exists($this->e(), $childName);
		}
		return $this->{__METHOD__}[$childName];
	}

	/**
	 * От разультата этого метода зависит добавление данного объекта
	 * в коллекцию Df_Core_Model_SimpleXml_Parser_Collection
	 * @see Df_Core_Model_SimpleXml_Parser_Collection::getItems()
	 * @return bool
	 */
	public function isValid() {return true;}

	/** @return Df_Varien_Simplexml_Element */
	public function getSimpleXmlElement() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Varien_Simplexml_Element $result */
			$result = $this->_getData(self::P__SIMPLE_XML);
			if (is_string($result)) {
				$result = rm_xml($result);
			}
			if (!($result instanceof Df_Varien_Simplexml_Element)) {
				Mage::log(gettype($result));
				Mage::log(get_class($result));
			}
			df_assert($result instanceof Df_Varien_Simplexml_Element);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => mixed) */
	protected function getAsCanonicalArray() {return $this->e()->asCanonicalArray();}

	/**
	 * @param string $path
	 * @param callable $castFunction
	 * @param string $castName
	 * @param bool $throw [optional]
	 * @return mixed|null
	 */
	private function descendWithCast($path, $castFunction, $castName, $throw = false) {
		/** @var string|null $resultAsText */
		$resultAsText = $this->descendS($path, $throw);
		/** @var mixed|null $result */
		if (!is_null($resultAsText) && !df_empty_string($resultAsText)) {
			$result = call_user_func($castFunction, $resultAsText);
		}
		else {
			if ($throw) {
				df_error(
					'В документе XML по пути «%s» требуется %s число, однако там пусто.'
					, $castName
					, $path
				);
			}
			else {
				$result = null;
			}
		}
		return $result;
	}

	/**
	 * @param string $name
	 * @return string|int|array|float|null
	 */
	private function getAttributeInternal($name) {
		if (!isset($this->{__METHOD__}[$name])) {
			$this->{__METHOD__}[$name] = rm_n_set($this->e()->getAttribute($name));
		}
		return rm_n_get($this->{__METHOD__}[$name]);
	}

	/**
	 * @param string $paramName
	 * @return string|int|float|null
	 */
	private function getEntityParamInternal($paramName) {
		if (!isset($this->{__METHOD__}[$paramName])) {
			$this->{__METHOD__}[$paramName] = rm_n_set(rm_xml_child_simple($this->e(), $paramName));
		}
		return rm_n_get($this->{__METHOD__}[$paramName]);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		// параметр PARAM__SIMPLE_XML может быть как объектом, так и строкой.
	}
	const _CLASS = __CLASS__;
	const P__SIMPLE_XML = 'simple_xml';
	/**
	 * Обратите внимание, что этот метод нельзя называть i(),
	 * потому что от класса Df_Core_Model_SimpleXml_Parser_Entity наследуются другие классы,
	 * и у наследников спецификация метода i() другая, что приводит к сбою интерпретатора PHP:
	 * «Strict Notice: Declaration of Df_Licensor_Model_File::i()
	 * should be compatible with that of Df_Core_Model_SimpleXml_Parser_Entity::i()»
	 * @static
	 * @param Df_Varien_Simplexml_Element|string $simpleXml
	 * @return Df_Core_Model_SimpleXml_Parser_Entity
	 */
	public static function simple($simpleXml) {return self::_c($simpleXml, __CLASS__);}

	/**
	 * @static
	 * @param Df_Varien_Simplexml_Element|string $simpleXml
	 * @param string $class
	 * @return Df_Core_Model_SimpleXml_Parser_Entity
	 */
	protected static function _c($simpleXml, $class) {
		/** @var Df_Core_Model_SimpleXml_Parser_Entity $result */
		$result = new $class(array(self::P__SIMPLE_XML => $simpleXml));
		df_assert($result instanceof Df_Core_Model_SimpleXml_Parser_Entity);
		return $result;
	}
}