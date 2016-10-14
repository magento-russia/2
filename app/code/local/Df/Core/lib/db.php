<?php
/** @return Varien_Db_Adapter_Pdo_Mysql|Varien_Db_Adapter_Interface */
function df_conn() {return df_db_resource()->getConnection('write');}

/**
 * 2016-01-27
 * @param string $identifier
 * @return string
 */
function df_db_quote($identifier) {return df_conn()->quoteIdentifier($identifier);}

/**
 * @param string $text
 * @param mixed $value
 * @param string|null $type [optional]
 * @param int|null $count [optional]
 * @return string
 */
function df_db_quote_into($text, $value, $type = null, $count = null) {
	return df_conn()->quoteInto($text, $value, $type, $count);
}

/**
 * 2016-03-26
 * @return Mage_Core_Model_Resource_Transaction
 */
function df_db_transaction() {return df_model('core/resource_transaction');}

/**
 * 2015-09-29
 * @return Mage_Core_Model_Resource
 */
function df_db_resource() {return df_mage()->core()->resource();}

/**
 * 2015-04-14
 * @param string $table
 * @param string|null $cCompare [optional]
 * @param int|string|int[]|string[]|null $values [optional]
 * @return array(array(string => string))
 */
function df_fetch_all($table, $cCompare = null, $values = null) {
	/** @var Varien_Db_Select $select */
	$select = df_select()->from(df_table($table));
	if (!is_null($values)) {
		$select->where($cCompare . ' ' . df_sql_predicate_simple($values), $values);
	}
	return df_conn()->fetchAssoc($select);
}

/**
 * 2015-04-13
 * @used-by df_fetch_col_int()
 * @used-by Df_Localization_Onetime_DemoImagesImporter_Image_Collection::loadInternal()
 * @param string $table
 * @param string $cSelect
 * @param string|null $cCompare [optional]
 * @param int|string|int[]|string[]|null $values [optional]
 * @param bool $distinct [optional]
 * @return int[]|string[]
 */
function df_fetch_col($table, $cSelect, $cCompare = null, $values = null, $distinct = false) {
	/** @var Varien_Db_Select $select */
	$select = df_select()->from(df_table($table), $cSelect);
	if (!is_null($values)) {
		if (!$cCompare) {
			$cCompare = $cSelect;
		}
		$select->where($cCompare . ' ' . df_sql_predicate_simple($values), $values);
	}
	$select->distinct($distinct);
	return df_conn()->fetchCol($select, $cSelect);
}

/**
 * 2015-04-13
 * @used-by df_fetch_col_int_unique()
 * @used-by Df_Catalog_Model_Processor_DeleteOrphanCategoryAttributesData::_process()
 * @used-by Df_Logging_Model_Resource_Event::getEventChangeIds()
 * @used-by Df_Tax_Setup_3_0_0::customerClassId()
 * @used-by Df_Tax_Setup_3_0_0::deleteDemoRules()
 * @used-by Df_Tax_Setup_3_0_0::taxClassIds()
 * @param string $table
 * @param string $cSelect
 * @param string|null $cCompare [optional]
 * @param int|string|int[]|string[]|null $values [optional]
 * @param bool $distinct [optional]
 * @return int[]|string[]
 */
function df_fetch_col_int($table, $cSelect, $cCompare = null, $values = null, $distinct = false) {
	/** намеренно не используем @see df_int() ради ускорения */
	return df_int_simple(df_fetch_col($table, $cSelect, $cCompare, $values, $distinct));
}

/**
 * 2015-04-13
 * @used-by Df_Catalog_Model_Resource_Product_Collection::getCategoryIds()
 * @param string $table
 * @param string $cSelect
 * @param string|null $cCompare [optional]
 * @param int|string|int[]|string[]|null $values [optional]
 * @return int[]|string[]
 */
function df_fetch_col_int_unique($table, $cSelect, $cCompare = null, $values = null) {
	return df_fetch_col_int($table, $cSelect, $cCompare, $values, $distinct = true);
}

/**
 * 2016-01-26
 * https://mage2.pro/t/557
 * «How to get the maximum value of a database table's column programmatically».
 * @param string $table
 * @param string $cSelect
 * @param string|null $cCompare [optional]
 * @param int|string|int[]|string[]|null $values [optional]
 * @return int|float
 */
