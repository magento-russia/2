<?php
class Df_Catalog_Block_Category_Navigation extends Df_Core_Block_Template {
	/**
	 * @override
	 * @see Mage_Core_Block_Template::_toHtml()
	 * @used-by Mage_Core_Block_Abstract::toHtml()
	 */
	protected function _toHtml() {
		/** @var string $result */
		$result = parent::_toHtml();
		if ($result) {
			$result = rm_div('df', rm_div('df-category-navigation', $result));
		}
		return $result;
	}

	/**
	 * @used-by df/catalog/category/navigation.phtml
	 * @return Mage_Catalog_Model_Resource_Category_Collection|Mage_Catalog_Model_Resource_Category_Flat_Collection
	 */
	protected function categories() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Catalog_Model_Resource_Category_Collection|Mage_Catalog_Model_Resource_Category_Flat_Collection $result */
			$result = $this->currentCategory()->getCollection();
			$result->addAttributeToSelect('*');
			$result->addAttributeToFilter('is_active', 1);
			$result->addIdFilter($this->currentCategory()->getChildren());
			$result->setOrder('position', 'ASC');
			/**
			 * К сожалению, нельзя здесь для проверки публичности метода использовать @see is_callable(),
			 * потому что наличие @see Varien_Object::__call()
			 * приводит к тому, что @see is_callable всегда возвращает true.
			 * Обратите внимание, что @uses method_exists(), в отличие от @see is_callable(),
			 * не гарантирует публичную доступность метода:
			 * т.е. метод может у класса быть, но вызывать его всё равно извне класса нельзя,
			 * потому что он имеет доступность private или protected.
			 * Пока эта проблема никак не решена.
			 */
			/**
			 * @uses Mage_Catalog_Model_Resource_Category_Collection::joinUrlRewrite()
			 * @uses Mage_Catalog_Model_Resource_Category_Flat_Collection::joinUrlRewrite()
			 */
			if (method_exists($result, 'joinUrlRewrite')) {
				call_user_func(array($result, 'joinUrlRewrite'));
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @see Df_Core_Block_Template::cacheKeySuffix()
	 * @used-by Df_Core_Block_Template::getCacheKeyInfo()
	 * @return string|string[]
	 */
	protected function cacheKeySuffix() {return $this->currentCategory()->getId();}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/catalog/category/navigation.phtml';}

	/**
	 * @override
	 * @see Df_Core_Block_Template::needToShow()
	 * @used-by Df_Core_Block_Template::_loadCache()
	 * @used-by Df_Core_Block_Template::getCacheKey()
	 * @used-by _construct()
	 * @return bool
	 */
	protected function needToShow() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_cfg()->catalog()->navigation()->getEnabled() && $this->hasItems()
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by df/catalog/category/navigation.phtml
	 * @param Df_Catalog_Model_Category $category
	 * @return string
	 */
	protected function thumbUrl(Df_Catalog_Model_Category $category) {
		/** @var string|null $thumb */
		$thumb = $category['thumbnail'];
		if (!$thumb) {
			$thumb = $category['df_thumbnail'];
		}
		return
			!$thumb
			? '/skin/frontend/base/default/df/images/catalog/category/navigation/absent/small.gif'
			: Mage::getBaseUrl('media') . "catalog/category/{$thumb}"
		;
	}

	/**
	 * @used-by cacheKeySuffix()
	 * @used-by getItems()
	 * @used-by hasItems()
	 * @return Df_Catalog_Model_Category
	 */
	private function currentCategory() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Catalog_Model_Category $result */
			$result = $this['category'];
			if (!$result) {
				$result = rm_state()->getCurrentCategory();
			}
			df_assert($result instanceof Df_Catalog_Model_Category);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by getItems()
	 * @used-by needToShow()
	 * @return bool
	 */
	private function hasItems() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->currentCategory()->hasChildren();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_Catalog_Model_Category_Content_Inserter::contentNew()
	 * @return string
	 */
	public static function r() {static $r; return !is_null($r) ? $r : $r = rm_render(__CLASS__);}
}