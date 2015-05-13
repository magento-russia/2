<?php
/**
 * @method Mage_Eav_Model_Resource_Entity_Attribute_Set getResource()
 */
class Df_Eav_Model_Entity_Attribute_Set extends Mage_Eav_Model_Entity_Attribute_Set {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Eav_Model_Resource_Entity_Attribute_Set::mf());
	}

	const _CLASS = __CLASS__;
	const P__NAME = 'attribute_set_name';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Eav_Model_Entity_Attribute_Set
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Eav_Model_Entity_Attribute_Set
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
	/** @return Df_Eav_Model_Entity_Attribute_Set */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
}