function df_fetch_col_max($table, $cSelect, $cCompare = null, $values = null) {
	/** @var Varien_Db_Select $select */
	$select = df_select()->from(df_table($table), "MAX($cSelect)");
	if (!is_null($values)) {
		if (!$cCompare) {
			$cCompare = $cSelect;
		}
		$select->where($cCompare . ' ' . df_sql_predicate_simple($values), $values);
	}
	/**
	 * 2016-03-01
	 * @uses \Zend_Db_Adapter_Abstract::fetchOne() возвращает false при пустом результате запроса.
	 * https://mage2.pro/t/853
	 */
	return df_conn()->fetchOne($select, $cSelect) ?: 0;
}

/**
 * 2015-11-03
 * @param $table
 * @param string $cSelect
 * @param array(string => string) $cCompare
 * @return string|null
 */
function df_fetch_one($table, $cSelect, $cCompare) {
	/** @var Varien_Db_Select $select */
	$select = df_select()->from(df_table($table), $cSelect);
	foreach ($cCompare as $column => $value) {
		/** @var string $column */
		/** @var string $value */
		$select->where('? = ' . $column, $value);
	}
	/**
	 * 2016-03-01
	 * @uses \Zend_Db_Adapter_Abstract::fetchOne() возвращает false при пустом результате запроса.
	 * https://mage2.pro/t/853
	 */
	return df_ftn(df_conn()->fetchOne($select));
}

/**
 * 2015-11-03
 * @param $table
 * @param string $cSelect
 * @param array(string => string) $cCompare
 * @return int
 */
function df_fetch_one_int($table, $cSelect, $cCompare) {
	return df_int(df_fetch_one($table, $cSelect, $cCompare));
}

/**
 * 2015-08-23
 * Обратите внимание, что метод
 * @see Varien_Db_Adapter_Pdo_Mysql::getPrimaryKeyName()
 * возвращает не название колонки, а слово «PRIMARY»,
 * поэтому он нам не подходит.
 * @used-by Df_Localization_Onetime_Dictionary_Db_Table::primaryKey()
 * @param string $table
 * @return string|null
 */
function df_primary_key($table) {
	/** @var array(string => string|null) */
	static $cache;
	if (!isset($cache[$table])) {
		$cache[$table] = rm_n_set(df_first(df_nta(dfa_deep(
			df_conn()->getIndexList($table), 'PRIMARY/COLUMNS_LIST'
		))));
	}
	return rm_n_get($cache[$table]);
}

/**
 * 2015-09-29
 * @return Varien_Db_Select
 */
function df_select() {return df_conn()->select();}

/**
 * 2015-04-13
 * @used-by df_fetch_col()
 * @used-by df_table_delete()
 * @param int|string|int[]|string[] $values
 * @param bool $not [optional]
 * @return string
 */
function df_sql_predicate_simple($values, $not = false) {
	return is_array($values) ? ($not ? 'NOT IN (?)' : 'IN (?)') : ($not ? '<> ?' : '= ?');
}

/**
 * @uses Mage_Core_Model_Resource::getTableName() не кэширует результаты своей работы,
 * и, глядя на реализацию @see Mage_Core_Model_Resource_Setup::getTable(),
 * которая выполняет кэширование для @see Mage_Core_Model_Resource::getTableName(),
 * я решил сделать аналогичную функцию, только доступную в произвольном контексте.
 * @param string|string[] $name
 * @return string
 */
function df_table($name) {
	if (is_array($name)) {
		/** @var string $message */
		$message = sprintf(
			'Метод df_table вызван с параметром-массивом.'
			."\nТакой вызов не поддеживается в Magento CE 1.4."
			. "\nРоссийская сборка Magento должна поддеживать эту версию Magento CE,"
			. ' поэтому в качестве параметра df_table используйте только строку.'
			. "\nПараметр-массив:%s."
			,print_r($name, $return = true)
		);
		df_notify_me($message);
		df_error($message);
	}
	/** @var array(string => string) $cache */
	static $cache;
	/**
	 * По аналогии с @see Mage_Core_Model_Resource_Setup::_getTableCacheName()
	 * @var string $key
	 */
	$key = is_array($name) ? implode('_', $name) : $name;
	if (!isset($cache[$key])) {
		$cache[$key] = df_mage()->core()->resource()->getTableName($name);
	}
	return $cache[$key];
}

