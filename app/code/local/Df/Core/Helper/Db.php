<?php
class Df_Core_Helper_Db extends Mage_Core_Helper_Abstract {
	/**
	 * Метод @see Varien_Db_Adapter_Pdo_Mysql::getCheckSql()
	 * отсутствует в Magento CE 1.4, поэтому скопировал его сюда.
	 * @param Zend_Db_Expr|Zend_Db_Select|string $expression
	 * @param string $true
	 * @param string $false
	 * @return Zend_Db_Expr
	 */
	public function getCheckSql($expression, $true, $false) {
		/** @var string $expressionS */
		$expressionS = (string)$expression;
		if ($expression instanceof Zend_Db_Expr || $expression instanceof Zend_Db_Select) {
			$expressionS = '(' . $expressionS . ')';
		}
		return new Zend_Db_Expr(sprintf('IF(%s, %s, %s)', $expressionS, $true, $false));
	}

	/**
	 * Метод @uses Varien_Db_Adapter_Pdo_Mysql::truncateTable() появился только в Magento CE 1.6.0.0,
	 * при этом метод @uses Varien_Db_Adapter_Pdo_Mysql::truncate() стал устаревшим.
	 * @param string $table
	 * @param Varien_Db_Adapter_Pdo_Mysql|Varien_Db_Adapter_Interface|null $adapter [optional]
	 * @return void
	 */
	public function truncate($table, $adapter = null) {
		$adapter = $adapter ? $adapter : rm_conn();
		/** @var bool $truncated */
		$truncated = false;
		/** @var string $method */
		$method = '';
		/** @var string[] $methods */
		$methods = array('truncateTable', 'truncate');
		foreach ($methods as $currentMethod) {
			/** @var string $currentMethod */
			if (
				/**
				 * К сожалению, нельзя здесь для проверки публичности метода использовать @see is_callable(),
				 * потому что наличие @see Varien_Object::__call()
				 * приводит к тому, что @see is_callable всегда возвращает true.
				 * Обратите внимание, что @uses method_exists(), в отличие от @see is_callable(),
				 * не гарантирует публичную доступность метода:
				 * т.е. метод может у класса быть, но вызывать его всё равно извне класса нельзя,
				 * потому что он имеет доступность private или protected.
				 * Пока эта проблема никак не решена.
				 */
				/**
				 * @uses Varien_Db_Adapter_Pdo_Mysql::truncateTable()
				 * @uses Varien_Db_Adapter_Pdo_Mysql::truncate()
				 */
				method_exists($adapter, $currentMethod)
			) {
				$method = $currentMethod;
				break;
			}
		}
		if ($method) {
			try {
				call_user_func(array($adapter, $method), $table);
				$truncated = true;
			}
			catch (Exception $e) {
				/**
				 * При выполнении профилей импорта-экспорта одним из клиентов
				 * произошёл сбой «DDL statements are not allowed in transactions»
				 */
			}
		}
		if (!$truncated) {
			$adapter->delete($table);
		}
	}

	/** @return Df_Core_Helper_Db */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}