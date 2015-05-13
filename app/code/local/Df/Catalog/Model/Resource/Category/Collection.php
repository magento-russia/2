<?php
class Df_Catalog_Model_Resource_Category_Collection
	extends Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection {
	/**
	 * Добавляет к коллекции разделы-предки.
	 * Используется модулем «Яндекс.Маркет»:
	 * @see Df_YandexMarket_Model_Yml_Document::getCategories()
	 * Обратите внимание, что вызов этого метода приводит к загрузке коллекции,
	 * то есть потом к коллекции уже нельзя будет добавить фильтры, ограничения и т.п.
	 * @param string[] $attributesToSelect
	 * @return Df_Catalog_Model_Resource_Category_Collection
	 */
	public function addAncestors(array $attributesToSelect) {
		/** @var int[] $ancestorIds */
		$ancestorIds = array();
		foreach ($this as $category) {
			/** @var Df_Catalog_Model_Category $category */
			$ancestorIds = array_merge($ancestorIds, $category->getParentIds());
		}
		/** @var Df_Catalog_Model_Resource_Category_Collection $ancestors */
		$ancestors = self::i();
		$ancestors->addIdFilter($ancestorIds);
		$ancestors->addAttributeToSelect($attributesToSelect);
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
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Catalog_Model_Category::mf(), Df_Catalog_Model_Resource_Category::mf());
	}
	const _CLASS = __CLASS__;

	/** @return Df_Catalog_Model_Resource_Category_Collection */
	public static function i() {return new self;}
}