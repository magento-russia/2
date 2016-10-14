<?php
class Df_Cms_Setup_2_0_0 extends Df_Core_Setup {
	/**
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 * @throws Exception
	 */
	protected function _process() {
		$t_ADMIN_USER = df_table('admin/user');
		$t_HIERARCHY_LOCK = df_table(Df_Cms_Model_Resource_Hierarchy_Lock::TABLE);
		$t_HIERARCHY_METADATA = df_table(Df_Cms_Model_Resource_Hierarchy_Node::TABLE_META_DATA);
		$t_HIERARCHY_NODE = df_table(Df_Cms_Model_Resource_Hierarchy_Node::TABLE);
		$t_INCREMENT = df_table(Df_Cms_Model_Resource_Increment::TABLE);
		$t_PAGE = df_table('cms/page');
		$t_PAGE_REVISION = df_table(Df_Cms_Model_Resource_Page_Revision::TABLE);
		$t_PAGE_VERSION = df_table(Df_Cms_Model_Resource_Page_Version::TABLE);
		$this->run("
			CREATE TABLE IF NOT EXISTS `{$t_PAGE_VERSION}` (
				`version_id` INT(10) UNSIGNED NOT null AUTO_INCREMENT,
				`label` VARCHAR(255) DEFAULT null,
				`access_level` ENUM('private','protected','public') NOT null,
				`page_id` SMALLINT(6) NOT null,
				`user_id` MEDIUMINT(9) UNSIGNED DEFAULT null,
				`revisions_count` INT(11) UNSIGNED DEFAULT null,
				`version_number` INT(11) UNSIGNED NOT null,
				`created_at` DATETIME NOT null DEFAULT '0000-00-00 00:00:00',
				PRIMARY KEY (`version_id`),
				KEY `IDX_PAGE_ID` (`page_id`),
				KEY `IDX_USER_ID` (`user_id`),
				KEY `IDX_VERSION_NUMBER` (`version_number`),
				CONSTRAINT `FK_CMS_VERSION_PAGE_ID`
					FOREIGN KEY (`page_id`)
					REFERENCES `{$t_PAGE}` (`page_id`)
				ON DELETE CASCADE ON UPDATE CASCADE,
				CONSTRAINT `FK_CMS_VERSION_USER_ID`
					FOREIGN KEY (`user_id`)
					REFERENCES `{$t_ADMIN_USER}` (`user_id`)
					ON DELETE SET null ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			CREATE TABLE IF NOT EXISTS `{$t_PAGE_REVISION}` (
				`revision_id` INT(10) UNSIGNED NOT null AUTO_INCREMENT,
				`version_id` INT(10) UNSIGNED NOT null,
				`page_id` SMALLINT(6) NOT null,
				`root_template` VARCHAR(255) DEFAULT null,
				`meta_keywords` TEXT DEFAULT null,
				`meta_description` TEXT DEFAULT null,
				`content_heading` VARCHAR(255) DEFAULT null,
				`content` MEDIUMTEXT DEFAULT null,
				`created_at` DATETIME NOT null DEFAULT '0000-00-00 00:00:00',
				`layout_update_xml` TEXT DEFAULT null,
				`custom_theme` VARCHAR(100) DEFAULT null,
				`custom_root_template` VARCHAR(255) DEFAULT null,
				`custom_layout_update_xml` TEXT DEFAULT null,
				`custom_theme_from` DATE DEFAULT null,
				`custom_theme_to` DATE DEFAULT null,
				`user_id` MEDIUMINT(9) UNSIGNED DEFAULT null,
				`revision_number` INT(11) UNSIGNED NOT null,
				PRIMARY KEY (`revision_id`),
				KEY `IDX_VERSION_ID` (`version_id`),
				KEY `IDX_PAGE_ID` (`page_id`),
				KEY `IDX_USER_ID` (`user_id`),
				KEY `IDX_REVISION_NUMBER` (`revision_number`),
				CONSTRAINT `FK_CMS_REVISION_PAGE_ID`
					FOREIGN KEY (`page_id`)
					REFERENCES `{$t_PAGE}` (`page_id`)
					ON DELETE CASCADE ON UPDATE CASCADE,
				CONSTRAINT `FK_CMS_REVISION_USER_ID`
					FOREIGN KEY (`user_id`)
					REFERENCES `{$t_ADMIN_USER}` (`user_id`)
					ON DELETE SET null ON UPDATE CASCADE,
				CONSTRAINT `FK_CMS_REVISION_VERSION_ID`
					FOREIGN KEY (`version_id`)
					REFERENCES `{$t_PAGE_VERSION}` (`version_id`)
					ON DELETE CASCADE ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			CREATE TABLE IF NOT EXISTS `{$t_INCREMENT}` (
				`increment_id` INT(10) UNSIGNED NOT null AUTO_INCREMENT,
				`type` INT(10) NOT null,
				`node` INT(10) UNSIGNED NOT null,
				`level` INT(10) UNSIGNED NOT null,
				`last_id` INT(11) UNSIGNED NOT null,
				PRIMARY KEY (`increment_id`),
				UNIQUE KEY `IDX_TYPE_NODE_LEVEL` (`type`,`node`,`level`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			CREATE TABLE IF NOT EXISTS `{$t_HIERARCHY_METADATA}` (
				`node_id` INT(10) UNSIGNED NOT null,
				`pager_visibility` TINYINT(4) UNSIGNED NOT null,
				`pager_frame` SMALLINT(6) UNSIGNED NOT null,
				`pager_jump` SMALLINT(6) UNSIGNED NOT null,
				`menu_visibility` TINYINT(4) UNSIGNED NOT null,
				`menu_excluded` tinyint(4) unsigned NOT null DEFAULT '0',
				`menu_layout` varchar(50) NOT null DEFAULT '',
				`menu_brief` tinyint(4) unsigned NOT null DEFAULT '0',
				`menu_levels_down` TINYINT(4) UNSIGNED NOT null,
				`menu_ordered` TINYINT(4) UNSIGNED NOT null,
				`menu_list_type` VARCHAR(50) NOT null DEFAULT '',
				PRIMARY KEY (`node_id`),
					CONSTRAINT `FK_DF_CMS_HIERARCHY_METADATA_NODE`
					FOREIGN KEY (`node_id`)
					REFERENCES `{$t_HIERARCHY_NODE}` (`node_id`)
					ON DELETE CASCADE ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			CREATE TABLE IF NOT EXISTS `{$t_HIERARCHY_NODE}` (
				`node_id` INT(10) UNSIGNED NOT null AUTO_INCREMENT,
				`parent_node_id` INT(10) UNSIGNED DEFAULT null,
				`page_id` SMALLINT(6) DEFAULT null,
				`identifier` VARCHAR(100) DEFAULT null,
				`label` VARCHAR(255) DEFAULT null,
				`level` TINYINT(3) UNSIGNED NOT null DEFAULT '0',
				`sort_order` INT(11) NOT null,
				`request_url` VARCHAR(255) NOT null,
				`xpath` VARCHAR(255) DEFAULT '',
				PRIMARY KEY (`node_id`),
				UNIQUE KEY `UNQ_REQUEST_URL` (`request_url`),
				KEY `IDX_PARENT_NODE` (`parent_node_id`),
				KEY `IDX_PAGE` (`page_id`),
				CONSTRAINT `FK_DF_CMS_HIERARCHY_NODE_PAGE`
					FOREIGN KEY (`page_id`)
					REFERENCES `{$t_PAGE}` (`page_id`)
					ON DELETE CASCADE ON UPDATE CASCADE,
					CONSTRAINT `FK_DF_CMS_HIERARCHY_NODE_PARENT_NODE`
				FOREIGN KEY (`parent_node_id`)
					REFERENCES `{$t_HIERARCHY_NODE}` (`node_id`)
					ON DELETE CASCADE ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			CREATE TABLE IF NOT EXISTS `{$t_HIERARCHY_LOCK}` (
				`lock_id` INT(10) UNSIGNED NOT null AUTO_INCREMENT,
				`user_id` MEDIUMINT(9) UNSIGNED NOT null,
				`user_name` VARCHAR(50) NOT null,
				`session_id` VARCHAR(50) NOT null,
				`started_at` INT(11) UNSIGNED NOT null,
				PRIMARY KEY (`lock_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
		$this->conn()->addColumn($t_PAGE, 'published_revision_id', ' int(10) unsigned default null');
		$this->conn()->addColumn($t_PAGE, 'website_root', "tinyint(1) NOT null default '1'");
		$this->conn()->addColumn($t_PAGE, 'under_version_control', 'tinyint(1) unsigned default 0');
		$attributes = array(
			'root_template','meta_keywords','meta_description','content','layout_update_xml'
			,'custom_theme','custom_theme_from','custom_theme_to'
		);
		$resource =
			$this->conn()->query(
				$this->conn()->select()
					->from(array('p' => $t_PAGE))
					->joinLeft(array('v' => $t_PAGE_REVISION), 'v.page_id = p.page_id', array())
					->where('v.page_id is null')
			)
		;
		try {
			$this->conn()->beginTransaction();
			while (true) {
				$page = $resource->fetch(Zend_Db::FETCH_ASSOC);
				if (!$page) {
					break;
				}
				$this->conn()->insert($t_INCREMENT, array(
					'type' => 0
					,'node' => $page['page_id']
					,'level' => 0
					,'last_id' => 1
				));
				$this->conn()->insert($t_PAGE_VERSION, array(
					'version_number' => 1
					,'page_id' => $page['page_id']
					,'access_level' => Df_Cms_Model_Page_Version::ACCESS_LEVEL_PUBLIC
					,'user_id' => null
					,'revisions_count' => 1
					,'label' => $page['title']
				));
				$versionId = $this->conn()->lastInsertId($t_PAGE_VERSION, 'version_id');
				$this->conn()->insert($t_INCREMENT, array(
					'type' => 0
					,'node' => $versionId
					,'level' => 1
					,'last_id' => 1
				));
				$_data = array();
				foreach ($attributes as $attr) {
					$_data[$attr] = $page[$attr];
				}
				$_data = array_merge($_data, array(
					'created_at' => date('Y-m-d')
					,'user_id' => null
					,'revision_number' => 1
					,'version_id' => $versionId
					,'page_id' => $page['page_id']
				));
				$this->conn()->insert($t_PAGE_REVISION, $_data);
			}
			$this->conn()->commit();
		} catch (Exception $e) {
			$this->conn()->rollback();
			throw $e;
		}
		$this->conn()->query("
			UPDATE {$t_PAGE} as p
				SET published_revision_id = (
					SELECT revision_id
						FROM
							{$t_PAGE_VERSION} as v
							, {$t_PAGE_REVISION} as r
						WHERE
								(v.page_id = p.page_id)
							AND
								('public' = v.access_level)
							AND
								(r.version_id = v.version_id)
							AND
								(r.page_id = p.page_id)
						ORDER BY revision_id
						DESC LIMIT 1
				)
				WHERE p.published_revision_id is null
		");
	}
}