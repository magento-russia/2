<?php
class Df_Seo_Model_Processor_MediaGallery extends Df_Core_Model {
	/** @return void */
	public function process() {
		/** @var Df_Catalog_Model_Product $product */
		$product = $this->getProduct();
		$product->setData(self::MEDIA_GALLERY_ATTRIBUTE, dfa(
			$this->getProduct()->getTypeInstance(true)->getSetAttributes($this->getProduct())
			,'media_gallery'
		));
		$product->setData(self::IMAGE_KEY, df_translit_url($this->getProduct()->getName()));
		foreach ($product->getMediaGalleryImages() as $image) {
			Df_Seo_Model_Processor_Image::i($this->getProduct(), $image)->process();
		}
		$product->save();
	}

	/** @return Df_Catalog_Model_Product */
	private function getProduct() {return $this->cfg(self::P__PRODUCT);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__PRODUCT, Df_Catalog_Model_Product::class);
	}

	const P__PRODUCT = 'product';
	/**
	 * @used-by process()
	 * @used-by Df_Seo_Model_Processor_Image_Renamer::process()
	 */
	const MEDIA_GALLERY_ATTRIBUTE = 'media_gallery_attribute';
	/**
	 * @used-by process()
	 * @used-by Df_Seo_Model_Processor_Image_Renamer::getCorrectedFileName()
	 * @used-by Df_Seo_Model_Processor_Image_Renamer::needCorrections()
	 */
	const IMAGE_KEY = 'image_key';

	/**
	 * @param Df_Catalog_Model_Product $product
	 * @return Df_Seo_Model_Processor_MediaGallery
	 */
	public static function i(Df_Catalog_Model_Product $product) {return new self(array(
		self::P__PRODUCT => $product
	));}
}