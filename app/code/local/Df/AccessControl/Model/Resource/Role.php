<?php
class Df_AccessControl_Model_Resource_Role extends Mage_Core_Model_Mysql4_Abstract {
	/** @return Df_AccessControl_Model_Resource_Role */
	public function prepareForInsert() {
		$this->_useIsObjectNew = true;
		return $this;
	}

	/**
	 * @param int $roleId
	 * @param bool $on
	 * @return Df_AccessControl_Model_Resource_Role
	 */
	public function setEnabled($roleId, $on) {
		df_param_integer($roleId, 0);
		df_param_boolean($on, 1);
		/** @var Df_AccessControl_Model_Role $role */
		$role = Df_AccessControl_Model_Role::ld($roleId);
		if ($on && !$role->isModuleEnabled()) {
			$role
				->setId($roleId)
				->save()
			;
		}
		else if (!$on && $role->isModuleEnabled()) {
			$role->delete();
		}
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		/**
		 * Нельзя вызывать parent::_construct(),
		 * потому что это метод в родительском классе — абстрактный.
		 * @see Mage_Core_Model_Resource_Abstract::_construct()
		 */
		$this->_init(self::MAIN_TABLE, self::PRIMARY_KEY);
		$this->_isPkAutoIncrement = false;
	}
	const _CLASS = __CLASS__;
	const FIELD__ROLE_ID = 'role_id';
	const MAIN_TABLE = 'df_access_control/role';
	const PRIMARY_KEY = 'role_id';

	/**
	 * Используется в
	 * @see Df_AccessControl_Model_Role::_construct()
	 * @see Df_AccessControl_Model_Resource_Role_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_AccessControl_Model_Resource_Role */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}