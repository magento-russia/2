<?php
/**
 * Данный класс соответствует документу в формате XML,
 * передаваемому 1С в интернет-магазин.
 */
abstract class Df_1C_Model_Cml2_Import_Data_Document extends Df_Core_Model_SimpleXml_Parser_Entity {
	/**
	 * 2015-08-04
	 * @return string
	 * @see Df_1C_Model_Cml2_Import_Data_Document_Catalog::getExternalId_CatalogAttributes()
	 * @see Df_1C_Model_Cml2_Import_Data_Document_Offers::getExternalId_CatalogAttributes()
	 */
	abstract public function getExternalId_CatalogAttributes();
	/**
	 * @return string
	 * @see Df_1C_Model_Cml2_Import_Data_Document_Catalog::getExternalId_CatalogProducts()
	 * @see Df_1C_Model_Cml2_Import_Data_Document_Offers::getExternalId_CatalogProducts()
	 */
	abstract public function getExternalId_CatalogProducts();
	/**
	 * @return string
	 * @see Df_1C_Model_Cml2_Import_Data_Document_Catalog::getExternalId_CatalogStructure()
	 * @see Df_1C_Model_Cml2_Import_Data_Document_Offers::getExternalId_CatalogStructure()
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

	/** @return Df_1C_Model_Cml2_Session_ByIp */
	protected function session() {return Df_1C_Model_Cml2_Session_ByIp::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__PATH, self::V_STRING_NE);
	}
	const _CLASS = __CLASS__;
	/** @var string */
	protected static $P__PATH = 'path';

	/**
	 * @static
	 * @param Df_Varien_Simplexml_Element $e
	 * @param string $relativePath
	 * @return Df_1C_Model_Cml2_Import_Data_Document
	 */
	public static function create(Df_Varien_Simplexml_Element $e, $relativePath) {
		df_param_string_not_empty($relativePath, 1);
		/** @var string $class */
		if (self::_isCatalog($e)) {
			$class = Df_1C_Model_Cml2_Import_Data_Document_Catalog::_CLASS;
		}
		else if (self::_isOffers($e)) {
			$class = Df_1C_Model_Cml2_Import_Data_Document_Offers::_CLASS;
		}
		else {
			df_error('Неизвестный тип документа.');
		}
		/** @var Df_1C_Model_Cml2_Import_Data_Document $result */
		$result = new $class(array(self::P__SIMPLE_XML => $e, self::$P__PATH => $relativePath));
		df_assert($result instanceof Df_1C_Model_Cml2_Import_Data_Document);
		return $result;
	}

	/**
	 * @param Df_Varien_Simplexml_Element $e
	 * @return bool
	 */
	private static function _isCatalog(Df_Varien_Simplexml_Element $e) {
		return !!$e->descend('Каталог');
	}
	/**
	 * @param Df_Varien_Simplexml_Element $e
	 * @return bool
	 */
	private static function _isOffers(Df_Varien_Simplexml_Element $e) {
		return !!$e->descend('ПакетПредложений');
	}
}