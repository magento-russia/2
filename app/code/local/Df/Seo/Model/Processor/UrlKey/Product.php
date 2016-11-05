<?php
/**
 * 2015-02-12
 * Этот класс предназначен для использования в самописных программах для Magento Dataflow.
 * http://magento-forum.ru/topic/760/
 */
class Df_Seo_Model_Processor_UrlKey_Product extends Mage_Dataflow_Model_Convert_Container_Abstract {
	public function process() {
		/** @var array $messagesToReport */
		$messagesToReport = [];
		/** @var Df_Catalog_Model_Resource_Product_Collection $products */
		$products = Df_Catalog_Model_Product::c();
		$products->addAttributeToSelect("url_key");
		$products->addAttributeToSelect("name");
		foreach ($products as $product) {
			/** @var Df_Catalog_Model_Product $product */
			$product
				->setUrlKey(Df_Catalog_Helper_Product_Url::s()->extendedFormat($product->getName()))
				->setIsMassupdate(true)
				->setExcludeUrlRewrite(true)
				->save()
			;
			$messageToReport = sprintf("«%s»: «%s»", $product->getName(), $product->getUrlKey());
			$messagesToReport[]= $messageToReport;
			$this->addException($messageToReport);
		}
		df_notify(implode("\n\n", $messagesToReport));
		$this->addException("Все товары обработаны.");
		df_mage()->catalog()->urlSingleton()->refreshRewrites();
		/** @var Mage_Catalog_Model_Indexer_Url $indexer */
		$indexer = df_model('catalog/indexer_url');
		$indexer->reindexAll();
	}
}