<?php
/**
 * @method string getValue()
 */
class Df_Eav_Model_Entity_Attribute_Option extends Mage_Eav_Model_Entity_Attribute_Option {
	/** @return string|null */
	public function get1CId() {return $this->_getData(\Df\C1\C::ENTITY_EXTERNAL_ID);}

	/**
	 * 2015-02-06
	 * По аналогии с @see Df_Catalog_Model_Product::getId()
	 * Читайте подробный комментарий в заголовке этого метода.
	 * @override
	 * @return int|null
	 */
	public function getId() {
		/** @var int|null $result */
		$result = parent::getId();
		return is_null($result) ? null : (int)$result;
	}

	/**
	 * @override
	 * @return Df_Eav_Model_Resource_Entity_Attribute_Option_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/**
	 * @param string|null $value
	 * @return Df_Eav_Model_Entity_Attribute_Option
	 */
	public function set1CId($value) {
		$this->setData(\Df\C1\C::ENTITY_EXTERNAL_ID, $value);
		return $this;
	}

	/**
	 * @override
	 * @return Df_Eav_Model_Resource_Entity_Attribute_Option
	 * 2016-10-14
	 * В родительском классе метод переобъявлен через PHPDoc,
	 * и поэтому среда разработки думает, что он публичен.
	 */
	/** @noinspection PhpHierarchyChecksInspection */
	protected function _getResource() {return Df_Eav_Model_Resource_Entity_Attribute_Option::s();}

	/** @used-by Df_Eav_Model_Resource_Entity_Attribute_Option_Collection::_construct() */

	/** @return Df_Eav_Model_Resource_Entity_Attribute_Option_Collection */
	public static function c() {return new Df_Eav_Model_Resource_Entity_Attribute_Option_Collection;}
}