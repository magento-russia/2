<?php
/**
 * 2016-11-04
 * @param string $table
 * @param string $name
 * @param string $definition [optional]
 * @return void
 */
function df_db_column_add($table, $name, $definition = 'varchar(255) default null') {
	$table = df_table($table);
	df_conn()->query("alter table {$table} add column `{$name}` {$definition};");
	df_conn()->resetDdlCache($table);
}

/**
 * 2016-11-04
 * @param string $table
 * @param string $column
 * @return void
 */
function df_db_column_drop($table, $column) {
	// 2016-11-04
	// df_table нужно вызывать обязательно!
	df_conn()->dropColumn(df_table($table), $column);
	/**
	 * 2016-11-04
	 * @see Varien_Db_Adapter_Pdo_Mysql::resetDdlCache() здесь вызывать не надо,
	 * потому что этот метод вызывается из @uses Varien_Db_Adapter_Pdo_Mysql::dropColumn()
	 * https://github.com/OpenMage/magento-mirror/blob/1.4.0.0/lib/Varien/Db/Adapter/Pdo/Mysql.php#L662
	 */
}

/**
 * 2016-11-01
 * http://stackoverflow.com/a/7264865
 *
 * 2016-11-04
 * Раньше (пока не знал о методе ядра) реализация была такой:
	$table = df_table($table);
	$query = df_db_quote_into("SHOW COLUMNS FROM `{$table}` LIKE ?", $column);
	return !!df_conn()->query($query)->fetchColumn();
 *
 * @param string $table
 * @param string $column
 * @return bool
 */
function df_db_column_exists($table, $column) {return
	// 2016-11-04
	// df_table нужно вызывать обязательно!
	df_conn()->tableColumnExists(df_table($table), $column)
;}

/**
 * 2016-11-04
 * @param string $table
 * @param string $from
 * @param string $to
 * @return void
 */
function df_db_column_rename($table, $from, $to) {
	$table = df_table($table);
	df_conn()->resetDdlCache($table);
}