<?php
class Df_Catalog_Block_Category_Navigation extends Df_Core_Block_Template {
	/**
	 * @return
	 *			Mage_Catalog_Model_Resource_Category_Collection
	 * 		|	Mage_Catalog_Model_Resource_Category_Flat_Collection
	 *		|	Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection
	 * 		|	Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat_Collection
	 *		|	array
	 */
	public function getItems() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * @var
			 *			Mage_Catalog_Model_Resource_Category_Collection
			 * 		|	Mage_Catalog_Model_Resource_Category_Flat_Collection
			 *		|	Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection
			 * 		|	Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat_Collection
			 *		|	array
			 */
			$result = array();
			if ($this->hasItems()) {
				/** @var Mage_Catalog_Model_Resource_Category_Collection|Mage_Catalog_Model_Resource_Category_Flat_Collection $result */
				$result = $this->getCurrentCategory()->getCollection();
				$result->addAttributeToSelect('*');
				$result->addAttributeToFilter('is_active', 1);
				$result->addIdFilter($this->getCurrentCategory()->getChildren());
				$result->setOrder('position', 'ASC');
				/**
				 * К сожалению, нельзя здесь для проверки публичности метода
				 * использовать is_callable, * потому что наличие Varien_Object::__call
				 * приводит к тому, что is_callable всегда возвращает true.
				 */
				if (method_exists($result, 'joinUrlRewrite')) {
					call_user_func(array($result, 'joinUrlRewrite'));
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return Mage_Core_Block_Abstract
	 */
	protected function _prepareLayout() {
		if (df_cfg()->catalog()->navigation()->getEnabled()) {
			/**
			 * Добавление *.css и *.js именно здесь, а не через файлы layout,
			 * даёт нам большую гибкость: нам не нужно думать, какие handle обрабатывать
			 */
			$this->addClientResourcesToThePage();
		}
		parent::_prepareLayout();
	}

	/**
	 * @override
	 * @return string|string[]
	 */
	protected function getCacheKeyParamsAdditional() {return $this->getCurrentCategory()->getId();}

	/**
	 * @override
	 * @return string|null
	 */
	protected function getDefaultTemplate() {return self::DEFAULT_TEMPLATE;}

	/**
	 * @override
	 * @return bool
	 */
	protected function needToShow() {
		/** @var bool $result */
		return
				parent::needToShow()
			&&
				df_enabled(Df_Core_Feature::TWEAKS)
			&&
				df_cfg()->catalog()->navigation()->getEnabled()
			&&
				$this->hasItems()
		;
	}

	/** @return Df_Catalog_Model_Category */
	private function getCurrentCategory() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Catalog_Model_Category $result */
			$result = $this->cfg(self::P__CATEGORY);
			if (!$result) {
				$result = rm_state()->getCurrentCategory();
			}
			df_assert($result instanceof Df_Catalog_Model_Category);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Catalog_Block_Category_Navigation */
	private function addClientResourcesToThePage() {
		if (!is_null($this->getBlockHead())) {
			/** Блок может отсутствовать на некоторых страницах. Например, при импорте товаров. */
			$this
				->getBlockHead()
					->addCss('df/common/reset.css')
					->addCss('df/catalog/category/navigation.css')
			;
		}
		return $this;
	}

	/** @return bool */
	private function hasItems() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getCurrentCategory() && $this->getCurrentCategory()->hasChildren()
			;
		}
		return $this->{__METHOD__};
	}

	const DEFAULT_TEMPLATE = 'df/catalog/category/navigation.phtml';
	const P__CATEGORY = 'category';
}