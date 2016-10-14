<?php
/**
 * 3 класса используют одну и ту же заплатку:
 * @see Df_Catalog_Block_Seo_Sitemap_Category
 * @see Df_Catalog_Block_Seo_Sitemap_Product
 * @see Df_Catalog_Block_Seo_Sitemap_Tree_Category
 */
class Df_Catalog_Block_Seo_Sitemap_Category extends Mage_Catalog_Block_Seo_Sitemap_Category {
	/**
	 * Цель перекрытия —
	 * устранение сбоя «Notice: Undefined property: Df_Catalog_Model_Category::$name
	 * in app/design/frontend/base/default/template/catalog/seo/tree.phtml on line 36»
	 * http://magento-forum.ru/topic/4298/
	 * @override
	 * @return Varien_Data_Collection
	 */
	public function getCollection() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getData('collection');
			self::preprocessCollection($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * Этот метод публичен, потому что используется не только этим классом, но и классами:
	 * @used-by Df_Catalog_Block_Seo_Sitemap_Product
	 * @used-by Df_Catalog_Block_Seo_Sitemap_Tree_Category
	 *
	 * Цель обработки коллекции — устранение сбоя
	 * «Notice: Undefined property: Df_Catalog_Model_Category::$name
	 * in app/design/frontend/base/default/template/catalog/seo/tree.phtml on line 36»
	 * http://magento-forum.ru/topic/4298/
	 *
	 * @override
	 * @param Varien_Data_Collection $collection
	 * @return void
	 */
	public static function preprocessCollection(Varien_Data_Collection $collection) {
		foreach ($collection as $item) {
			/** @var Df_Catalog_Model_Product|Df_Catalog_Model_Category $item */
			$item->{'name'} = $item->getName();
		}
	}
}