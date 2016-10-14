<?php
class Df_Banner_Setup_0_1_1 extends Df_Core_Setup {
	/**
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		$t_DF_BANNER = rm_table(Df_Banner_Model_Resource_Banner::TABLE);
		$t_DF_BANNER_ITEM = rm_table(Df_Banner_Model_Resource_Banneritem::TABLE);
		// Обратите внимание, что удаление таблицы перед её созданием
		// позволяет нам беспроблемно проводить одну и ту же установку много раз подряд
		// (например, с целью тестирования или когда в процессе разработки
		// перед выпуском версии требуется доработать
		// ранее разработанный и запускавшийся доработать установщик).
		$this->dropTable($t_DF_BANNER);
		$this->dropTable($t_DF_BANNER_ITEM);
		/**
		 * Не используем $this->getConnection()->newTable(),
		 * потому что метод @see Varien_Db_Adapter_Pdo_Mysql::newTable()
		 * отсутствует в Magento CE 1.4.0.1.
		 */
		$this->run("
			create table if not exists {$t_DF_BANNER} (
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
			create table if not exists {$t_DF_BANNER_ITEM} (
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
}