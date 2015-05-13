<?php
class Df_Catalog_Model_Product_Image extends Mage_Catalog_Model_Product_Image {
	/**
	 * Цель перекрытия —
	 * добавление к товарному изображению информации EXIF.
	 * @override
	 * @return Df_Catalog_Model_Product_Image
	 */
	public function saveFile() {
		parent::saveFile();
		/** @var bool $patchNeeded */
		static $patchNeeded;
		if (!isset($patchNeeded)) {
			$patchNeeded =
					df_enabled(Df_Core_Feature::SEO)
				&&
					df_cfg()->seo()->images()->getAddExifToJpegs()
			;
		}
		if ($patchNeeded) {
			Df_Seo_Model_Product_Gallery_Processor_Image_Exif::i(array(
				Df_Seo_Model_Product_Gallery_Processor_Image_Exif::P__PRODUCT => $this->getProductDf()
				,Df_Seo_Model_Product_Gallery_Processor_Image_Exif::P__IMAGE_PATH => $this->getNewFile()
			))->process();
		}
		return $this;
	}

	/**
	 * @param Df_Catalog_Model_Product $product
	 * @return void
	 */
	public function setProductDf(Df_Catalog_Model_Product $product) {$this->_productDf = $product;}

	/** @return Df_Catalog_Model_Product */
	private function getProductDf() {return $this->_productDf;}

	/** @var Mage_Catalog_Model_Product  */
	private $_productDf;
}