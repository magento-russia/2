<?php
abstract class Df_Core_Model_SimpleXml_Parser_Collection
	extends Df_Core_Model_SimpleXml_Parser_Entity
	implements IteratorAggregate, Countable {
	/** @return string */
	abstract protected function getItemClass();
	/** @return string[] */
	abstract protected function getItemsXmlPathAsArray();
	/**
	 * @override
	 * @return int
	 */
	public function count() {return count($this->getItems());}
	/**
	 * Убрал @see df_assert()
	 * ради ускорения работы метода @see Df_Localization_Model_Realtime_Dictionary::translate()
	 * @param string $id
	 * @return Df_Core_Model_SimpleXml_Parser_Entity|null
	 */
	public function findById($id) {return df_a($this->getMapFromIdToEntity(), $id);}
	/**
	 * @param string $name
	 * @return Df_Core_Model_SimpleXml_Parser_Entity|null
	 */
	public function findByName($name) {return df_a($this->getMapFromNameToEntity(), $name);}
	/**
	 * @param string $name
	 * @return Df_Core_Model_SimpleXml_Parser_Entity[]
	 */
	public function findByNameAll($name) {
		df_param_string($name, 0);
		/** @var Df_Core_Model_SimpleXml_Parser_Entity|null $result */
		$result = array();
		foreach ($this->getItems() as $entity) {
			/** @var Df_Core_Model_SimpleXml_Parser_Entity $entity */
			if ($name === $entity->getName()) {
				$result[]= $entity;
			}
		}
		return $result;
	}

	/**
	 * Для коллекций особая логика этого метода необязательна
	 * @override
	 * @return string
	 */
	public function getId() {return get_class($this);}

	/** @return Df_Core_Model_SimpleXml_Parser_Entity[] */
	public function getItems() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Core_Model_SimpleXml_Parser_Entity[] $result */
			$result = new ArrayObject(array());
			foreach ($this->getImportEntitiesAsSimpleXMLElementArray() as $entityAsSimpleXMLElement) {
				/** @var Df_Varien_Simplexml_Element $entityAsSimpleXMLElement */
				/** @var Df_Core_Model_SimpleXml_Parser_Entity $item */
				$item = $this->createItemFromSimpleXmlElement($entityAsSimpleXMLElement);
				if ($item->isValid()) {
					$result[]= $this->createItemFromSimpleXmlElement($entityAsSimpleXMLElement);
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Traversable */
	public function getIterator() {return $this->getItems();}

	/** @return bool */
	public function hasItems() {return 0 < $this->count();}

	/**
	 * @param Df_Core_Model_SimpleXml_Parser_Entity $item
	 * @return Df_Core_Model_SimpleXml_Parser_Collection
	 */
	protected function addItem(Df_Core_Model_SimpleXml_Parser_Entity $item) {
		$this->{__CLASS__ . '::getItems'}[] = $item;
		$this->{__CLASS__ . '::getMapFromIdToEntity'}[$item->getId()] = $item;
		$this->{__CLASS__ . '::getMapFromNameToEntity'}[$item->getName()] = $item;
		return $this;
	}

	/**
	 * @param Df_Varien_Simplexml_Element $entityAsSimpleXMLElement
	 * @return Df_Core_Model_SimpleXml_Parser_Entity
	 */
	protected function createItemFromSimpleXmlElement(
		Df_Varien_Simplexml_Element $entityAsSimpleXMLElement
	) {
		/** @var string $class */
		$class = $this->getItemClass();
		/** @var Df_Core_Model_SimpleXml_Parser_Entity $result */
		$result = new $class(array_merge(
			array(Df_Core_Model_SimpleXml_Parser_Entity::P__SIMPLE_XML => $entityAsSimpleXMLElement)
			,$this->getItemParamsAdditional()
		));
		df_assert($result instanceof Df_Core_Model_SimpleXml_Parser_Entity);
		return $result;
	}

	/** @return Df_Varien_Simplexml_Element[] */
	protected function getImportEntitiesAsSimpleXMLElementArray() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->e()->xpathA($this->getItemsXmlPathAsArray());
		}
		return $this->{__METHOD__};
	}

	/**
	 * Позволяет добавлять к создаваемым элементам
	 * дополнительные, единые для всех элементов, параметры
	 * @return array(string => mixed)
	 */
	protected function getItemParamsAdditional() {return array();}

	/** @return Df_Core_Model_SimpleXml_Parser_Entity[] */
	private function getMapFromIdToEntity() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Core_Model_SimpleXml_Parser_Entity[] $result */
			$result = array();
			foreach ($this->getItems() as $entity) {
				/** @var Df_Core_Model_SimpleXml_Parser_Entity $entity */
				$result[$entity->getId()] = $entity;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Core_Model_SimpleXml_Parser_Entity[] */
	private function getMapFromNameToEntity() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Core_Model_SimpleXml_Parser_Entity[] $result */
			$result = array();
			foreach ($this->getItems() as $entity) {
				/** @var Df_Core_Model_SimpleXml_Parser_Entity $entity */
				$result[$entity->getName()] = $entity;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
}