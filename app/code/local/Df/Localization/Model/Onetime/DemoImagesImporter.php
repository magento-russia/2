<?php
class Df_Localization_Model_Onetime_DemoImagesImporter extends Df_Core_Model {
	/** @return void */
	public function process() {
		foreach (Df_Localization_Model_Onetime_DemoImagesImporter_Image_Collection::s() as $image) {
			/** @var Df_Localization_Model_Onetime_DemoImagesImporter_Image $image */
			if (!$image->isExist()) {
				$image->download($this->getBaseUrl());
			}
		}
	}

	/** @return string */
	private function getBaseUrl() {return $this->cfg(self::$P__BASE_URL);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__BASE_URL, self::V_STRING_NE);
	}

	/** @var string */
	private static $P__BASE_URL = 'base_url';

	/**
	 * @param string $baseUrl
	 * @return Df_Localization_Model_Onetime_DemoImagesImporter
	 */
	public static function i($baseUrl) {return new self(array(self::$P__BASE_URL => $baseUrl));}
}