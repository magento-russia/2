<?php
class Df_Seo_Model_Product_Gallery_Processor_Image extends Df_Core_Model {
	/** @return Df_Seo_Model_Product_Gallery_Processor_Image */
	public function process() {
		// Path may be changed due to renaming
		$imagePath =
			Df_Seo_Model_Product_Gallery_Processor_Image_Renamer::i(
				array(
					'product' => $this->getProduct()
					,'image' => $this->getImage()
				)
			)->process()
		;
		Df_Seo_Model_Product_Gallery_Processor_Image_Exif::i(
			array(
				'product' => $this->getProduct()
				,'imagePath' => $imagePath
			)
		)->process();
		return $this;
	}

	/** @return Varien_Object */
	private function getImage() {
		return $this->cfg(self::P__IMAGE);
	}

	/** @return Mage_Catalog_Model_Product */
	private function getProduct() {
		return $this->cfg(self::P__PRODUCT);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__IMAGE, self::P__IMAGE_TYPE)
			->_prop(self::P__PRODUCT, self::P__PRODUCT_TYPE)
		;
	}
	const _CLASS = __CLASS__;
	const P__IMAGE = 'image';
	const P__IMAGE_TYPE = 'Varien_Object';
	const P__PRODUCT = 'product';
	const P__PRODUCT_TYPE = 'Mage_Catalog_Model_Product';
}