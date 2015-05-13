<?php
class Df_Seo_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return Df_Seo_Helper_Product_Image_Batch_Processor */
	public function getProductImageBatchProcessor() {
		return Df_Seo_Helper_Product_Image_Batch_Processor::s();
	}

	/** @return Df_Seo_Helper_Product_Image_Renamer */
	public function getProductImageRenamer() {
		return Df_Seo_Helper_Product_Image_Renamer::s();
	}

	/** @return Df_Seo_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}