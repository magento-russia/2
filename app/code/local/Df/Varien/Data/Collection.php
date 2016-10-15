<?php
class Df_Varien_Data_Collection extends Varien_Data_Collection {
	/**
	 * @override
	 * @return Df_Varien_Data_Collection
	 */
	public function __construct() {
		parent::__construct();
		/** родительский класс никак не инициализирует поле @see _isCollectionLoaded */
		$this->_setIsLoaded(false);
		/**
		 * 2015-08-15
		 * Переменную @uses _itemObjectClass мы никак не используем,
		 * но пусть уж она будет инициализирована правильным значением
		 */
		$this->_itemObjectClass = $this->itemClass();
	}

	/**
	 * 2015-02-11
	 * Добавляет к коллекции элементы из коллекции $source
	 * @param Df_Varien_Data_Collection $source
	 * @return void
	 */
	public function add(Df_Varien_Data_Collection $source) {$source->walk(array($this, 'addItem'));}

	/**
	 * Adding item to item array
	 * @override
	 * @param  Varien_Object $item
	 * @return  Varien_Data_Collection
	 */
	public function addItem(Varien_Object $item) {
		/** @var string $itemClass */
		$itemClass = $this->itemClass();
		df_assert($item instanceof $itemClass);
		try {
			df_assert(!is_null($item->getId()));
		}
		catch (Exception $e) {
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
		return $this;
	}

	/**
	 * Иногда нам надо формировать коллекции из коллекций.
	 * @see Varien_Data_Collection не наследуется от @see Varien_Object,
	 * поэтому нам надо обойти запрет на добавление в коллекцию
	 * непотомков Varien_Object
	 * @used-by Df_Cms_Model_ContentsMenu_Collection::getPosition()
	 * @param object $item
	 * @return Df_Varien_Data_Collection
	 * @throws Exception
	 */
	public function addItemNotVarienObject($item) {
		$itemId = $item->getId();
		if (!is_null($itemId)) {
			if (isset($this->_items[$itemId])) {
				df_error('Item ('.get_class($item).') with the same id "'.$item->getId().'" already exist');
			}
			$this->_items[$itemId] = $item;
		} else {
			$this->_items[]= $item;
		}
		return $this;
	}


	/** @return bool */
	public function hasItems() {return !!$this->count();}

	/**
	 * @param int[]|string[] $keys
	 * @return Df_Varien_Data_Collection
	 */
	public function removeItemsByKeys(array $keys) {
		$this->load();
		$this->_items = array_diff_key($this->_items, array_fill_keys($keys, null));
		return $this;
	}

	/** @return Df_Varien_Data_Collection */
	public function reverse() {
		$this->load();
		$this->_items = array_reverse($this->_items, $preserve_keys = true);
		return $this;
	}

	/**
	 * @used-by __construct()
	 * @used-by addItem()
	 * @return string
	 */
	protected function itemClass() {return 'Varien_Object';}

	/**
	 * 2015-02-08
	 * Намеренно в качестве параметра требуем не callable,
	 * а название статичного внутреннего метода:
	 * такие ограничения унифицируют архитектуру системы
	 * и упрощают код вызова данного метода.
	 * @used-by Df_Checkout_Model_Collection_Ergonomic_Address_Field::orderByWeight()
	 * @used-by Df_Localization_Onetime_Processor_Collection::loadInternal()
	 * @param string $staticInternalMethod
	 * @return void
	 */
	protected function uasort($staticInternalMethod) {
		$this->load();
		/**
		 * Подавляем предупреждение «Array was modified by the user comparison function».
		 * http://stackoverflow.com/a/10985500
		 * «There is a PHP bug that can cause this warning, even if you don't change the array.»
		 */
		@uasort($this->_items, array(get_class($this), $staticInternalMethod));
	}

	/**
	 * @param Traversable|Mage_Core_Model_Abstract[] $collection
	 * @return void
	 * @throws Df_Core_Exception_Batch|Exception
	 */
	public static function saveModified($collection) {
		/** @var Df_Core_Exception_Batch $batchException */
		$batchException = new Df_Core_Exception_Batch();
		df_admin_begin();
		try {
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
					try {
						$entity->save();
					}
					catch (Exception $e) {
						$batchException->addException(new Df_Core_Exception_Entity($entity, $e));
					}
				}
			}
		}
		catch (Exception $e) {
			df_admin_end();
			throw $e;
		}
		df_admin_end();
		$batchException->throwIfNeeed();
	}

	/**
	 * @var Varien_Data_Collection $collection
	 * @return void
	 */
	public static function unsetDataChanges(Varien_Data_Collection $collection) {
		/** @uses Varien_Object::setDataChanges() */
		$collection->walk('setDataChanges', array(false));
	}
}