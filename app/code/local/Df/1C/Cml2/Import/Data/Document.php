<?php
/**
 * Данный класс соответствует документу в формате XML,
 * передаваемому 1С в интернет-магазин.
 */
abstract class Df_1C_Cml2_Import_Data_Document extends Df_Core_Xml_Parser_Entity {
	/**
	 * 2015-08-04
	 * @return string
	 * @see Df_1C_Cml2_Import_Data_Document_Catalog::getExternalId_CatalogAttributes()
	 * @see Df_1C_Cml2_Import_Data_Document_Offers::getExternalId_CatalogAttributes()
	 */
	abstract public function getExternalId_CatalogAttributes();
	/**
	 * @return string
	 * @see Df_1C_Cml2_Import_Data_Document_Catalog::getExternalId_CatalogProducts()
	 * @see Df_1C_Cml2_Import_Data_Document_Offers::getExternalId_CatalogProducts()
	 */
	abstract public function getExternalId_CatalogProducts();
	/**
	 * @return string
	 * @see Df_1C_Cml2_Import_Data_Document_Catalog::getExternalId_CatalogStructure()
	 * @see Df_1C_Cml2_Import_Data_Document_Offers::getExternalId_CatalogStructure()
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

	/** @return Df_1C_Cml2_Session_ByIp */
	protected function session() {return Df_1C_Cml2_Session_ByIp::s();}

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
	 * @used-by Df_1C_Cml2_Import_Data_Document::create()
	 * @used-by Df_1C_Cml2_File_CatalogComposite::getXmlDocument()
	 * @static
	 * @param Df_Core_Sxe $e
	 * @param string $relativePath
	 * @return Df_1C_Cml2_Import_Data_Document
	 */
	public static function create(Df_Core_Sxe $e, $relativePath) {
		df_param_string_not_empty($relativePath, 1);
		/** @var string $class */
		$class =
			self::_isCatalog($e)
			? Df_1C_Cml2_Import_Data_Document_Catalog::_C
			: (
				self::_isOffers($e)
				? Df_1C_Cml2_Import_Data_Document_Offers::_C
				: df_error('Неизвестный тип документа.')
			)
		;
		return rm_ic($class, __CLASS__, array(self::$P__E => $e, self::$P__PATH => $relativePath));
	}

	/**
	 * @param Df_Core_Sxe $e
	 * @return bool
	 */
	private static function _isCatalog(Df_Core_Sxe $e) {return !!$e->descend('Каталог');}
	/**
	 * @param Df_Core_Sxe $e
	 * @return bool
	 */
	private static function _isOffers(Df_Core_Sxe $e) {return !!$e->descend('ПакетПредложений');}
}