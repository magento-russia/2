<?php
/** @method Df_Catalog_Model_XmlExport_Catalog getDocument() */
abstract class Df_Catalog_Model_XmlExport_Category extends \Df\Xml\Generator\Part {
	/** @return Df_Catalog_Model_Category */
	protected function getCategory() {return $this->_getData(self::$P__CATEGORY);}

	/** @return Df_Catalog_Model_Category[] */
	protected function getChildren() {
		return df_nta($this->getCategory()->getData(
			Df_Catalog_Model_XmlExport_Catalog::CATEGORY__CHILDREN
		));
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__DOCUMENT, Df_Catalog_Model_XmlExport_Catalog::_C)
			->_prop(self::$P__CATEGORY, Df_Catalog_Model_Category::_C)
		;
	}
	const _C = __CLASS__;
	/** @var string */
	private static $P__CATEGORY = 'product';

	/**
	 * @used-by Df_1C_Cml2_Export_Processor_Catalog_Category::process()
	 * @param string $class
	 * @param Df_Catalog_Model_Category $category
	 * @param \Df\Xml\Generator\Document $document
	 * @return Df_Catalog_Model_XmlExport_Category
	 */
	protected static function ic(
		$class
		,Df_Catalog_Model_Category $category
		,\Df\Xml\Generator\Document $document
	) {
		return df_ic($class, __CLASS__, array(
			self::$P__DOCUMENT => $document, self::$P__CATEGORY => $category
		));
	}
}