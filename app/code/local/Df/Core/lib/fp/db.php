<?php
/** @return Varien_Db_Adapter_Pdo_Mysql|Varien_Db_Adapter_Interface */
function rm_conn() {
	/** @var Varien_Db_Adapter_Pdo_Mysql|Varien_Db_Adapter_Interface $result */
	static $result;
	if (!isset($result)) {
		$result = df_mage()->core()->resource()->getConnection('write');
	}
	return $result;
}

/**
 * @param string $text
 * @param mixed $value
 * @param string|null $type [optional]
 * @param int|null $count [optional]
 * @return string
 */
function rm_quote_into($text, $value, $type = null, $count = null) {
	return rm_conn()->quoteInto($text, $value, $type, $count);
}

/**
 * @see Mage_Core_Model_Resource::getTableName() не кэширует результаты своей работы,
 * и, глядя на реализацию Mage_Core_Model_Resource_Setup::getTable(),
 * которая выполняет кэширование для @see Mage_Core_Model_Resource::getTableName(),
 * я решил сделать аналогичную функцию, только доступную в произвольном контексте.
 * @param string|string[] $name
 * @return string
 */
function rm_table($name) {
	if (is_array($name)) {
		/** @var string $message */
		$message =
			sprintf(
				'Метод rm_table вызван с параметром-массивом.'
				."\nТакой вызов не поддеживается в Magento CE 1.4."
				. "\nРоссийская сборка Magento должна поддеживать эту версию Magento CE,"
				. ' поэтому в качестве параметра rm_table используйте только строку.'
				. "\nПараметр-массив:%s."
				,print_r($name, $return = true)
			);
		;
		df_notify_me($message);
		df_error($message);
	}
	/** @var array(string => string) $cache */
	static $cache = array();
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