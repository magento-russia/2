<?php
namespace Df\C1\Cml2\Import\Data;
/**
 * Данный класс соответствует документу в формате XML,
 * передаваемому 1С в интернет-магазин.
 */
abstract class Document extends \Df\Xml\Parser\Entity {
	/**
	 * 2015-08-04
	 * @return string
	 * @see \Df\C1\Cml2\Import\Data\Document\Catalog::getExternalId_CatalogAttributes()
	 * @see \Df\C1\Cml2\Import\Data\Document\Offers::getExternalId_CatalogAttributes()
	 */
	abstract public function getExternalId_CatalogAttributes();
	/**
	 * @return string
	 * @see \Df\C1\Cml2\Import\Data\Document\Catalog::getExternalId_CatalogProducts()
	 * @see \Df\C1\Cml2\Import\Data\Document\Offers::getExternalId_CatalogProducts()
	 */
	abstract public function getExternalId_CatalogProducts();
	/**
	 * @return string
	 * @see \Df\C1\Cml2\Import\Data\Document\Catalog::getExternalId_CatalogStructure()
	 * @see \Df\C1\Cml2\Import\Data\Document\Offers::getExternalId_CatalogStructure()
	 */
	abstract public function getExternalId_CatalogStructure();
	/** @return void */
	abstract public function storeInSession();

	/** @return string */
	public function getSchemeVersion() {return $this->getAttribute('ВерсияСхемы');}

	/** @return bool */
	public function isCatalog() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = self::_isCatalog($this->e());
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function isOffers() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = self::_isOffers($this->e());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	protected function getPath() {return $this->cfg(self::$P__PATH);}

	/** @return \Df\C1\Cml2\Session\ByIp */
	protected function session() {return \Df\C1\Cml2\Session\ByIp::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__PATH, DF_V_STRING_NE);
	}
	/** @var string */
	protected static $P__PATH = 'path';

	/**
	 * @used-by \Df\C1\Cml2\Import\Data\Document::create()
	 * @used-by \Df\C1\Cml2\File\CatalogComposite::getXmlDocument()
	 * @static
	 * @param \Df\Xml\X $e
	 * @param string $relativePath
	 * @return \Df\C1\Cml2\Import\Data\Document
	 */
	public static function create(\Df\Xml\X $e, $relativePath) {
		df_param_string_not_empty($relativePath, 1);
		/** @var string $class */
		$class =
			self::_isCatalog($e)
			? \Df\C1\Cml2\Import\Data\Document\Catalog::class
			: (
				self::_isOffers($e)
				? \Df\C1\Cml2\Import\Data\Document\Offers::class
				: df_error('Неизвестный тип документа.')
			)
		;
		return df_ic($class, __CLASS__, array(self::$P__E => $e, self::$P__PATH => $relativePath));
	}

	/**
	 * @param \Df\Xml\X $e
	 * @return bool
	 */
	private static function _isCatalog(\Df\Xml\X $e) {return !!$e->descend('Каталог');}
	/**
	 * @param \Df\Xml\X $e
	 * @return bool
	 */
	private static function _isOffers(\Df\Xml\X $e) {return !!$e->descend('ПакетПредложений');}
}