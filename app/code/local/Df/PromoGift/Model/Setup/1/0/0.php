<?php
class Df_PromoGift_Model_Setup_1_0_0 extends Df_Core_Model_Setup {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		/** @var string $t_CATALOG_PRODUCT_ENTITY */
		$t_CATALOG_PRODUCT_ENTITY = rm_table('catalog_product_entity');
		/** @var string $t_CORE_WEBSITE */
		$t_CORE_WEBSITE = rm_table('core/website');
		/** @var string $_PROMO_GIFT */
		$_PROMO_GIFT = rm_table(Df_PromoGift_Const::DB__PROMO_GIFT);		
		/** @var string $t_SALES_RULE */
		$t_SALES_RULE = rm_table('salesrule');
		// Добавляем в таблицу «salesrule» поле «df_max_usages_per_quote»
		// для ограничения числа применений правила для заказа
		/** @var string $column */
		$column = Df_PromoGift_Const::DB__SALES_RULE__MAX_USAGES_PER_QUOTE;
		$this->getSetup()->run("
			ALTER TABLE `{$t_SALES_RULE}` ADD COLUMN `{$column}` int(11) unsigned NOT null DEFAULT '0';
		");
		// Создаём таблицу промо-подарков
		$this->getSetup()->run("DROP TABLE IF EXISTS `{$_PROMO_GIFT}`;");
		$this->getSetup()->run("
			CREATE TABLE `{$_PROMO_GIFT}` (
				`gift_id` int(10) unsigned NOT null auto_increment
				-- Столбцы ниже далее назначаются внешними ключами,
				-- поэтому их типы должны соответствовать типам первичных ключей
				-- в соответствующих таблицах
				-- Обратите внимание: чтобы потом мы могли назначить для колонок ограничения связей,
				-- тип данных колонки должен совпадать с типом данных соответствующего первичного ключа
				-- той таблицы, на которую ссылается наша колонка

				-- @link http://dev.mysql.com/doc/refman/5.1/en/innodb-foreign-key-constraints.html
				-- «Corresponding columns in the foreign key and the referenced key
				-- must have similar internal data types inside InnoDB
				-- so that they can be compared without a type conversion.

				-- The size and sign of integer types must be the same.
				-- The length of string types need not be the same.
				-- For nonbinary (character) string columns,
				-- the character set and collation must be the same.»

				-- Наличие столбца website_id не является необходимым,
				-- но позволит нам удобно фильтровать подарки на витрине
				,`website_id` smallint(5) unsigned default null
				,`rule_id` int(10) unsigned NOT null
				,`product_id` int(10) unsigned NOT null
				, PRIMARY KEY  (`gift_id`)
				-- InnoDB требует наличия индексов для ограниченных внешних ключей.
				-- Однако InnoDB может создавать такие индексы автоматически, так что, видимо
				-- явное их определение в коде ниже не является необходимым

				-- @link http://dev.mysql.com/doc/refman/5.1/en/innodb-foreign-key-constraints.html
				-- InnoDB requires indexes on foreign keys and referenced keys
				-- so that foreign key checks can be fast and not require a table scan.

				-- In the referencing table, there must be an index
				-- where the foreign key columns are listed as the first columns in the same order.

				-- Such an index is created on the referencing table automatically if it does not exist.
				-- (This is in contrast to some older versions,
				-- in which indexes had to be created explicitly
				-- or the creation of foreign key constraints would fail.)

				-- index_name, if given, is used as described previously.
				, KEY `IDX_WEBSITE` (`website_id`)
				, KEY `IDX_RULE` (`rule_id`)
				, KEY `IDX_PRODUCT` (`product_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
		/**
		 * Добавляем к таблице «df_promo_gift» ограничения связей.
		 * @link http://dev.mysql.com/doc/refman/5.1/en/innodb-foreign-key-constraints.html
		 */
		$this->getSetup()->run("
			ALTER TABLE `{$_PROMO_GIFT}`
				ADD CONSTRAINT `FK_DF_PROMO_GIFT_WEBSITE`
					FOREIGN KEY (`website_id`)
					REFERENCES `{$t_CORE_WEBSITE}` (`website_id`)
					ON DELETE CASCADE
					ON UPDATE CASCADE
		  	;
		");
		$this->getSetup()->run("
			ALTER TABLE `{$_PROMO_GIFT}`
				ADD CONSTRAINT `FK_DF_PROMO_GIFT_RULE`
					FOREIGN KEY (`rule_id`)
					REFERENCES `{$t_SALES_RULE}` (`rule_id`)
					ON DELETE CASCADE
					ON UPDATE CASCADE
			;
		");
		$this->getSetup()->run("
			ALTER TABLE `{$_PROMO_GIFT}`
				ADD CONSTRAINT `FK_DF_PROMO_GIFT_PRODUCT`
					FOREIGN KEY (`product_id`)
					REFERENCES `{$t_CATALOG_PRODUCT_ENTITY}` (`entity_id`)
					ON DELETE CASCADE
					ON UPDATE CASCADE
			;
		");
		rm_cache_clean();
	}

	/**
	 * @static
	 * @param Df_Core_Model_Resource_Setup $setup
	 * @return Df_PromoGift_Model_Setup_1_0_0
	 */
	public static function i(Df_Core_Model_Resource_Setup $setup) {return self::ic($setup, __CLASS__);}
}