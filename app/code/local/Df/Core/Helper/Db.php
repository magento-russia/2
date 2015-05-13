<?php
class Df_Core_Helper_Db extends Mage_Core_Helper_Abstract {
	/**
	 * Метод @see Varien_Db_Adapter_Pdo_Mysql::getCheckSql()
	 * отсутствует в Magento CE 1.4, поэтому скопировал его сюда.
	 * @param Zend_Db_Expr|Zend_Db_Select|string $expression
	 * @param string $true  true value
	 * @param string $false false value
	 * @return Zend_Db_Expr
	 */
	public function getCheckSql($expression, $true, $false) {
		if ($expression instanceof Zend_Db_Expr || $expression instanceof Zend_Db_Select) {
			$expression = rm_sprintf("IF((%s), %s, %s)", $expression, $true, $false);
		} else {
			$expression = rm_sprintf("IF(%s, %s, %s)", $expression, $true, $false);
		}
		return new Zend_Db_Expr($expression);
	}

	/**
	 * @param Varien_Db_Adapter_Pdo_Mysql|Varien_Db_Adapter_Interface $adapter
	 * @param string $table
	 * @return Df_Core_Helper_Db
	 */
	public function truncate($adapter, $table) {
		/** @var bool $truncated */
		$truncated = false;
		/** @var string $method */
		$method = '';
		/**
		 * Метод Varien_Db_Adapter_Pdo_Mysql::truncateTable
		 * появился только в Magento CE 1.6.0.0,
		 * и при этом метод Varien_Db_Adapter_Pdo_Mysql::truncate стал устаревшим.
		 */
		/** @var string[] $methods */
		$methods = array('truncateTable', 'truncate');
		foreach ($methods as $currentMethod) {
			/** @var string $currentMethod */
			if (
				/**
				 * К сожалению, нельзя здесь для проверки публичности метода
				 * использовать is_callable,
				 * потому что наличие Varien_Object::__call
				 * приводит к тому, что is_callable всегда возвращает true.
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
			catch(Exception $e) {
				/**
				 * При выполнении профилей импорта-экспорта одним из клиентов
				 * произошёл сбой «DDL statements are not allowed in transactions»
				 */
			}
		}
		if (!$truncated) {
			$adapter->delete($table);
		}
		return $this;
	}

	/** @return Df_Core_Helper_Db */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}