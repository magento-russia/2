<?php
class Df_Catalog_Model_Product_Image extends Mage_Catalog_Model_Product_Image {
	/**
	 * Цель перекрытия —
	 * добавление к товарному изображению информации EXIF.
	 * @override
	 * @see Mage_Catalog_Model_Product_Image::saveFile()
	 * @return Df_Catalog_Model_Product_Image
	 */
	public function saveFile() {
		parent::saveFile();
		/** @var bool $patchNeeded */
		static $patchNeeded;
		if (is_null($patchNeeded)) {
			$patchNeeded = df_cfgr()->seo()->images()->getAddExifToJpegs();
		}
		if ($patchNeeded) {
			Df_Seo_Model_Processor_Image_Exif::p($this->getNewFile(), $this->getProductDf());
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