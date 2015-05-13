<?php
class Df_Banner_Model_Setup_0_1_1 extends Df_Core_Model_Setup {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		/** @var string $t_DF_BANNER */
		$t_DF_BANNER = rm_table('df_banner');
		/** @var string $t_DF_BANNER_ITEM */
		$t_DF_BANNER_ITEM = rm_table('df_banner_item');
		$this->getSetup()->run("
			DROP TABLE IF EXISTS {$t_DF_BANNER};
			CREATE TABLE {$t_DF_BANNER} (
				`banner_id` int(11) unsigned NOT null auto_increment,
				`identifier` varchar(255) NOT null default '',
				`title` varchar(255) NOT null default '',
				`show_title` smallint(6) NOT null default '0',
				`content` text null default '',
				`width` int(11) unsigned null,
				`height` int(11) unsigned null,
				`delay` int(11) unsigned null,
				`status` smallint(6) NOT null default '0',
				`active_from` datetime null,
				`active_to` datetime null,
				`created_time` datetime null,
				`update_time` datetime null,
				PRIMARY KEY (`banner_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			DROP TABLE IF EXISTS {$t_DF_BANNER_ITEM};
			CREATE TABLE {$t_DF_BANNER_ITEM} (
				`banner_item_id` int(11) unsigned NOT null auto_increment,
				`banner_id` int(11) unsigned NOT null,
				`title` varchar(255) NOT null default '',
				`image` varchar(255) NOT null default '',
				`image_url` varchar(512) NOT null default '',
				`thumb_image` varchar(255) NOT null default '',
				`thumb_image_url` varchar(512) NOT null default '',
				`content` text null default '',
				`link_url` varchar(512) NOT null default '#',
				`status` smallint(6) NOT null default '0',
				`created_time` datetime null,
				`update_time` datetime null,
			PRIMARY KEY (`banner_item_id`),
			CONSTRAINT `FK_DF_BANNER_ITEM`
			FOREIGN KEY (`banner_id`)
				REFERENCES `{$t_DF_BANNER}` (`banner_id`)
				ON DELETE CASCADE ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
	}

	/**
	 * @static
	 * @param Df_Core_Model_Resource_Setup $setup
	 * @return Df_Banner_Model_Setup_0_1_1
	 */
	public static function i(Df_Core_Model_Resource_Setup $setup) {return self::ic($setup, __CLASS__);}
}