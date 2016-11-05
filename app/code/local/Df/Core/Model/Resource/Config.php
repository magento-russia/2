<?php
/**
 * Этот класс, в отличие от родительского класса @see Df_Core_Model_Resource_Config
 * не перекрывает системные методы, а добавляет новую функциональность.
 * Намеренно поэтому разделил методы на два класса:
 * @see Df_Core_Model_Resource_ConfigM и этот,
 * чтобы разделить методы-переопределения и новую функциональность.
 */
class Df_Core_Model_Resource_Config extends Df_Core_Model_Resource_ConfigM {
	/**
	 * @param string $path
	 * @param string $value
	 * @param string|null $valueOriginal [optional]
	 * @param bool $useLikeOperator [optional]
	 */
	public function updateByPath($path, $value, $valueOriginal = null, $useLikeOperator = false) {
		/** @var array(string => string)|string $where */
		$where = array('? = path' => $path);
		if (!is_null($valueOriginal)) {
			if (df_strings_are_equal_ci($valueOriginal, 'null')) {
				$where['value is null'] = null;
			}
			else {
				if ($useLikeOperator) {
					$where['value like ?'] = $valueOriginal;
				}
				else {
					$where['? = value'] = $valueOriginal;
				}
			}
		}
		if (df_strings_are_equal_ci($value, 'null')) {
			$value = null;
		}
		$this->_getWriteAdapter()->update($this->getMainTable(), array('value' => $value), $where);
	}

	/**
	 * @param string $path
	 * @param string $value
	 * @param string|null $valueOriginal [optional]
	 * @param bool $useLikeOperator [optional]
	 */
	public function updateByPathLowLevel(
		$path, $value, $valueOriginal = null, $useLikeOperator = false
	) {
		/** @var string $where */
		$where = strtr(
			$useLikeOperator
			? "value like '{old value}'"
			: "value = '{old value}'"
			, array('{old value}' => $valueOriginal)
		);
		/** @var string $query */
		$query = strtr(
			"UPDATE {table} SET value = {new_value} WHERE ({where}) and path = '{path}';"
			, array(
				'{table}' => df_table('core/config_data')
				,'{path}' => $path
				,'{new_value}' =>
					df_strings_are_equal_ci($value, 'null')
					? 'null'
					: df_quote_single($value)
				,'{where}' => $where
			)
		);
		try {
			df_conn()->query($query);
		}
		catch (Exception $e) {
			Mage::logException($e);
			df_notify_exception($e, $query);
		}
	}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}