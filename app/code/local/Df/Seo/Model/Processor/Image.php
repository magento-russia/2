<?php
class Df_Seo_Model_Processor_Image extends Df_Core_Model {
	/** @return void */
	public function process() {
		Df_Seo_Model_Processor_Image_Exif::p(
			Df_Seo_Model_Processor_Image_Renamer::i($this->getProduct(), $this->getImage())->process()
			, $this->getProduct()
		);
	}

	/** @return Varien_Object */
	private function getImage() {return $this->cfg(self::$P__IMAGE);}

	/** @return Df_Catalog_Model_Product */
	private function getProduct() {return $this->cfg(self::$P__PRODUCT);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__IMAGE, 'Varien_Object')
			->_prop(self::$P__PRODUCT, Df_Catalog_Model_Product::_C)
		;
	}
	const _C = __CLASS__;
	/** @var string */
	private static $P__IMAGE = 'image';
	/** @var string */
	private static $P__PRODUCT = 'product';
	/**
	 * @param Df_Catalog_Model_Product $product
	 * @param Varien_Object $image
	 * @return Df_Seo_Model_Processor_Image
	 */
	public static function i(Df_Catalog_Model_Product $product, Varien_Object $image) {
		return new self(array(self::$P__PRODUCT => $product, self::$P__IMAGE => $image));
	}
}