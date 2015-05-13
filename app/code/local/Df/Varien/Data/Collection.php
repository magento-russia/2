<?php
class Df_Varien_Data_Collection extends Varien_Data_Collection implements Zend_Validate_Interface {
	/**
	 * @override
	 * @return Df_Varien_Data_Collection
	 */
	public function __construct() {
		parent::__construct();
		/**
		 * Родительский класс никак не инициализирует поле @see _isCollectionLoaded
		 */
		$this->_setIsLoaded(false);
	}

	/**
	 * Adding item to item array
	 * @override
	 * @param  Varien_Object $item
	 * @return  Varien_Data_Collection
	 */
	public function addItem(Varien_Object $item) {
		/** @var string $itemClass */
		$itemClass = $this->getItemClass();
		df_assert($item instanceof $itemClass);
		if ($this->isValid($item)) {
			try {
				df_assert(!is_null($item->getId()));
			}
			catch(Exception $e) {
				df_bt();
				df_error(
					'Программист пытается добавить в коллекцию объект без идентификатора.
					<br/>У добавляемых в коллекцию объектов должен быть идентификатор.'
				);
			}
			/**
			 * Родительский класс возбуждает исключительную ситуацию,
			 * когда находит в коллекции идентификатор добавляемого элемента.
			 * Нам такое поведение не нужно, поэтому мы добавляем элемент
			 * только в случае отсутствия идентификатора элемента в коллекции.
			 */
			$itemId = $this->_getItemId($item);
			if (
					is_null($itemId)
				||
					/**
					 * Вызов @see Varien_Data_Collection::getItemById()
					 * может привести к рекурсии, поэтому вместо
					 * is_null($this->getItemById($itemId))
					 * используем !isset($this->_items[$itemId])
					 */
					!isset($this->_items[$itemId])
			) {
				parent::addItem($item);
			}
		}
		return $this;
	}

	/**
	 * Иногда нам надо формировать коллекции из коллекций.
	 * Varien_Data_Collection не наследуется от Varien_Object, * поэтому нам надо обойти запрет на добавление в коллекцию
	 * непотомков Varien_Object
	 *
	 * @param object $item
	 * @return Varien_Data_Collection
	 * @throws Exception
	 */
	public function addItemNotVarienObject($item) {
		$itemId = $item->getId();
		if (!is_null($itemId)) {
			if (isset($this->_items[$itemId])) {
				throw new Exception('Item ('.get_class($item).') with the same id "'.$item->getId().'" already exist');
			}
			$this->_items[$itemId] = $item;
		} else {
			$this->_items[]= $item;
		}
		return $this;
	}

	/**
	 * @param array|Traversable $items
	 * @return Varien_Data_Collection
	 */
	public function addItems($items) {
		foreach ($items as $item) {
			/** @var Varien_Object $item */
			$this->addItem($item);
		}
		return $this;
	}

	/**
	 * Adds a validator to the end of the chain
	 *
	 * If $breakChainOnFailure is true, then if the validator fails, the next validator in the chain, * if one exists, will not be executed.
	 * @override
	 * @param Zend_Validate_Interface $validator
	 * @param boolean				 $breakChainOnFailure
	 * @return Zend_Validate Provides a fluent interface
	 */
	public function addValidator(Zend_Validate_Interface $validator, $breakChainOnFailure = false) {
		$this->getValidator()->addValidator($validator, $breakChainOnFailure);
		return $this;
	}

	/** @return array(string => mixed) */
	public function getData() {return $this->_items;}

	/**
	 * Returns an array of message codes that explain why a previous isValid() call
	 * returned false.
	 *
	 * If isValid() was never called or if the most recent isValid() call
	 * returned true, then this method returns an empty array.
	 *
	 * This is now the same as calling array_keys() on the return value from getMessages().
	 * @override
	 * @return array
	 * @deprecated Since 1.5.0
	 */
	public function getErrors() {return array();}

