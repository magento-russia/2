<?php
class Df_Seo_Observer {
	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @see Mage_Adminhtml_CacheController::cleanImagesAction()
	 * @return void
	 */
	public function clean_catalog_images_cache_after() {
		Df_Seo_Helper_Product_Image_Batch_Processor::s()->process();
	}
}