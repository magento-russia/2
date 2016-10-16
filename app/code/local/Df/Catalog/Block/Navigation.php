<?php
class Df_Catalog_Block_Navigation extends Mage_Catalog_Block_Navigation {
	/**
	 * Цель перекрытия —
	 * предоставление возможности сторонним модулям
	 * добавлять свои пункты в товарное меню
	 * посредством подписки на создаваемое этим методом
	 * оповещение «df_menu_top_add_submenu».
	 * @override
	 * @return Varien_Data_Tree_Node_Collection|Mage_Catalog_Model_Resource_Category_Collection|Varien_Data_Collection|array
	 */
	public function getStoreCategories() {
		/** @var Varien_Data_Tree_Node_Collection|Mage_Catalog_Model_Resource_Category_Collection|Varien_Data_Collection|array $result */
		$result = parent::getStoreCategories();
		/** @var bool $isNodeCollection */
		$isNodeCollection = $result instanceof Varien_Data_Tree_Node_Collection;
		/** @var bool $isArray */
		$isArray = is_array($result);
		df_assert(
				$isArray
			||
				$isNodeCollection
			||
				df_h()->catalog()->check()->categoryCollection($result)
			||
				$result instanceof Varien_Data_Collection
		);
		Mage::dispatchEvent('df_menu_top_add_submenu', array('menu' => $this->getAdditionalRoot()));
		if ($isArray || $isNodeCollection) {
			foreach ($this->getAdditionalRoot()->getNodes() as $node) {
				/** @var Varien_Data_Tree_Node $node */
				if ($isNodeCollection) {
					$result->add($node);
				}
				else if ($isArray) {
					$result[]= $node;
				}
			}
		}
		return $result;
	}

	/** @return Varien_Data_Tree */
	protected function getAdditionalRoot() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new Varien_Data_Tree();
		}
		return $this->{__METHOD__};
	}
}