<?php
abstract class Df_Core_Xml_Parser_Collection
	extends Df_Core_Xml_Parser_Entity implements IteratorAggregate, Countable {
	/**
	 * @used-by getImportEntitiesAsSimpleXMLElementArray()
	 * @return string|string[]
	 */
	abstract protected function itemPath();
	/**
	 * @override
	 * @return int
	 */
	public function count() {return count($this->getItems());}
	/**
	 * Убрал @see df_assert()
	 * ради ускорения работы метода @see Df_Localization_Realtime_Dictionary::translate()
	 * @param int|string $id
	 * @return Df_Core_Xml_Parser_Entity|null
	 */
	public function findById($id) {
		$this->getItems();
		return df_a($this->_mapFromIdToEntity, $id);
	}
	/**
	 * @param string $name
	 * @return Df_Core_Xml_Parser_Entity|null
	 */
	public function findByName($name) {
		$this->getItems();
		return rm_first($this->findByNameAll($name));
	}
	/**
	 * @param string $name
	 * @return Df_Core_Xml_Parser_Entity[]
	 */
	public function findByNameAll($name) {
		$this->getItems();
		/** @noinspection PhpParamsInspection */
		return df_nta(df_a($this->_mapFromNameToEntity, $name));
	}

	/**
	 * Для коллекций особая логика этого метода необязательна
	 * @override
	 * @return string
	 */
	public function getId() {return get_class($this);}

	/** @return Df_Core_Xml_Parser_Entity[] */
	final public function getItems() {
		if (!isset($this->_items)) {
			/** @var Df_Core_Xml_Parser_Entity[] $result */
			$this->_items = array();
			$this->initItems();
			$this->postInitItems($this->_items);
		}
		return $this->_items;
	}

	/**
	 * @override
	 * @return Traversable
	 */
	public function getIterator() {return new ArrayIterator($this->getItems());}

	/** @return bool */
	public function hasItems() {return !!$this->getItems();}

	/**
	 * @param Df_Core_Xml_Parser_Entity $item
	 * @return void
	 */
	protected function addItem(Df_Core_Xml_Parser_Entity $item) {
		$this->_items[]= $item;
		$this->_mapFromIdToEntity[$item->getId()] = $item;
		$this->_mapFromNameToEntity[$item->getName()][] = $item;
	}

	/**
	 * @param Df_Core_Sxe $e
	 * @return Df_Core_Xml_Parser_Entity
	 */
	protected function createItem(Df_Core_Sxe $e) {
		return Df_Core_Xml_Parser_Entity::entity($e, $this->itemClassAdvanced($e), $this->itemParams());
	}

	/** @return Df_Core_Sxe[] */
	protected function getImportEntitiesAsSimpleXMLElementArray() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->e()->xpathA($this->itemPath());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-08-15
	 * Не объявляем этот метод абстрактным средствами языка,
	 * потому что потомки не обязаны его перекрывать:
	 * они могут вместо этого метода перекрыть метод @used-by itemClassAdvanced()
	 * @abstract
	 * @used-by itemClassAdvanced()
	 * @return string
	 */
	protected function itemClass() {df_abstract(__METHOD__);}

	/**
	 * 2015-08-15
	 * Перекрывайте этот метод, когда класс элемента должен зависеть от ветки XML.
	 * @used-by createItem()
	 * @param Df_Core_Sxe $e
	 * @return string
	 */
	protected function itemClassAdvanced(Df_Core_Sxe $e) {return $this->itemClass();}

	/**
	 * Позволяет добавлять к создаваемым элементам
	 * дополнительные, единые для всех элементов, параметры
	 * @return array(string => mixed)
	 */
	protected function itemParams() {return array();}

	/**
	 * @used-by getItems()
	 * @return void
	 */
	protected function initItems() {
		foreach ($this->getImportEntitiesAsSimpleXMLElementArray() as $e) {
			/** @var Df_Core_Sxe $e */
			/** @var Df_Core_Xml_Parser_Entity $item */
			$item = $this->createItem($e);
			if ($item->isValid()) {
				$this->addItem($item);
			}
		}
	}

	/**
	 * 2015-08-04
	 * Обратите внимение, что у нас не должно возникать проблемы с производительностью
	 * из-за передачи в этот метод массива со всеми элементами.
	 * PHP ведёт себя интеллектуально: если метод не меняет содержимое массива,
	 * то PHP использует в методе орининальный массив (т.е. как бы передаёт массив по ссылке),
	 * и только если метод меняет массив, тогда PHP создаёт копию исходного массива.
	 * http://stackoverflow.com/a/9740541
	 *
	 * Наш метод и его потомки менять напрямую содержимое массива не должны:
	 * для добавления новых элементов в массив должен использоваться метод @see addItem(),
	 * а для удаления (вернее, недобавления) элементов из массива должен перекрываться метод
	 * @see Df_Core_Xml_Parser_Entity::isValid()
	 *
	 * @used-by getItems()
	 * @see Df_1C_Cml2_Import_Data_Collection_Offers::postInitItems()
	 * @see Df_1C_Cml2_Import_Data_Collection_OfferPart_OptionValues::postInitItems()
	 * @see Df_1C_Cml2_Import_Data_Collection_ProductPart_AttributeValues_Custom::postInitItems()
	 * @param Df_Core_Xml_Parser_Entity[] $items
	 * @return void
	 */
	protected function postInitItems(array $items) {}

	/**
	 * 2015-08-04
	 * Временно сделал это свойство protected.
	 * Потом надо будет переработать обработку заказов.
	 * @used-by Df_1C_Cml2_Import_Data_Collection_Order_Items::initItems()
	 * @var Df_Core_Xml_Parser_Entity
	 */
	protected $_items;
	/** @var array(int|string => Df_Core_Xml_Parser_Entity) */
	private $_mapFromIdToEntity = array();
	/** @var array(string => Df_Core_Xml_Parser_Entity[]) */
	private $_mapFromNameToEntity = array();
	const _C = __CLASS__;
}