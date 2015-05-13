<?php
class Df_1C_Model_Cml2_File extends Df_Core_Model_Abstract {
	/** @return string */
	public function getNameBase() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = basename($this->getPathRelative());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getPathFull() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_1C_Model_Cml2_FileSystem::s()->getFullPathByRelativePath(
					$this->getPathRelative()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Обратите внимание,
	 * что этот метод может вернуть не просто имя файла (catalog.xml, offers.xml),
	 * но и имя с относительным путём (для файлов картинок), например:
	 * import_files/cb/cbcf4934-55bc-11d9-848a-00112f43529a_b5cfbe1a-c400-11e1-a851-4061868fc6eb.jpeg
	 * @return string
	 */
	public function getPathRelative() {return $this->cfg(self::$P__PATH_RELATIVE);}

	/** @return Df_Varien_Simplexml_Element */
	public function getXml() {return $this->getXmlDocument()->e();}

	/** @return Df_1C_Model_Cml2_Import_Data_Document */
	public function getXmlDocument() {
		return Df_1C_Model_Cml2_FileSystem::s()->getXmlDocumentByRelativePath($this->getPathRelative());
	}

	/** @return Df_1C_Model_Cml2_Import_Data_Document_Catalog */
	public function getXmlDocumentAsCatalog() {
		if (!isset($this->{__METHOD__})) {
			df_assert($this->getXmlDocument() instanceof Df_1C_Model_Cml2_Import_Data_Document_Catalog);
			$this->{__METHOD__} = $this->getXmlDocument();
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Model_Cml2_Import_Data_Document_Offers */
	public function getXmlDocumentAsOffers() {
		if (!isset($this->{__METHOD__})) {
			df_assert($this->getXmlDocument() instanceof Df_1C_Model_Cml2_Import_Data_Document_Offers);
			$this->{__METHOD__} = $this->getXmlDocument();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__PATH_RELATIVE, self::V_STRING_NE);
	}

	/** @var string */
	private static $P__PATH_RELATIVE = 'path_relative';
	const _CLASS = __CLASS__;

	/**
	 * @param string $pathRelative
	 * @return Df_1C_Model_Cml2_File
	 */
	public static function i($pathRelative) {
		return new self(array(self::$P__PATH_RELATIVE => $pathRelative));
	}
}