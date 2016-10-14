<?php
class Df_Seo_Model_Processor_UrlKey_Category extends Mage_Dataflow_Model_Convert_Container_Abstract {
	/** @return void */
	public function process() {
		/** @var Df_Catalog_Model_Resource_Category_Collection $categories */
		$categories =
			Df_Catalog_Model_Category::c()
				->addAttributeToSelect('url_key')
				->addAttributeToSelect('name')
		;
		foreach ($categories as $category) {
			/** @var Df_Catalog_Model_Category $category */
			$category
				->setUrlKey(Df_Catalog_Helper_Product_Url::s()->extendedFormat($category->getName()))
				->setExcludeUrlRewrite(true)
				->save()
			;
			$this->addException(sprintf('«%s»: «%s»', $category->getName(), $category->getUrlKey()));
		}
		$this->addException('Все товарные разделы обработаны.');
	}

	const _C = __CLASS__;
}