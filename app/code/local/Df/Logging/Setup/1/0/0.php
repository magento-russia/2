<?php
class Df_Logging_Setup_1_0_0 extends Df_Core_Setup {
	/**
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		$t_LOG = rm_table(Df_Logging_Model_Resource_Event::TABLE);
		$t_CHANGES = rm_table(Df_Logging_Model_Resource_Event_Changes::TABLE);
		$t_USER = rm_table('admin/user');
		$this->dropTable($t_LOG);
		$this->run("
			create table if not exists `{$t_LOG}` (
				`log_id` int(11) NOT null auto_increment
				,`ip` bigint(20) NOT null default '0'
				,`x_forwarded_ip` bigint(20) unsigned NOT null default '0'
				,`event_code` varchar(100) NOT null default ''
				,`time` datetime NOT null default '0000-00-00 00:00:00'
				,`action` char(20) NOT null default '-'
				,`info` varchar(255) NOT null default '-'
				,`status` char(15) NOT null default 'success'
				,`user` varchar(40) NOT null default ''
				,`user_id` mediumint(9) unsigned null DEFAULT null
				,`fullaction` varchar(200) NOT null default '-'
				,`error_message` text DEFAULT null
				,PRIMARY KEY (`log_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8
		");
		$this->conn()->addConstraint(
			'FK_LOGGING_EVENT_USER'
			,$t_LOG
			,'user_id'
			,$t_USER
			,'user_id'
			,'SET null'
		);
		$this->conn()->addKey($t_LOG, 'IDX_LOGGING_EVENT_USERNAME', 'user');
		$this->dropTable($t_CHANGES);
		$this->run("
			create table if not exists `{$t_CHANGES}` (
				`id` int(11) NOT null AUTO_INCREMENT,
				`source_name` VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT null,
				`event_id` int(11) DEFAULT null,
				`source_id` INT( 11 ) null DEFAULT null,
				`original_data` text NOT null,
				`result_data` text NOT null,
				PRIMARY KEY (`id`),
				KEY `event_id` (`event_id`),
				CONSTRAINT `FK_LOGGING_EVENT_CHANGES_EVENT_ID`
					FOREIGN KEY (`event_id`) REFERENCES `{$t_LOG}` (`log_id`)
					ON DELETE CASCADE ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8
		");
	}
}