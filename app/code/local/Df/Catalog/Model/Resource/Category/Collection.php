<?php
class Df_Catalog_Model_Resource_Category_Collection
	extends Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection {
	/**
	 * Добавляет к коллекции разделы-предки.
	 * @used-by Df_Catalog_Model_XmlExport_Catalog::getCategories()
	 * Обратите внимание, что вызов этого метода приводит к загрузке коллекции,
	 * то есть потом к коллекции уже нельзя будет добавить фильтры, ограничения и т.п.
	 * @param string|string[] $attributesToSelect [optional]
	 * @return Df_Catalog_Model_Resource_Category_Collection
	 */
	public function addAncestors($attributesToSelect = '*') {
		/** @var int[] $ancestorIds */
		$ancestorIds = array();
		foreach ($this as $category) {
			/** @var Df_Catalog_Model_Category $category */
			$ancestorIds = array_merge($ancestorIds, $category->getParentIds());
		}
		/** @var Df_Catalog_Model_Resource_Category_Collection $ancestors */
		$ancestors = new self;
		$ancestors->setStoreId($this->getStoreId());
		$ancestors->addIdFilter(array_unique($ancestorIds));
		$ancestors->addAttributeToSelect($attributesToSelect);
		$ancestors->setDisableFlat($this->getDisableFlat());
		foreach ($ancestors as $ancestor) {
			/** @var Df_Catalog_Model_Category $ancestor */
			/**
			 * Обратите внимание, что в Magento у видимого администратору корневого раздела
			 * есть ещё невидимый раздел-родитель.
			 *
			 * Например, в состоянии сразу после установки Российской сборки Magento
			 * у раздела «корневой раздел» (который видится администратору как корневой)
			 * есть почему-то еще невидимый администратору раздел-родитель «Root Category».
			 *
			 * Eго мы тоже добавляем в коллекцию.
			 * (Модулю «Яндекс.Маркет» это важно,
			 * потому что иначе в файле YML свойство parent_id раздела «корневой раздел»
			 * будет ссылаться на отсутствующий в этом файле YML раздел).
			 */
			if (!$this->getItemById($ancestor->getId())) {
				$this->addItem($ancestor);
			}
		}
		return $this;
	}

	/**
	 * @used-by getNewEmptyItem()
	 * Дублируем сюда реализацию родительского метода
	 * @see Mage_Catalog_Model_Resource_Category_Collection::getDisableFlat(),
	 * потому что данный метод отсутствует в Magento CE 1.4.0.1
	 * @override
	 * @return bool
	 */
	public function getDisableFlat() {return $this->_disableFlat;}

	/**
	 * Дублируем сюда реализацию родительского метода
	 * @see Mage_Catalog_Model_Resource_Category_Collection::getNewEmptyItem(),
	 * потому что данный метод отсутствует в Magento CE 1.4.0.1
	 * @override
	 * @return Df_Catalog_Model_Category
	 */
	public function getNewEmptyItem() {
		return new $this->_itemObjectClass(array('disable_flat' => $this->getDisableFlat()));
	}

	/**
	 * Дублируем сюда реализацию родительского метода
	 * @see Mage_Catalog_Model_Resource_Category_Collection::setDisableFlat(),
	 * потому что данный метод отсутствует в Magento CE 1.4.0.1
	 * @override
	 * @var bool $flag
	 * @return Df_Catalog_Model_Resource_Category_Collection
	 */
	public function setDisableFlat($flag) {
		$this->_disableFlat = (bool)$flag;
		return $this;
	}

	/**
	 * 2015-02-09
	 * Родительский метод: @see Df_Catalog_Model_Resource_Category_Collection::setEntity()

	 * @override
	 * @param Mage_Eav_Model_Entity_Abstract $entity
	 * @return Df_Catalog_Model_Resource_Category_Collection
	 */
	public function setEntity($entity) {df_should_not_be_here(__METHOD__);}

	/**
	 * 2015-02-09
	 * Родительский метод не вызываем намеренно.
	 * Родительский метод: @see Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection::_init()
	 * @override
	 * @param string $model
	 * @param string|null $entityModel [optional]
	 * @return Df_Catalog_Model_Resource_Category_Collection
	 */
	protected function _init($model, $entityModel = null) {
		$this->_itemObjectClass = Df_Catalog_Model_Category::class;
		$this->_entity = Df_Catalog_Model_Resource_Category::s();
		return $this;
	}

	/**
	 * Дублируем сюда описание родительского поля
	 * @see Mage_Catalog_Model_Resource_Category_Collection::_disableFlat,
	 * потому что данное поле отсутствует в Magento CE 1.4.0.1
	 * @override
	 * @var bool
	 */
	protected $_disableFlat = false;


}