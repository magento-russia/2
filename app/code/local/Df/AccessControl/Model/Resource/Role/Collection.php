<?php
class Df_AccessControl_Model_Resource_Role_Collection
	extends Mage_Core_Model_Mysql4_Collection_Abstract {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_AccessControl_Model_Role::mf(), Df_AccessControl_Model_Resource_Role::mf());
	}
	/** @var string */
	protected $_eventPrefix = 'df_access_control_role_collection';
	/** @var string */
	protected $_eventObject = 'role_collection';

	const _CLASS = __CLASS__;
	/** @return Df_AccessControl_Model_Resource_Role_Collection */
	public static function i() {return new self;}
}