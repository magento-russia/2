<?php
class Df_Localization_Translation_FileStorage extends Df_Core_Model {
	/** @return string */
	public function getCode() {
		return $this->cfg(self::P__CODE);
	}

	/** @return Df_Localization_Translation_File_Collection */
	public function getFiles() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Localization_Translation_File_Collection $result */
			$result = Df_Localization_Translation_File_Collection::i();
			if (is_dir($this->getPath())) {
				foreach (Df_Spl_Iterator_FilesByExtension::i($this->getPath(), 'csv') as $file) {
					/** @var DirectoryIterator $file */
					$result->addItem(Df_Localization_Translation_File::i($file->getRealPath()));
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getPath() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::getBaseDir('locale') . DS . $this->getCode();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__CODE, RM_V_STRING_NE);
	}
	const _C = __CLASS__;
	const P__CODE = 'code';
	/**
	 * @param string $code
	 * @return Df_Localization_Translation_FileStorage
	 */
	public static function i($code) {return new self(array(self::P__CODE => $code));}
}