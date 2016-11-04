<?php
namespace Df\C1\Cml2\File;
use Df\C1\Cml2\File;
use Df\C1\Cml2\Import\Data\Document;
use Df\C1\Cml2\Import\Data\Document\Catalog as DocumentCatalog;
class CatalogComposite extends File {
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
	public function getXml() {return dfc($this, function() {
		/** @var \Df\Xml\X $result */
		$result = clone $this->getFileStructure()->getXml();
		$result->extend($source = $this->getFileAttributes()->getXml());
		$result->extend($source = $this->getFileProducts()->getXml());
		return $result;
	});}

	/**
	 * @override
	 * @see \Df\C1\Cml2\File::getXmlDocument()
	 * @return DocumentCatalog
	 */
	public function getXmlDocument() {return dfc($this, function() {
		/** @var DocumentCatalog $result */
		$result = Document::create($this->getXml(), 'no path');
		df_assert($result instanceof DocumentCatalog);
		return $result;
	});}

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
			->_prop(self::$P__FILE_ATTRIBUTES, File::class)
			->_prop(self::$P__FILE_PRODUCTS, File::class)
			->_prop(self::$P__FILE_STRUCTURE, File::class)
		;
	}
	/** @var string */
	private static $P__FILE_ATTRIBUTES = 'file_attributes';
	/** @var string */
	private static $P__FILE_PRODUCTS = 'file_products';
	/** @var string */
	private static $P__FILE_STRUCTURE = 'file_structure';
	/**
	 * @param File $fileStructure
	 * @param File $fileProducts
	 * @param File $fileAttributes
	 * @return self
	 */
	public static function i2(File $fileStructure, File $fileProducts, File $fileAttributes) {
		df_assert($fileAttributes->getXmlDocumentAsCatalog()->hasAttributes());
		df_assert($fileProducts->getXmlDocumentAsCatalog()->hasProducts());
		df_assert($fileStructure->getXmlDocumentAsCatalog()->hasStructure());
		return new self([
			self::$P__FILE_ATTRIBUTES => $fileAttributes
			, self::$P__FILE_PRODUCTS => $fileProducts
			, self::$P__FILE_STRUCTURE => $fileStructure
		]);
	}
}