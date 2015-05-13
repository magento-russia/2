<?php
class Df_Invitation_Model_Setup_1_0_0 extends Df_Core_Model_Setup {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		/** @var string $t_CUSTOMER */
		$t_CUSTOMER = rm_table('customer/entity');
		/** @var string $t_CUSTOMER_GROUP */
		$t_CUSTOMER_GROUP = rm_table('customer/customer_group');
		/** @var string $_HISTORY */
		$_HISTORY = rm_table(Df_Invitation_Model_Resource_Invitation_History::TABLE_NAME);
		/** @var string $t_INVITATION */
		$t_INVITATION = rm_table(Df_Invitation_Model_Resource_Invitation::TABLE_NAME);		
		/** @var string $t_TRACK */
		$t_TRACK = rm_table('df_invitation/invitation_track');		
		/** @var string $t_STORE */
		$t_STORE = rm_table('core_store');
		$this->getSetup()->run("
		DROP TABLE IF EXISTS `{$t_INVITATION}`;
		CREATE TABLE `{$t_INVITATION}` (
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
		$this->getSetup()->run("
			DROP TABLE IF EXISTS `{$_HISTORY}`;
			CREATE TABLE `{$_HISTORY}` (
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
		$this->getSetup()->run("
			CREATE TABLE `{$t_TRACK}` (
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
		rm_cache_clean();
	}

	/**
	 * @static
	 * @param Df_Core_Model_Resource_Setup $setup
	 * @return Df_Invitation_Model_Setup_1_0_0
	 */
	public static function i(Df_Core_Model_Resource_Setup $setup) {return self::ic($setup, __CLASS__);}
}