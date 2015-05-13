<?php
class Df_Localization_Model_Onetime_DemoImagesImporter_Image_Collection
	extends Df_Varien_Data_Collection_Singleton {
	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {
		return Df_Localization_Model_Onetime_DemoImagesImporter_Image::_CLASS;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function loadInternal() {
		foreach ($this->getImageLocalPaths() as $imageLocalPath) {
			/** @var string $imageLocalPath */
			$this->addItem(
				Df_Localization_Model_Onetime_DemoImagesImporter_Image::i($imageLocalPath)
			);
		}
	}

	/** @return string[] */
	private function getImageLocalPaths() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				rm_conn()->fetchCol(
					rm_conn()->select()
						->from(rm_table('catalog/product_attribute_media_gallery'), 'value')
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Localization_Model_Onetime_DemoImagesImporter_Image_Collection */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}