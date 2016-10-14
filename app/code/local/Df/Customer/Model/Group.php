<?php
class Df_Customer_Model_Group extends Mage_Customer_Model_Group {
	/**
	 * @see Df_1C_Setup_2_44_0::process()
	 * @return string|null
	 */
	public function get1CId() {return $this->_getData(Df_1C_Const::ENTITY_EXTERNAL_ID);}

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
		$this->setData(Df_1C_Const::ENTITY_EXTERNAL_ID, $value);
		return $this;
	}

	/**
	 * @override
	 * @return Df_Customer_Model_Resource_Group
	 */
	protected function _getResource() {return Df_Customer_Model_Resource_Group::s();}

	/**
	 * @used-by Df_1C_Cml2_Export_Processor_Catalog_CustomerGroup::_construct()
	 * @used-by Df_Customer_Model_Resource_Group_Collection::_construct()
	 * @used-by Df_Localization_Onetime_Dictionary_Rule_Conditions_CustomerGroup::getEntityClass()
	 */
	const _C = __CLASS__;
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
	/** @return Df_Customer_Model_Group */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}