<?php
class Df_Seo_Model_Observer {
	/** @return void */
	public function clean_catalog_images_cache_after() {
		if (df_enabled(Df_Core_Feature::SEO)) {
			df_h()->seo()->getProductImageBatchProcessor()->process();
		}
	}

}