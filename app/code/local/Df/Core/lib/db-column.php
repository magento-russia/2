<?php
/**
 * 2016-11-04
 * @param string $table
 * @param string $name
 * @param string $definition [optional]
 * @return void
 */
function df_db_column_add($table, $name, $definition = 'varchar(255) default null') {
	// 2016-11-04
	// df_table нужно вызывать обязательно!
	df_conn()->addColumn(df_table($table), $name, $definition);
	/**
	 * 2016-11-04
	 * @see Varien_Db_Adapter_Pdo_Mysql::resetDdlCache() здесь вызывать не надо,
	 * потому что этот метод вызывается из @uses Varien_Db_Adapter_Pdo_Mysql::addColumn()
	 * https://github.com/OpenMage/magento-mirror/blob/1.4.0.0/lib/Varien/Db/Adapter/Pdo/Mysql.php#L630
	 */
}

/**
 * 2016-11-04
 * При отсутствии колонки функция ничего не делает.
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
 * Возвращает массив вида:
	{
		"SCHEMA_NAME": null,
		"TABLE_NAME": "customer_group",
		"COLUMN_NAME": "test_7781",
		"COLUMN_POSITION": 11,
		"DATA_TYPE": "varchar",
		"DEFAULT": null,
		"NULLABLE": true,
		"LENGTH": "255",
		"SCALE": null,
		"PRECISION": null,
		"UNSIGNED": null,
		"PRIMARY": false,
		"PRIMARY_POSITION": null,
		"IDENTITY": false
	}
 * @param string $table
 * @param string $column
 * @return array(string => string|int|null)
 */
function df_db_column_describe($table, $column) {
	/** @var array(string => string|int|null) $result */
	$result = dfa(df_conn()->describeTable(df_table($table)), $column);
	df_result_array($result);
	return $result;
}

/**
 * 2016-11-04
 * К сожалению, MySQL не позволяет переименовывать колонку
 * без указания при этом её полного описания: http://stackoverflow.com/questions/8553130
 * В ядре Magento также нет такого метода (причем как в Magento 1.x, так и в Magento 2).
 * Поэтому в нашей функции мы сначала получаем описание колонки,
 * а потом передаём его же при переименовании.
 * @param string $table
 * @param string $from  Колонка должна присутствовать!
 * @param string $to
 * @return void
 */
function df_db_column_rename($table, $from, $to) {
	// 2016-11-04
	// df_table нужно вызывать обязательно!
	$table = df_table($table);
	/** @var array(string => string|int|null) $definitionRaw */
	$definitionRaw = df_db_column_describe($table, $from);
	/**
	 * 2016-11-04
	 * Метод @uses Varien_Db_Adapter_Pdo_Mysql::getColumnCreateByDescribe()
	 * появился только в Magento 1.6.1.0 (вышла в октябре 2011 года):
	 * https://github.com/OpenMage/magento-mirror/blob/1.6.1.0/lib/Varien/Db/Adapter/Pdo/Mysql.php#L1590
	 * @var array(string => string|int|null) $definition
	 *
	 * Получаем массив вида:
		{
			"name": "test_7781",
			"type": "text",
			"length": "255",
			"options": [],
			"comment": "Test 7781"
		}
	 */
	$definition = df_conn()->getColumnCreateByDescribe($definitionRaw);
	/**
	 * 2016-11-04
	 * Метод @uses Varien_Db_Adapter_Pdo_Mysql::getColumnCreateByDescribe()
	 * в качестве комментария устанавливает имя таблицы, что нам не нужно:
	 * https://github.com/OpenMage/magento-mirror/blob/1.9.3.0/lib/Varien/Db/Adapter/Pdo/Mysql.php#L1750
	 */
	unset($definition['comment']);
	df_conn()->changeColumn($table, $from, $to, $definition);
	/**
	 * 2016-11-04
	 * @see Varien_Db_Adapter_Pdo_Mysql::resetDdlCache() здесь вызывать не надо,
	 * потому что этот метод вызывается из @uses Varien_Db_Adapter_Pdo_Mysql::changeColumn()
	 */
}