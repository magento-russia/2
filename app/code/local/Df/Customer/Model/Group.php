<?php
class Df_Customer_Model_Group extends Mage_Customer_Model_Group {
	/**
	 * @see Df_C1_Setup_2_44_0::process()
	 * @return string|null
	 */
	public function get1CId() {return $this->_getData(\Df\C1\C::ENTITY_EXTERNAL_ID);}

	/**
	 * @override
	 * @return Df_Customer_Model_Resource_Group_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/**
	 * @param string|null $value
	 * @return Df_Customer_Model_Group
	 */
	public function set1CId($value) {
		$this->setData(\Df\C1\C::ENTITY_EXTERNAL_ID, $value);
		return $this;
	}

	/**
	 * @override
	 * @return Df_Customer_Model_Resource_Group
	 * 2016-10-14
	 * В родительском классе метод переобъявлен через PHPDoc,
	 * и поэтому среда разработки думает, что он публичен
	 */
	/** @noinspection PhpHierarchyChecksInspection */
	protected function _getResource() {return Df_Customer_Model_Resource_Group::s();}

	/**
	 * @used-by \Df\C1\Cml2\Export\Processor\Catalog\CustomerGroup::_construct()
	 * @used-by Df_Customer_Model_Resource_Group_Collection::_construct()
	 * @used-by Df_Localization_Onetime_Dictionary_Rule_Conditions_CustomerGroup::getEntityClass()
	 */

	const ID__GENERAL = 1;

	/** @return Df_Customer_Model_Resource_Group_Collection */
	public static function c() {return new Df_Customer_Model_Resource_Group_Collection;}
	/** @return Df_Customer_Model_Resource_Group_Collection */
	public static function cs() {return Df_Customer_Model_Resource_Group_Collection::s();}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Customer_Model_Group
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}