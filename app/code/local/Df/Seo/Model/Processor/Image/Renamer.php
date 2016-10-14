<?php
/** @used-by Df_Seo_Model_Processor_Image::process() */
class Df_Seo_Model_Processor_Image_Renamer extends Df_Core_Model {
	/** @return string */
	public function process() {
		$result = $this->getImage()->getPath();
		if (is_file($result)) {
			if ($this->needCorrections()) {
				$correctedFileName = $this->getCorrectedFileName();
				$r =
					copy(
						$this->getImage()->getPath()
						,$correctedFileName
					)
				;
				if (!$r || !is_file($correctedFileName)) {
					df_notify('Failed to create file: «%s».', $correctedFileName);
				}
				else {
					$result = $correctedFileName;
					// Add image with new name
					$this
						->getProduct()
						->addImageToMediaGallery(
							$correctedFileName
							,$this->getFields()
							,false
							,false
						)
					;
					// Remove previous image
					$this
						->getProduct()
						->getData(Df_Seo_Model_Processor_MediaGallery::MEDIA_GALLERY_ATTRIBUTE)
						->getBackend()
						->removeImage(
							$this->getProduct()
							,$this->getImage()->getFile()
						)
					;
					if (
						file_exists(
							$this->getImage()->getPath()
						)
					) {
						unlink(
							$this->getImage()->getPath()
						)
						;
					}
				}
			}
		}
		return $result;
	}

	/**
	 * @param string $key
	 * @param int $ordering
	 * @return string
	 */
	private function generateOrderedKey($key, $ordering) {
		return
			(1 === $ordering)
			? $key
			: implode('-', array($key, $ordering))
		;
	}

	/** @return string */
	private function getBaseName() {
		return df_a($this->getFileInfo(), 'basename');
	}

	/** @return string */
	private function getCorrectedFileName() {
		$result = $this->getImage()->getPath();
		$dirname = df_a($this->getFileInfo(), 'dirname');
		$extension = df_a($this->getFileInfo(), 'extension');
		$key = $this->getProduct()->getData(Df_Seo_Model_Processor_MediaGallery::IMAGE_KEY);
		$i = 1;
		while (1) {
			$result =
				df_concat_path(
					$dirname, rm_concat_clean('.', $this->generateOrderedKey($key, $i++), $extension)
				)
			;
			if (!file_exists($result)) {
				break;
			}
		}
		return $result;
	}

	/** @return array */
	private function getFields() {
		$result = array();
		$fields = array("image", "small_image", "thumbnail");
		foreach ($fields as $field) {
			if ($this->getImage()->getFile() === $this->getProduct()->getData($field)) {
				$result[]= $field;
			}
		}
		return $result;
	}

	/** @return array */
	private function getFileInfo() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = pathinfo ($this->getImage()->getPath());
		}
		return $this->{__METHOD__};
	}

	/** @return Varien_Object */
	private function getImage() {return $this->cfg(self::$P__IMAGE);}

	/** @return Mage_Catalog_Model_Product */
	private function getProduct() {return $this->cfg(self::$P__PRODUCT);}

	/** @return bool */
	private function needCorrections() {
		return
				0
			!==
				strpos(
					$this->getBaseName()
					,$this->getProduct()->getData(Df_Seo_Model_Processor_MediaGallery::IMAGE_KEY)
				)
		;
	}

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
	 * @static
	 * @param Df_Catalog_Model_Product $product
	 * @param Varien_Object $image
	 * @return Df_Seo_Model_Processor_Image_Renamer
	 */
	public static function i(Df_Catalog_Model_Product $product, Varien_Object $image) {
		return new self(array(self::$P__PRODUCT => $product, self::$P__IMAGE => $image));
	}
}