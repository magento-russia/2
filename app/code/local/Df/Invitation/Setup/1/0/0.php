<?php
class Df_Invitation_Setup_1_0_0 extends Df_Core_Setup {
	/**
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		$t_CUSTOMER = df_table('customer/entity');
		$t_CUSTOMER_GROUP = df_table('customer/customer_group');
		$_HISTORY = df_table(Df_Invitation_Model_Resource_Invitation_History::TABLE);
		$t_INVITATION = df_table(Df_Invitation_Model_Resource_Invitation::TABLE);
		$t_TRACK = df_table('df_invitation/invitation_track');
		$t_STORE = df_table('core_store');
		$this->dropTable($t_INVITATION);
		$this->run("
			create table if not exists `{$t_INVITATION}` (
				`invitation_id` INT UNSIGNED  NOT null AUTO_INCREMENT PRIMARY KEY
				,`customer_id` INT( 10 ) UNSIGNED DEFAULT null
				,`date` DATETIME NOT null
				,`email` VARCHAR( 255 ) NOT null
				,`referral_id` INT( 10 ) UNSIGNED DEFAULT null
				,`protection_code` CHAR(32) NOT null
				,`signup_date` DATETIME DEFAULT null
				,`store_id` SMALLINT(5) UNSIGNED NOT null
				,`group_id` smallint(3) unsigned null DEFAULT null
				,`message` TEXT DEFAULT null
				,`status` enum('new','sent','accepted','canceled') NOT null DEFAULT 'new'
				,INDEX `IDX_customer_id` (`customer_id`),INDEX `IDX_referral_id` (`referral_id`)
				,CONSTRAINT `FK_INVITATION_STORE`
					FOREIGN KEY (`store_id`)
					REFERENCES `{$t_STORE}` (`store_id`)
					ON DELETE CASCADE
					ON UPDATE CASCADE
				,CONSTRAINT `FK_INVITATION_CUSTOMER`
					FOREIGN KEY (`customer_id`)
					REFERENCES `{$t_CUSTOMER}` (`entity_id`)
					ON DELETE SET null
					ON UPDATE CASCADE
				,CONSTRAINT `FK_INVITATION_REFERRAL`
					FOREIGN KEY (`referral_id`)
					REFERENCES `{$t_CUSTOMER}` (`entity_id`)
					ON DELETE SET null
					ON UPDATE CASCADE
				,CONSTRAINT `FK_INVITATION_CUSTOMER_GROUP`
					FOREIGN KEY (`group_id`)
					REFERENCES `{$t_CUSTOMER_GROUP}` (`customer_group_id`)
					ON DELETE SET null
					ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
		$this->dropTable($_HISTORY);
		$this->run("
			create table if not exists `{$_HISTORY}` (
				`history_id` INT UNSIGNED NOT null AUTO_INCREMENT PRIMARY KEY
				,`invitation_id` INT UNSIGNED NOT null
				,`date` DATETIME NOT null
				,`status` enum('new','sent','accepted','canceled') NOT null DEFAULT 'new'
				,INDEX `IDX_invitation_id` (`invitation_id`)
			,CONSTRAINT `FK_INVITATION_HISTORY_INVITATION`
				FOREIGN KEY (`invitation_id`)
				REFERENCES `{$t_INVITATION}` (`invitation_id`)
				ON DELETE CASCADE
				ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
		$this->run("
			create table if not exists `{$t_TRACK}` (
				`track_id` int(10) unsigned NOT null AUTO_INCREMENT
				,`inviter_id` int(10) unsigned NOT null DEFAULT 0
				,`referral_id` int(10) unsigned NOT null DEFAULT 0
				,PRIMARY KEY (`track_id`)
				,UNIQUE KEY `UNQ_INVITATION_TRACK_IDS` (`inviter_id`,`referral_id`)
				,KEY `FK_INVITATION_TRACK_REFERRAL` (`referral_id`)
				,CONSTRAINT `FK_INVITATION_TRACK_INVITER`
					FOREIGN KEY (`inviter_id`)
					REFERENCES `{$t_CUSTOMER}` (`entity_id`)
					ON DELETE CASCADE
					ON UPDATE CASCADE	
				,CONSTRAINT `FK_INVITATION_TRACK_REFERRAL`
					FOREIGN KEY (`referral_id`)
					REFERENCES `{$t_CUSTOMER}` (`entity_id`)
					ON DELETE CASCADE
					ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
	}
}