<?php
class Df_Seo_Helper_Product_Image_Batch_Processor extends Mage_Core_Helper_Abstract {
	/** @return Df_Seo_Helper_Product_Image_Batch_Processor */
	public function process() {
		/** @var Df_Catalog_Model_Resource_Product_Collection $collection */
		$collection = Df_Catalog_Model_Product::c();
		$collection->addAttributeToSelect('*');
		if (df_module_enabled(Df_Core_Module::LICENSOR)) {
			$collection
				// Преобразуем картинки только для тех доменов,
				// для которых данная функция лицензирована
				->addWebsiteFilter(
					df_feature(Df_Core_Feature::SEO)->getWebsiteIds()
				)
			;
		}

		$collection->load();
		df_h()->catalog()->assert()->productCollection($collection);
		try {
			foreach ($collection as $product) {
				Mage
					::getModel(
						"df_seo/product_gallery_processor"
						,array("product" => $product)
					)
						->process()
				;
			}
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e, false);
		}
		return $this;
	}

	const _CLASS = __CLASS__;

	/** @return Df_Seo_Helper_Product_Image_Batch_Processor */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}