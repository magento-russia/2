<?php
class Df_Seo_Helper_Product_Image_Batch_Processor extends Mage_Core_Helper_Abstract {
	/** @return Df_Seo_Helper_Product_Image_Batch_Processor */
	public function process() {
		/** @var Df_Catalog_Model_Resource_Product_Collection $collection */
		$collection = Df_Catalog_Model_Product::c();
		$collection->addAttributeToSelect('*');
		$collection->load();
		try {
			foreach ($collection as $product) {
				/** @var Df_Catalog_Model_Product $product */
				Df_Seo_Model_Processor_MediaGallery::i($product)->process();
			}
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e, false);
		}
		return $this;
	}



	/** @return Df_Seo_Helper_Product_Image_Batch_Processor */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}