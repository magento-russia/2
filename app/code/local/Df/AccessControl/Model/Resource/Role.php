<?php
class Df_AccessControl_Model_Resource_Role extends Df_Core_Model_Resource {
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
	 * @param Mage_Core_Model_Resource_Setup $setup
	 * @return void
	 */
	public function tableCreate(Mage_Core_Model_Resource_Setup $setup) {
		$f_CATEGORIES = Df_AccessControl_Model_Role::P__CATEGORIES;
		$f_ID = Df_AccessControl_Model_Role::P__ID;
		$f_ROLE_ID = Df_Admin_Model_Role::P__ID;
		$f_STORES = Df_AccessControl_Model_Role::P__STORES;
		$t_DF_ACCESS_CONTROL_ROLE = df_table(self::$TABLE);
		$t_ADMIN_ROLE = df_table(Df_Admin_Model_Resource_Role::TABLE);
		// Обратите внимание, что удаление таблицы перед её созданием
		// позволяет нам беспроблемно проводить одну и ту же установку много раз подряд
		// (например, с целью тестирования или когда в процессе разработки
		// перед выпуском версии требуется доработать
		// ранее разработанный и запускавшийся доработать установщик).
		df_table_drop($t_DF_ACCESS_CONTROL_ROLE, $setup->getConnection());
		/**
		 * Не используем $this->getConnection()->newTable(),
		 * потому что метод @see Varien_Db_Adapter_Pdo_Mysql::newTable()
		 * отсутствует в Magento CE 1.4.0.1.
		 */
		$setup->run("
			create table `{$t_DF_ACCESS_CONTROL_ROLE}` (
				`{$f_ID}` int(10) unsigned not null primary key
				,constraint `FK___DF__ACCESS_CONTROL_ROLE__ROLE`
					foreign key (`{$f_ID}`)
					references `{$t_ADMIN_ROLE}` (`{$f_ROLE_ID}`)
					on delete cascade
				--  Товарные разделы,
				--  которыми будет ограничен доступ представителей данной роли к товарному каталогу.
				,`{$f_CATEGORIES}` text
				--  Магазины и витрины,
				--  которыми будет ограничен доступ представителей данной роли.
				,`{$f_STORES}` text
			) engine=InnoDB default charset=utf8;
		");
		// После изменения структуры базы данных надо удалить кэш,
		// потому что Magento кэширует структуру базы данных.
		df_cache_clean();
	}

	/**
	 * Нельзя вызывать @see parent::_construct(),
	 * потому что это метод в родительском классе — абстрактный.
	 * @see Mage_Core_Model_Mysql4_Abstract::_construct()
	 * @override
	 * @return void
	 */
	protected function _construct() {
		$this->_init(self::$TABLE, Df_AccessControl_Model_Role::P__ID);
		$this->_isPkAutoIncrement = false;
	}
	/**
	 * @used-by _construct()
	 * @used-by tableCreate()
	 * @var string
	 */
	private static $TABLE = 'df_access_control/role';

	/** @return Df_AccessControl_Model_Resource_Role */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}