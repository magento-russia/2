<?php
namespace Df\C1\Cml2\Import\Data;
use Df\Xml\X;
// Класс соответствует документу в формате XML, передаваемому 1С в интернет-магазин.
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
	public function isCatalog() {return dfc($this, function() {return
		self::_isCatalog($this->e())
	;});}

	/** @return bool */
	public function isOffers() {return dfc($this, function() {return
		self::_isOffers($this->e())
	;});}

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
	 * @param X $e
	 * @param string $relativePath
	 * @return self
	 */
	public static function create(X $e, $relativePath) {
		df_param_string_not_empty($relativePath, 1);
		/** @var string $class */
		$class =
			self::_isCatalog($e) ? Document\Catalog::class : (
				self::_isOffers($e) ? Document\Offers::class :
					df_error('Неизвестный тип документа.')
			)
		;
		return df_ic($class, __CLASS__, [self::$P__E => $e, self::$P__PATH => $relativePath]);
	}

	/**
	 * @param X $e
	 * @return bool
	 */
	private static function _isCatalog(X $e) {return !!$e->descend('Каталог');}
	/**
	 * @param X $e
	 * @return bool
	 */
	private static function _isOffers(X $e) {return !!$e->descend('ПакетПредложений');}
}