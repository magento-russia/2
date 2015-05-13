<?php
class Df_Dataflow_Model_Settings_Products extends Df_Core_Model_Settings {
	/** @return boolean */
	public function getCustomOptionsSupport() {return $this->getYesNo('custom_options_support');}
	/** @return boolean */
	public function getDeletePreviousImages() {return $this->getYesNo('delete_previous_images');}
	/** @return boolean */
	public function getEnhancedCategorySupport() {return $this->getYesNo('enhanced_category_support');}
	/** @return boolean */
	public function getGallerySupport() {return $this->getYesNo('gallery_support');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_dataflow/products/';}
	/** @return Df_Dataflow_Model_Settings_Products */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}