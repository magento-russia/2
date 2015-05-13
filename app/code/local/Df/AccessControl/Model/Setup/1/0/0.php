<?php
class Df_AccessControl_Model_Setup_1_0_0 extends Df_Core_Model_Setup {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		/** @var string $t_ACCESS_CONTROL_ROLE */
		$t_ACCESS_CONTROL_ROLE = rm_table('df_access_control/role');
		/** @var string $t_ADMIN_ROLE */
		$t_ADMIN_ROLE = rm_table('admin_role');
		$this->getSetup()->run("DROP TABLE IF EXISTS `{$t_ACCESS_CONTROL_ROLE}`;");
		$this->getSetup()->run("
			CREATE TABLE `{$t_ACCESS_CONTROL_ROLE}` (
				`role_id` int(10) unsigned NOT null PRIMARY KEY
				,CONSTRAINT `FK___DF__ACCESS_CONTROL_ROLE__ROLE`
					FOREIGN KEY (`role_id`)
					REFERENCES `{$t_ADMIN_ROLE}` (`role_id`)
					ON DELETE CASCADE
				--  Товарные разделы,--  которыми будет ограничен доступ представителей данной роли к товарному каталогу.
				,`categories` text
				--  Магазины и витрины,--  которыми будет ограничен доступ представителей данной роли.
				,`stores` text

			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
	}

	/**
	 * @static
	 * @param Df_Core_Model_Resource_Setup $setup
	 * @return Df_AccessControl_Model_Setup_1_0_0
	 */
	public static function i(Df_Core_Model_Resource_Setup $setup) {return self::ic($setup, __CLASS__);}
}