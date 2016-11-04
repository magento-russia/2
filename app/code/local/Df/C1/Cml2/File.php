<?php
namespace Df\C1\Cml2;
class File extends \Df_Core_Model {
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
				\Df\C1\Cml2\FileSystem::s()->getFullPathByRelativePath(
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

	/** @return \Df\Xml\X */
	public function getXml() {return $this->getXmlDocument()->e();}

	/** @return \Df\C1\Cml2\Import\Data\Document */
	public function getXmlDocument() {
		return \Df\C1\Cml2\FileSystem::s()->getXmlDocumentByRelativePath($this->getPathRelative());
	}

	/** @return \Df\C1\Cml2\Import\Data\Document\Catalog */
	public function getXmlDocumentAsCatalog() {
		if (!isset($this->{__METHOD__})) {
			df_assert($this->getXmlDocument() instanceof \Df\C1\Cml2\Import\Data\Document\Catalog);
			$this->{__METHOD__} = $this->getXmlDocument();
		}
		return $this->{__METHOD__};
	}

	/** @return \Df\C1\Cml2\Import\Data\Document\Offers */
	public function getXmlDocumentAsOffers() {
		if (!isset($this->{__METHOD__})) {
			df_assert($this->getXmlDocument() instanceof \Df\C1\Cml2\Import\Data\Document\Offers);
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
		$this->_prop(self::$P__PATH_RELATIVE, DF_V_STRING_NE);
	}

	/** @used-by \Df\C1\Cml2\File\CatalogComposite::_construct() */

	/** @var string */
	private static $P__PATH_RELATIVE = 'path_relative';

	/**
	 * @param string $pathRelative
	 * @return \Df\C1\Cml2\File
	 */
	public static function i($pathRelative) {
		return new self(array(self::$P__PATH_RELATIVE => $pathRelative));
	}
}