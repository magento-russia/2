<?php
use Df_PromoGift_Model_Rule as Rule;
class Df_PromoGift_Setup_1_0_0 extends Df_Core_Setup {
	/**
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		$t_CATALOG_PRODUCT_ENTITY = df_table('catalog_product_entity');
		$t_CORE_WEBSITE = df_table('core/website');
		$_PROMO_GIFT = df_table(Df_PromoGift_Model_Resource_Gift::TABLE);
		$t_SALES_RULE = df_table('salesrule');
		// Добавляем в таблицу «salesrule» поле «df_max_usages_per_quote»
		// для ограничения числа применений правила для заказа
		df_db_column_add($t_SALES_RULE, Rule::P__MAX_USAGES_PER_QUOTE, "int(11) unsigned NOT null DEFAULT '0'");
		$this->dropTable($_PROMO_GIFT);
		$this->run("
			create table if not exists `{$_PROMO_GIFT}` (
				`gift_id` int(10) unsigned NOT null auto_increment
				-- Столбцы ниже далее назначаются внешними ключами,
				-- поэтому их типы должны соответствовать типам первичных ключей
				-- в соответствующих таблицах
				-- Обратите внимание: чтобы потом мы могли назначить для колонок ограничения связей,
				-- тип данных колонки должен совпадать с типом данных соответствующего первичного ключа
				-- той таблицы, на которую ссылается наша колонка

				-- http://dev.mysql.com/doc/refman/5.1/en/innodb-foreign-key-constraints.html
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

				-- http://dev.mysql.com/doc/refman/5.1/en/innodb-foreign-key-constraints.html
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
		 * http://dev.mysql.com/doc/refman/5.1/en/innodb-foreign-key-constraints.html
		 */
		$this->run("
			ALTER TABLE `{$_PROMO_GIFT}`
				ADD CONSTRAINT `FK_DF_PROMO_GIFT_WEBSITE`
					FOREIGN KEY (`website_id`)
					REFERENCES `{$t_CORE_WEBSITE}` (`website_id`)
					ON DELETE CASCADE
					ON UPDATE CASCADE
		  	;
		");
		$this->run("
			ALTER TABLE `{$_PROMO_GIFT}`
				ADD CONSTRAINT `FK_DF_PROMO_GIFT_RULE`
					FOREIGN KEY (`rule_id`)
					REFERENCES `{$t_SALES_RULE}` (`rule_id`)
					ON DELETE CASCADE
					ON UPDATE CASCADE
			;
		");
		$this->run("
			ALTER TABLE `{$_PROMO_GIFT}`
				ADD CONSTRAINT `FK_DF_PROMO_GIFT_PRODUCT`
					FOREIGN KEY (`product_id`)
					REFERENCES `{$t_CATALOG_PRODUCT_ENTITY}` (`entity_id`)
					ON DELETE CASCADE
					ON UPDATE CASCADE
			;
		");
	}
}