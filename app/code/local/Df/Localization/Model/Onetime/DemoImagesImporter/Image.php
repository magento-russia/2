<?php
class Df_Localization_Model_Onetime_DemoImagesImporter_Image extends Df_Core_Model {
	/** @return bool */
	public function isExist() {return is_file($this->getPathFull());}

	/**
	 * @param string $baseUrl
	 * @return void
	 */
	public function download($baseUrl) {
		/** @var string $imageData */
		$imageData = @file_get_contents($baseUrl . 'media/catalog/product' . $this->getPathLocal());
		if ($imageData) {
			df_path()->prepareForWriting($this->getDir());
			file_put_contents($this->getPathFull(), $imageData);
		}
	}

	/** @return string */
	private function getDir() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = dirname($this->getPathFull());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getPathFull() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_concat_path(Mage::getBaseDir('media'), 'catalog', 'product') . $this->getPathLocal()
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getPathLocal() {return $this->cfg(self::$P__LOCAL_PATH);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__LOCAL_PATH, self::V_STRING_NE);
	}

	/** @var string */
	private static $P__LOCAL_PATH = 'local_path';

	const _CLASS = __CLASS__;

	/**
	 * @param string $localPath
	 * @return Df_Localization_Model_Onetime_DemoImagesImporter_Image
	 */
	public static function i($localPath) {return new self(array(self::$P__LOCAL_PATH => $localPath));}
}