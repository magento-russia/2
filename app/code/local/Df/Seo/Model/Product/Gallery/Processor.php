<?php
class Df_Seo_Model_Product_Gallery_Processor extends Df_Core_Model {
	/** @return Df_Seo_Model_Product_Gallery_Processor */
	public function process() {
		$this->getProduct()->load('media_gallery');
		$this
			->getProduct()
			->setMediaGalleryAttribute(
				df_a(
					$this
						->getProduct()
						->getTypeInstance(true)
						->getSetAttributes(
							$this->getProduct()
						)
					,"media_gallery"
				)
			)
			->setImageKey(
				df_output()
					->transliterate(
						$this->getProduct()->getName()
					)
			)
		;
		foreach ($this->getProduct()->getMediaGalleryImages() as $image) {
			Mage
				::getModel(
					"df_seo/product_gallery_processor_image"
					,array(
						"product" => $this->getProduct()
						,"image" => $image
					)
				)
					->process()
			;
		}
		$this->getProduct()->save();
		//$this->getProduct()->getResource()->saveAttribute($this->getProduct(), 'media_gallery');
		return $this;
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
		$this->_prop(self::P__PRODUCT, self::P__PRODUCT_TYPE);
	}
	const _CLASS = __CLASS__;
	const P__PRODUCT = 'product';
	const P__PRODUCT_TYPE = 'Mage_Catalog_Model_Product';

}