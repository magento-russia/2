<?php
namespace Df\C1\Cml2\File;
class CatalogComposite extends \Df\C1\Cml2\File {
	/**
	 * @override
	 * @return string
	 */
	public function getNameBase() {df_should_not_be_here(); return null;}

	/**
	 * @override
	 * @return string
	 */
	public function getPathFull() {df_should_not_be_here(); return null;}

	/**
	 * @override
	 * @return string
	 */
	public function getPathRelative() {df_should_not_be_here(); return null;}

	/**
	 * @override
	 * @return \Df\Xml\X
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
	 * @see \Df\C1\Cml2\File::getXmlDocument()
	 * @return \Df\C1\Cml2\Import\Data\Document\Catalog
	 */
	public function getXmlDocument() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				\Df\C1\Cml2\Import\Data\Document::create($this->getXml(), 'no path')
			;
			df_assert($this->{__METHOD__} instanceof \Df\C1\Cml2\Import\Data\Document\Catalog);
		}
		return $this->{__METHOD__};
	}

	/** @return \Df\C1\Cml2\File */
	private function getFileAttributes() {return $this->cfg(self::$P__FILE_ATTRIBUTES);}

	/** @return \Df\C1\Cml2\File */
	private function getFileProducts() {return $this->cfg(self::$P__FILE_PRODUCTS);}

	/** @return \Df\C1\Cml2\File */
	private function getFileStructure() {return $this->cfg(self::$P__FILE_STRUCTURE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__FILE_ATTRIBUTES, \Df\C1\Cml2\File::class)
			->_prop(self::$P__FILE_PRODUCTS, \Df\C1\Cml2\File::class)
			->_prop(self::$P__FILE_STRUCTURE, \Df\C1\Cml2\File::class)
		;
	}
	/** @var string */
	private static $P__FILE_ATTRIBUTES = 'file_attributes';
	/** @var string */
	private static $P__FILE_PRODUCTS = 'file_products';
	/** @var string */
	private static $P__FILE_STRUCTURE = 'file_structure';
	/**
	 * @param \Df\C1\Cml2\File $fileStructure
	 * @param \Df\C1\Cml2\File $fileProducts
	 * @param \Df\C1\Cml2\File $fileAttributes
	 * @return \Df\C1\Cml2\File\CatalogComposite
	 */
	public static function i2(
		\Df\C1\Cml2\File $fileStructure, \Df\C1\Cml2\File $fileProducts, \Df\C1\Cml2\File $fileAttributes
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