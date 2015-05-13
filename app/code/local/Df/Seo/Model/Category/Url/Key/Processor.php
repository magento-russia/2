<?php
class Df_Seo_Model_Category_Url_Key_Processor extends Mage_Dataflow_Model_Convert_Container_Abstract {
	/** @return void */
	public function process() {
		/** @var Df_Catalog_Model_Resource_Category_Collection $categories */
		$categories =
			Df_Catalog_Model_Resource_Category_Collection::i()
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
			$this->addException(rm_sprintf('«%s»: «%s»', $category->getName(), $category->getUrlKey()));
		}
		$this->addException(df_h()->seo()->__('Все товарные разделы обработаны.'));
	}

	const _CLASS = __CLASS__;
}