/**
 * 2015-04-12
 * @used-by df_table_delete_not()
 * @used-by Df_Bundle_Model_Resource_Bundle::deleteAllOptions()
 * @used-by Df_Tax_Setup_3_0_0::customerClassId()
 * @used-by Df_Tax_Setup_3_0_0::deleteDemoData()
 * @used-by Df_Cms_Model_Resource_Hierarchy_Node::deleteNodesByPageId()
 * @used-by Df_Cms_Model_Resource_Hierarchy_Node::dropNodes()
 * @used-by Df_Directory_Setup_Processor_InstallRegions::regionsDelete()
 * @used-by Df_PromoGift_Model_Resource_Indexer::deleteGiftsForProduct()
 * @used-by Df_PromoGift_Model_Resource_Indexer::deleteGiftsForRule()
 * @used-by Df_PromoGift_Model_Resource_Indexer::deleteGiftsForWebsite()
 * @used-by Df_Reward_Setup_1_0_0::_process()
 * @used-by Df_YandexMarket_Setup_2_42_1::_process()
 * @param string $table
 * @param string $columnName
 * @param int|string|int[]|string[] $values
 * @param bool $not [optional]
 * @return void
 */
function df_table_delete($table, $columnName, $values, $not = false) {
	/** @var string $condition */
	$condition = df_sql_predicate_simple($values, $not);
	df_conn()->delete(df_table($table), array("{$columnName} {$condition}" => $values));
}

/**
 * 2015-04-12
 * @used-by Df_Catalog_Model_Processor_DeleteOrphanCategoryAttributesData::_process()
 * @param string $table
 * @param string $columnName
 * @param int|string|int[]|string[] $values
 * @return void
 */
function df_table_delete_not($table, $columnName, $values) {
	df_table_delete($table, $columnName, $values, $not = true);
}

/**
 * 2015-02-10
 * Не используем метод @see Varien_Db_Adapter_Pdo_Mysql::dropTable() по следующим причинам:
 * 1) Он отсутствует в Magento CE 1.4.0.1.
 * 2) Он использует @see Varien_Db_Adapter_Pdo_Mysql::query() вместо
 * @uses Varien_Db_Adapter_Pdo_Mysql::raw_query().
 * 3) Он не вызывает @uses Varien_Db_Adapter_Pdo_Mysql::resetDdlCache(),
 * а вручную мы можем забыть вызвать.
 * Обратите внимание, что имя таблицы должно быть уже в формате MySQL (например, «core_resource»).
 * Если у Вас имя таблицы — в формате Magento (например, «core/resource»),
 * то Вы должны предварительно перевести это имя в формат MySQL посредством вызова @see df_table().
 * @param string $table
 * @param Varien_Db_Adapter_Pdo_Mysql|Varien_Db_Adapter_Interface|null $adapter [optional]
 * @return void
 */
function df_table_drop($table, $adapter = null) {
	$adapter = $adapter ? $adapter : df_conn();
	$adapter->raw_query('drop table if exists ' . df_db_quote($table));
	/**
	 * Обратите внимание, что изменение структуры базы данных может привести к тому,
	 * что кэш слоя бизнес-логики также окажется устаревшим.
	 * Поэтому после изменения структуры базы данных
	 * во многих случаях недостаточно ограничиться только удалением кэша структуры базы данных,
	 * а также может потребоваться и удалить другие виды кэша.
	 * Используйте для этого функцию @see rm_cache_clean().
	 */
	$adapter->resetDdlCache();
}

/**
 * Метод @uses Varien_Db_Adapter_Pdo_Mysql::truncateTable() появился только в Magento CE 1.6.0.0,
 * при этом метод @uses Varien_Db_Adapter_Pdo_Mysql::truncate() стал устаревшим.
 * @param string $table
 * @param Varien_Db_Adapter_Pdo_Mysql|Varien_Db_Adapter_Interface|null $adapter [optional]
 * @return void
 */
function df_table_truncate($table, $adapter = null) {
	Df_Core_Helper_Db::s()->truncate($table, $adapter);
}