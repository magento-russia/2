<?php
/**
 * 3 класса используют одну и ту же заплатку:
 * @see Df_Catalog_Block_Seo_Sitemap_Category
 * @see Df_Catalog_Block_Seo_Sitemap_Product
 * @see Df_Catalog_Block_Seo_Sitemap_Tree_Category
 */
class Df_Catalog_Block_Seo_Sitemap_Product extends Mage_Catalog_Block_Seo_Sitemap_Product {
	/**
	 * Цель перекрытия —
	 * устранение сбоя «Notice: Undefined property: Df_Catalog_Model_Category::$name
	 * in app/design/frontend/base/default/template/catalog/seo/tree.phtml on line 36»
	 * @override
	 * @return Varien_Data_Collection
	 */
	public function getCollection() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getData('collection');
			Df_Catalog_Block_Seo_Sitemap_Category::preprocessCollection($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}
}