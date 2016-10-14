<?php
class Df_1C_Cml2_File_CatalogComposite extends Df_1C_Cml2_File {
	/**
	 * @override
	 * @return string
	 */
	public function getNameBase() {df_should_not_be_here(__METHOD__);}

	/**
	 * @override
	 * @return string
	 */
	public function getPathFull() {df_should_not_be_here(__METHOD__);}

	/**
	 * @override
	 * @return string
	 */
	public function getPathRelative() {df_should_not_be_here(__METHOD__);}

	/**
	 * @override
	 * @return Df_Core_Sxe
	 */
	public function getXml() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = clone $this->getFileStructure()->getXml();
			$this->{__METHOD__}->extend($source = $this->getFileAttributes()->getXml());
			$this->{__METHOD__}->extend($source = $this->getFileProducts()->getXml());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @see Df_1C_Cml2_File::getXmlDocument()
	 * @return Df_1C_Cml2_Import_Data_Document_Catalog
	 */
	public function getXmlDocument() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_1C_Cml2_Import_Data_Document::create($this->getXml(), 'no path')
			;
			df_assert($this->{__METHOD__} instanceof Df_1C_Cml2_Import_Data_Document_Catalog);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Cml2_File */
	private function getFileAttributes() {return $this->cfg(self::$P__FILE_ATTRIBUTES);}

	/** @return Df_1C_Cml2_File */
	private function getFileProducts() {return $this->cfg(self::$P__FILE_PRODUCTS);}

	/** @return Df_1C_Cml2_File */
	private function getFileStructure() {return $this->cfg(self::$P__FILE_STRUCTURE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__FILE_ATTRIBUTES, Df_1C_Cml2_File::_C)
			->_prop(self::$P__FILE_PRODUCTS, Df_1C_Cml2_File::_C)
			->_prop(self::$P__FILE_STRUCTURE, Df_1C_Cml2_File::_C)
		;
	}
	/** @var string */
	private static $P__FILE_ATTRIBUTES = 'file_attributes';
	/** @var string */
	private static $P__FILE_PRODUCTS = 'file_products';
	/** @var string */
	private static $P__FILE_STRUCTURE = 'file_structure';
	/**
	 * @param Df_1C_Cml2_File $fileStructure
	 * @param Df_1C_Cml2_File $fileProducts
	 * @param Df_1C_Cml2_File $fileAttributes
	 * @return Df_1C_Cml2_File_CatalogComposite
	 */
	public static function i2(
		Df_1C_Cml2_File $fileStructure, Df_1C_Cml2_File $fileProducts, Df_1C_Cml2_File $fileAttributes
	) {
		df_assert($fileAttributes->getXmlDocumentAsCatalog()->hasAttributes());
		df_assert($fileProducts->getXmlDocumentAsCatalog()->hasProducts());
		df_assert($fileStructure->getXmlDocumentAsCatalog()->hasStructure());
		return new self(array(
			self::$P__FILE_ATTRIBUTES => $fileAttributes
			, self::$P__FILE_PRODUCTS => $fileProducts
			, self::$P__FILE_STRUCTURE => $fileStructure
		));
	}
}