	/**
	 * Returns an array of messages that explain why the most recent isValid()
	 * call returned false. The array keys are validation failure message identifiers, * and the array values are the corresponding human-readable message strings.
	 *
	 * If isValid() was never called or if the most recent isValid() call
	 * returned true, then this method returns an empty array.
	 * @override
	 * @return array
	 */
	public function getMessages() {return $this->getValidator()->getMessages();}

	/** @return Zend_Validate */
	public function getValidator() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new Zend_Validate();
		}
		return $this->{__METHOD__};
	}

	/**
	 * Returns true if and only if $value meets the validation requirements
	 *
	 * If $value fails validation, then this method returns false, and
	 * getMessages() will return an array of messages that explain why the
	 * validation failed.
	 * @override
	 * @param mixed $value
	 * @return boolean
	 * @throws Zend_Validate_Exception If validation of $value is impossible
	 */
	public function isValid($value) {return $this->getValidator()->isValid($value);}

	/** @return Df_Varien_Data_Collection */
	public function reverse() {
		$this->load();
		$this->_items = array_reverse($this->_items, $preserve_keys = true);
		return $this;
	}

	/**
	 * Удаляет из коллекции элементы $items (если они есть в коллекции)
	 *
	 * @param array|Traversable $items
	 * @return Df_Varien_Data_Collection
	 */
	public function subtract($items) {
		foreach ($items as $item) {
			/** @var Varien_Object $item */
			$itemId = $this->_getItemId($item);
			df_assert(!is_null($itemId));
			$this->removeItemByKey($itemId);
		}
		return $this;
	}

	/**
	 * Стандартный метод Varien_Data_Collection::toArray()
	 * преобразует в массив не только коллекцию, но и элементы коллекции.
	 *
	 * Наш метод Df_Varien_Data_Collection::toArrayOfObjects
	 * преобразует в массив коллекцию, но элементы коллекции массиве остаются объектами.
	 * @return array
	 */
	public function toArrayOfObjects() {
		/** @var array $result */
		$result = array();
		foreach ($this as $item) {
			/** @var Varien_Object $item */
			$result[]= $item;
		}
		df_result_array($result);
		return $result;
	}

	/** @return string */
	protected function getItemClass() {return $this->_itemObjectClass;}

	/**
	 * @static
	 * @param array|Traversable $items
	 * @param string $class[optional]
	 * @return Df_Varien_Data_Collection
	 */
	public static function createFromCollection($items, $class = null) {
		if (!is_null($class)) {
			df_param_string($class, 1);
		}
		if (is_null($class)) {
			$class = __CLASS__;
		}
		/** @var Df_Varien_Data_Collection $result */
		$result = new $class();
		df_assert($result instanceof Df_Varien_Data_Collection);
		$result->addItems($items);
		return $result;
	}

	const _CLASS = __CLASS__;

	/**
	 * @var Traversable|Mage_Core_Model_Abstract[] $collection
	 * @return void
	 */
	public static function saveModified($collection) {
		rm_admin_begin();
		foreach ($collection as $entity) {
			/** @var Mage_Core_Model_Abstract $entity */
			/**
			 * @see Varien_Data_Collection
			 * может содержать не только объекты класса @see Mage_Core_Model_Abstract,
			 * однако операция сохранения имеет смысл только для @see Mage_Core_Model_Abstract
			 */
			df_assert($entity instanceof Mage_Core_Model_Abstract);
			/**
			 * Обратите внимание, что сохранение в Magento — интеллектуальное:
			 * Magento сохраняет только те объекты, свойства которых изменились:
			 * hasDataChanges = true.
			 * Ну, и мы дополнительно вызываем hasDataChanges: не помешает.
			 */
			if ($entity->hasDataChanges()) {
				$entity->save();
			}
		}
		rm_admin_end();
	}

	/**
	 * @var Varien_Data_Collection $collection
	 * @return void
	 */
	public static function unsetDataChanges(Varien_Data_Collection $collection) {
		foreach ($collection as $entity) {
			/** @var Varien_Object $entity */
			$entity->setDataChanges(false);
		}
	}
}