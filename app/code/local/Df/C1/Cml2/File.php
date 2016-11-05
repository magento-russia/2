<?php
namespace Df\C1\Cml2;
use Df\C1\Cml2\Import\Data\Document;
class File extends \Df_Core_Model {
	/** @return string */
	public function getNameBase() {return dfc($this, function() {return
		basename($this->getPathRelative())
	;});}

	/** @return string */
	public function getPathFull() {return dfc($this, function() {return
		FileSystem::s()->getFullPathByRelativePath($this->getPathRelative())
	;});}

	/**
	 * Обратите внимание,
	 * что этот метод может вернуть не просто имя файла (catalog.xml, offers.xml),
	 * но и имя с относительным путём (для файлов картинок), например:
	 * import_files/cb/cbcf4934-55bc-11d9-848a-00112f43529a_b5cfbe1a-c400-11e1-a851-4061868fc6eb.jpeg
	 * @return string
	 */
	public function getPathRelative() {return $this->cfg(self::$P__PATH_RELATIVE);}

	/** @return \Df\Xml\X */
	public function getXml() {return $this->getXmlDocument()->e();}

	/** @return Document */
	public function getXmlDocument() {return
		FileSystem::s()->getXmlDocumentByRelativePath($this->getPathRelative())
	;}

	/** @return Document\Catalog */
	public function getXmlDocumentAsCatalog() {return dfc($this, function() {
		df_assert($this->getXmlDocument() instanceof Document\Catalog);
		return $this->getXmlDocument();
	});}

	/** @return Document\Offers */
	public function getXmlDocumentAsOffers() {return dfc($this, function() {
		df_assert($this->getXmlDocument() instanceof Document\Offers);
		return $this->getXmlDocument();
	});}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__PATH_RELATIVE, DF_V_STRING_NE);
	}

	/** @var string */
	private static $P__PATH_RELATIVE = 'path_relative';

	/**
	 * @param string $pathRelative
	 * @return self
	 */
	public static function i($pathRelative) {return new self([
		self::$P__PATH_RELATIVE => $pathRelative
	]);}
}