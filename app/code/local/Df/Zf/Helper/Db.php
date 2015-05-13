<?php
class Df_Zf_Helper_Db extends Mage_Core_Helper_Abstract {
	/**
	 * @static
	 * @param Zend_Db_Expr|string|null $value
	 * @return bool
	 */
	public static function isNull($value) {
		/** @var bool $result */
		$result = is_null($value) || ('' === $value);
		if (!$result) {
			if (!is_string($value)) {
				df_assert($value instanceof Zend_Db_Expr);
				$value = $value->__toString();
			}
			/** @var string $value */
			df_assert_string($value);
			/** @var bool $result */
			$result = (Df_Zf_Const::NULL_UC === mb_strtoupper($value));
		}
		return $result;
	}

	const _CLASS = __CLASS__;
	/** @return Df_Zf_Helper_Db */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}