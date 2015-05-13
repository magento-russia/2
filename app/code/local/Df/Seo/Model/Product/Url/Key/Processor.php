<?php
class Df_Seo_Model_Product_Url_Key_Processor extends Mage_Dataflow_Model_Convert_Container_Abstract {
	public function process() {
		/** @var array $messagesToReport */
		$messagesToReport = array();
		foreach ($this->getItems() as $item) {
			/** @var Df_Catalog_Model_Product $item */
			$item
				->setUrlKey(Df_Catalog_Helper_Product_Url::s()->extendedFormat($item->getName()))
				->setIsMassupdate(true)
				->setExcludeUrlRewrite(true)
				->save()
			;
			$messageToReport = rm_sprintf("«%s»: «%s»", $item->getName(), $item->getUrlKey());
			$messagesToReport[]= $messageToReport;
			$this->addException($messageToReport);
		}
		df_notify(implode("\n\n", $messagesToReport));
		$this
			->addException(
				df_h()->seo()->__("Все товары обработаны.")
			)
		;
		df_mage()->catalog()->urlSingleton()->refreshRewrites();
		/** @var Mage_Catalog_Model_Indexer_Url $indexer */
		$indexer = df_model('catalog/indexer_url');
		$indexer->reindexAll();
	}

	/** @return array */
	private function getItems() {
		return
			Mage::getResourceModel('catalog/product_collection')
				->addAttributeToSelect("url_key")
				->addAttributeToSelect("name")
				//->setPageSize(10)
				//->setCurPage(1)
				->load()
		;
	}

	const _CLASS = __CLASS__;
}